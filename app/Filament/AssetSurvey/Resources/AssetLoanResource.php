<?php

namespace App\Filament\AssetSurvey\Resources;

use App\Filament\AssetSurvey\Resources\AssetLoanResource\Pages;
use App\Models\Asset;
use App\Models\AssetLoan;
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

class AssetLoanResource extends Resource
{
    protected static ?string $model = AssetLoan::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';

    protected static ?string $navigationLabel = 'Asset Loans';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Asset Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Loan Information')
                    ->schema([
                        Forms\Components\Select::make('asset_id')
                            ->label('Asset')
                            ->required()
                            ->relationship('asset', 'name')
                            ->searchable()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn (Asset $record): string => "{$record->name} ({$record->asset_code})")
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $asset = Asset::find($state);
                                    if ($asset) {
                                        // Check if asset is currently on loan
                                        $currentLoan = AssetLoan::where('asset_id', $state)
                                            ->where('status', 'out')
                                            ->whereNull('actual_return_date')
                                            ->first();
                                        
                                        if ($currentLoan) {
                                            Notification::make()
                                                ->warning()
                                                ->title('Asset Currently On Loan')
                                                ->body("This asset is currently on loan to {$currentLoan->borrower_name} until {$currentLoan->expected_return_date->format('d/m/Y')}")
                                                ->send();
                                        }
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('borrower_name')
                            ->label('Borrower Name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('borrower_position')
                            ->label('Borrower Position')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pic_name')
                            ->label('PIC (Person in Charge)')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pic_contact')
                            ->label('PIC Contact')
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Loan Schedule')
                    ->schema([
                        Forms\Components\DatePicker::make('loan_date')
                            ->label('Loan Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\DatePicker::make('expected_return_date')
                            ->label('Expected Return Date')
                            ->required()
                            ->after('loan_date'),
                        Forms\Components\DatePicker::make('actual_return_date')
                            ->label('Actual Return Date')
                            ->after('loan_date')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if ($state) {
                                    $set('status', 'in');
                                }
                            }),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'out' => 'Out (On Loan)',
                                'in' => 'In (Returned)',
                                'overdue' => 'Overdue',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending'),
                    ])->columns(2),

                Forms\Components\Section::make('Usage Details')
                    ->schema([
                        Forms\Components\Textarea::make('purpose')
                            ->label('Purpose of Loan')
                            ->rows(2),
                        Forms\Components\TextInput::make('location_used')
                            ->label('Location Where Used')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('calibration_count')
                            ->label('Calibration Count')
                            ->numeric()
                            ->default(0)
                            ->helperText('Number of times the asset has been calibrated/reset'),
                        Forms\Components\DatePicker::make('calibration_date')
                            ->label('Last Calibration Date'),
                    ])->columns(2),

                Forms\Components\Section::make('Condition Assessment')
                    ->schema([
                        Forms\Components\Select::make('condition_out')
                            ->label('Condition When Loaned Out')
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                            ]),
                        Forms\Components\Select::make('condition_in')
                            ->label('Condition When Returned')
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                            ])
                            ->visible(fn (Forms\Get $get) => $get('actual_return_date')),
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Approval')
                    ->schema([
                        Forms\Components\Select::make('approved_by')
                            ->label('Approved By')
                            ->relationship('approver', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\DateTimePicker::make('approval_date')
                            ->label('Approval Date'),
                    ])->columns(2)
                    ->visible(fn (Forms\Get $get) => in_array($get('status'), ['approved', 'out', 'in'])),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset.name')
                    ->label('Tools')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset.asset_code')
                    ->label('Good Asset')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('available_units')
                    ->label('Available (unit)')
                    ->getStateUsing(function (AssetLoan $record): string {
                        $totalUnits = $record->asset->total_units ?? 1;
                        $onLoanCount = AssetLoan::where('asset_id', $record->asset_id)
                            ->where('status', 'out')
                            ->count();
                        $available = max(0, $totalUnits - $onLoanCount);
                        return $available;
                    })
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Out')
                    ->colors([
                        'danger' => 'out',
                        'secondary' => fn ($state) => $state !== 'out',
                    ])
                    ->formatStateUsing(fn (string $state): string => $state === 'out' ? '1' : '0')
                    ->alignCenter(),
                Tables\Columns\BadgeColumn::make('return_status')
                    ->label('In')
                    ->colors([
                        'success' => 'in',
                        'secondary' => fn ($state) => $state !== 'in',
                    ])
                    ->getStateUsing(fn (AssetLoan $record): string => $record->status)
                    ->formatStateUsing(fn (string $state): string => $state === 'in' ? '1' : '0')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('calibration_count')
                    ->label('Cal')
                    ->alignCenter()
                    ->tooltip('Calibration Count - Number of times reset'),
                Tables\Columns\TextColumn::make('borrower_name')
                    ->label('Borrower')
                    ->searchable(),
                Tables\Columns\TextColumn::make('pic_info')
                    ->label('PIC')
                    ->getStateUsing(function (AssetLoan $record): string {
                        return $record->pic_name . ' (' . $record->loan_date->format('d/m/Y') . ')';
                    })
                    ->searchable(['pic_name']),
                Tables\Columns\TextColumn::make('expected_return_date')
                    ->label('Expected Return')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('actual_return_date')
                    ->label('Actual Return')
                    ->date('d/m/Y')
                    ->placeholder('Not returned')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => ['approved', 'in'],
                        'primary' => 'out',
                        'danger' => ['overdue', 'rejected'],
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'out' => 'Out',
                        'in' => 'In',
                        'overdue' => 'Overdue',
                        'rejected' => 'Rejected',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('calibration_count')
                    ->label('Cal')
                    ->alignCenter()
                    ->tooltip('Calibration Count'),
                Tables\Columns\TextColumn::make('loan_duration')
                    ->label('Duration (Days)')
                    ->getStateUsing(function (AssetLoan $record): string {
                        $endDate = $record->actual_return_date ?? now();
                        $days = $record->loan_date->diffInDays($endDate);
                        return $days . ' days';
                    })
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'out' => 'Out',
                        'in' => 'In',
                        'overdue' => 'Overdue',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('asset')
                    ->relationship('asset', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('currently_on_loan')
                    ->label('Currently On Loan')
                    ->query(fn (Builder $query): Builder => $query->currentlyOnLoan()),
                Tables\Filters\Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query): Builder => $query->overdue()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('return')
                    ->label('Mark as Returned')
                    ->icon('heroicon-o-arrow-left-circle')
                    ->color('success')
                    ->visible(fn (AssetLoan $record): bool => $record->status === 'out')
                    ->form([
                        Forms\Components\DatePicker::make('actual_return_date')
                            ->label('Return Date')
                            ->required()
                            ->default(now()),
                        Forms\Components\Select::make('condition_in')
                            ->label('Condition When Returned')
                            ->required()
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                            ]),
                        Forms\Components\Textarea::make('return_notes')
                            ->label('Return Notes')
                            ->rows(2),
                    ])
                    ->action(function (AssetLoan $record, array $data): void {
                        $record->update([
                            'actual_return_date' => $data['actual_return_date'],
                            'condition_in' => $data['condition_in'],
                            'status' => 'in',
                            'notes' => $record->notes . "\n\nReturn Notes: " . ($data['return_notes'] ?? ''),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Asset Returned')
                            ->body('Asset has been marked as returned successfully.')
                            ->send();
                    }),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (AssetLoan $record): bool => $record->status === 'pending')
                    ->action(function (AssetLoan $record): void {
                        $record->update([
                            'status' => 'approved',
                            'approved_by' => Auth::id(),
                            'approval_date' => now(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title('Loan Approved')
                            ->body('Asset loan has been approved successfully.')
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('loan_date', 'desc');
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
            'index' => Pages\ListAssetLoans::route('/'),
            'create' => Pages\CreateAssetLoan::route('/create'),
            'edit' => Pages\EditAssetLoan::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'out')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }
}