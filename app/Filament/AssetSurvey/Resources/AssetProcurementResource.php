<?php

namespace App\Filament\AssetSurvey\Resources;

use App\Filament\AssetSurvey\Resources\AssetProcurementResource\Pages;
use App\Models\Asset;
use App\Models\AssetProcurement;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;

class AssetProcurementResource extends Resource
{
    protected static ?string $model = AssetProcurement::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Asset Procurement';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Asset Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Request Information')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->label('Related Asset (Optional)')
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Asset $record): string => "{$record->name} ({$record->asset_code})")
                            ->helperText('Select if this procurement is to replace an existing damaged asset'),
                        Forms\Components\TextInput::make('requester_name')
                            ->label('Requester Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('requester_position')
                            ->label('Requester Position')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('request_date')
                            ->label('Request Date')
                            ->required()
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make('Item Details')
                    ->schema([
                        Forms\Components\TextInput::make('item_name')
                            ->label('Item Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('item_specification')
                            ->label('Item Specification')
                            ->rows(3)
                            ->helperText('Detailed specifications, model, brand, etc.'),
                        Forms\Components\TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->reactive(),
                        Forms\Components\TextInput::make('unit_price')
                            ->label('Unit Price (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                                $quantity = $get('quantity') ?? 1;
                                if ($state) {
                                    $set('total_price', $quantity * $state);
                                }
                            }),
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total Price (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled()
                            ->dehydrated(),
                    ])->columns(2),

                Forms\Components\Section::make('Supplier Information')
                    ->schema([
                        Forms\Components\TextInput::make('supplier_name')
                            ->label('Supplier Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('supplier_contact')
                            ->label('Supplier Contact')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('purchase_order_number')
                            ->label('Purchase Order Number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Invoice Number')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Request Details')
                    ->schema([
                        Forms\Components\Textarea::make('justification')
                            ->label('Justification')
                            ->required()
                            ->rows(3)
                            ->helperText('Explain why this procurement is needed'),
                        Forms\Components\Select::make('urgency_level')
                            ->label('Urgency Level')
                            ->required()
                            ->options([
                                'low' => 'Low',
                                'medium' => 'Medium',
                                'high' => 'High',
                                'urgent' => 'Urgent',
                            ])
                            ->default('medium'),
                        Forms\Components\TextInput::make('budget_source')
                            ->label('Budget Source')
                            ->maxLength(255)
                            ->helperText('Department budget, project budget, etc.'),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'ordered' => 'Ordered',
                                'delivered' => 'Delivered',
                                'completed' => 'Completed',
                            ])
                            ->default('pending'),
                    ])->columns(2),

                Forms\Components\Section::make('Delivery Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('expected_delivery_date')
                            ->label('Expected Delivery Date'),
                        Forms\Components\DatePicker::make('actual_delivery_date')
                            ->label('Actual Delivery Date')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('status', 'delivered');
                                }
                            }),
                        Forms\Components\Select::make('condition_received')
                            ->label('Condition When Received')
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('actual_delivery_date')),
                        Forms\Components\Textarea::make('delivery_notes')
                            ->label('Delivery Notes')
                            ->rows(2)
                            ->visible(fn (Forms\Get $get) => $get('actual_delivery_date')),
                    ])->columns(2),

                Forms\Components\Section::make('Warranty Information')
                    ->schema([
                        Forms\Components\TextInput::make('warranty_period')
                            ->label('Warranty Period')
                            ->maxLength(255)
                            ->helperText('e.g., 1 year, 6 months, etc.'),
                        Forms\Components\DatePicker::make('warranty_expiry_date')
                            ->label('Warranty Expiry Date'),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['delivered', 'completed'])),

                Forms\Components\Section::make('Approval')
                    ->schema([
                        Forms\Components\Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('approval_date')
                            ->label('Approval Date'),
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Approval Notes')
                            ->rows(2),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['approved', 'rejected', 'ordered', 'delivered', 'completed'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('requester_name')
                    ->label('Requester')
                    ->searchable(),
                Tables\Columns\TextColumn::make('request_date')
                    ->label('Request Date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total Price')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('urgency_level')
                    ->label('Urgency')
                    ->colors([
                        'success' => 'low',
                        'warning' => 'medium',
                        'danger' => ['high', 'urgent'],
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => ['approved', 'completed'],
                        'primary' => ['ordered', 'delivered'],
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'ordered' => 'Ordered',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('expected_delivery_date')
                    ->label('Expected Delivery')
                    ->date()
                    ->placeholder('Not set')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('actual_delivery_date')
                    ->label('Actual Delivery')
                    ->date()
                    ->placeholder('Not delivered')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('supplier_name')
                    ->label('Supplier')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'ordered' => 'Ordered',
                        'delivered' => 'Delivered',
                        'completed' => 'Completed',
                    ]),
                SelectFilter::make('urgency_level')
                    ->label('Urgency')
                    ->options([
                        'low' => 'Low',
                        'medium' => 'Medium',
                        'high' => 'High',
                        'urgent' => 'Urgent',
                    ]),
                Tables\Filters\Filter::make('pending_approval')
                    ->label('Pending Approval')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'pending')),
                Tables\Filters\Filter::make('overdue_delivery')
                    ->label('Overdue Delivery')
                    ->query(fn (Builder $query): Builder => 
                        $query->where('status', 'ordered')
                              ->where('expected_delivery_date', '<', now())
                              ->whereNull('actual_delivery_date')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AssetProcurement $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Approval Notes')
                            ->rows(2),
                    ])
                    ->action(function (AssetProcurement $record, array $data): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approval_date' => now(),
                            'approval_notes' => $data['approval_notes'] ?? null,
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Procurement Approved')
                            ->body('Procurement request has been approved successfully.')
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (AssetProcurement $record): bool => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('approval_notes')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (AssetProcurement $record, array $data): void {
                        $record->update([
                            'status' => 'rejected',
                            'approved_by' => Auth::id(),
                            'approval_date' => now(),
                            'approval_notes' => $data['approval_notes'],
                        ]);

                        Notification::make()
                            ->warning()
                            ->title('Procurement Rejected')
                            ->body('Procurement request has been rejected.')
                            ->send();
                    }),
                Action::make('mark_delivered')
                    ->label('Mark as Delivered')
                    ->icon('heroicon-o-truck')
                    ->color('primary')
                    ->visible(fn (AssetProcurement $record): bool => $record->status === 'ordered')
                    ->form([
                        Forms\Components\DatePicker::make('actual_delivery_date')
                            ->label('Delivery Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('condition_received')
                            ->label('Condition When Received')
                            ->required()
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                            ]),
                        Forms\Components\Textarea::make('delivery_notes')
                            ->label('Delivery Notes')
                            ->rows(2),
                    ])
                    ->action(function (AssetProcurement $record, array $data): void {
                        $record->update([
                            'actual_delivery_date' => $data['actual_delivery_date'],
                            'condition_received' => $data['condition_received'],
                            'delivery_notes' => $data['delivery_notes'] ?? null,
                            'status' => 'delivered',
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Item Delivered')
                            ->body('Item has been marked as delivered successfully.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('request_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssetProcurements::route('/'),
            'create' => Pages\CreateAssetProcurement::route('/create'),
            'edit' => Pages\EditAssetProcurement::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}