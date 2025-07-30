<?php

namespace App\Filament\AssetSurvey\Resources;

use App\Filament\AssetSurvey\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Assets';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        // Show all assets without tenant filtering
        return parent::getEloquentQuery();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->relationship('company', 'name')
                            ->getOptionLabelFromRecordUsing(fn (Company $record): string => "{$record->name} ({$record->code})")
                            ->searchable(['name', 'code'])
                            ->preload()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    $company = Company::find($state);
                                    if ($company) {
                                        // Get the next asset number for this company
                                        $lastAsset = Asset::where('company_id', $state)
                                            ->where('asset_code', 'like', $company->code . '-%')
                                            ->orderBy('asset_code', 'desc')
                                            ->first();
                                        
                                        if ($lastAsset) {
                                            $lastNumber = (int) substr($lastAsset->asset_code, strlen($company->code) + 1);
                                            $nextNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
                                        } else {
                                            $nextNumber = '001';
                                        }
                                        
                                        $set('asset_code', $company->code . '-' . $nextNumber);
                                    }
                                }
                            }),
                        Forms\Components\TextInput::make('asset_code')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Asset code will be auto-generated based on company selection'),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('tool_category')
                            ->label('Tool Category')
                            ->options([
                                'safety' => 'Alat Pelindung Diri/Safety First',
                                'documentation' => 'Alat Dokumentasi',
                                'support' => 'Support Tools',
                                'architecture' => 'Alat Survey Arsitektur',
                                'civil' => 'Alat Sipil',
                                'mep' => 'Alat MEP & Utility',
                            ])
                            ->searchable(),
                        Forms\Components\TextInput::make('type')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('tool_name')
                            ->label('Tool Name (REKANUSA)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('subcategory')
                            ->label('Subcategory (REKANUSA)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('asset_number')
                            ->label('Asset Number (REKANUSA)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('brand')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('model')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('serial_number')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('total_units')
                            ->label('Total Units (REKANUSA)')
                            ->numeric()
                            ->minValue(0),
                    ])->columns(2),

                Forms\Components\Section::make('Location & Status')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('department')
                            ->maxLength(255),
                        Forms\Components\Select::make('condition')
                            ->required()
                            ->options([
                                'excellent' => 'Excellent',
                                'good' => 'Good',
                                'fair' => 'Fair',
                                'poor' => 'Poor',
                                'damaged' => 'Damaged',
                            ])
                            ->default('good'),
                        Forms\Components\Select::make('status')
                            ->required()
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Under Maintenance',
                                'retired' => 'Retired',
                                'disposed' => 'Disposed',
                            ])
                            ->default('active'),
                    ])->columns(2),

                Forms\Components\Section::make('Financial Information')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date'),
                        Forms\Components\TextInput::make('purchase_price')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('current_value')
                            ->numeric()
                            ->prefix('Rp'),
                        Forms\Components\TextInput::make('depreciation_rate')
                            ->numeric()
                            ->suffix('%')
                            ->helperText('Annual depreciation rate'),
                    ])->columns(2),

                Forms\Components\Section::make('Maintenance')
                    ->schema([
                        Forms\Components\DatePicker::make('warranty_expiry'),
                        Forms\Components\DatePicker::make('last_maintenance'),
                        Forms\Components\DatePicker::make('next_maintenance'),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->rows(3),
                        Forms\Components\KeyValue::make('specifications')
                            ->keyLabel('Specification')
                            ->valueLabel('Value'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')
                    ->label('Company')
                    ->formatStateUsing(fn ($record): string => "{$record->company->name} ({$record->company->code})")
                    ->searchable(['companies.name', 'companies.code'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('asset_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tool_category')
                    ->label('Tool Category')
                    ->badge()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('tool_name')
                    ->label('Tool Name')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('asset_number')
                    ->label('Asset Number')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('brand')
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'excellent' => 'success',
                        'good' => 'primary',
                        'fair' => 'warning',
                        'poor' => 'danger',
                        'damaged' => 'danger',
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'maintenance' => 'info',
                        'retired' => 'danger',
                        'disposed' => 'secondary',
                    }),
                Tables\Columns\TextColumn::make('availability_status')
                    ->label('Availability')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Available' => 'success',
                        'On Loan' => 'warning',
                        'Maintenance' => 'info',
                        'Retired' => 'danger',
                        'Disposed' => 'secondary',
                    })
                    ->formatStateUsing(function ($record): string {
                        if ($record->isCurrentlyOnLoan()) {
                            $loan = $record->currentLoan();
                            return 'Out - ' . ($loan ? $loan->borrower_name : 'Unknown');
                        }
                        return $record->availability_status;
                    }),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->money('IDR')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_units')
                    ->label('Total Units')
                    ->numeric()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('next_maintenance')
                    ->date()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'name')
                    ->getOptionLabelFromRecordUsing(fn (Company $record): string => "{$record->name} ({$record->code})"),
                Tables\Filters\SelectFilter::make('tool_category')
                    ->label('Tool Category')
                    ->options([
                        'safety' => 'Alat Pelindung Diri/Safety First',
                        'documentation' => 'Alat Dokumentasi',
                        'support' => 'Support Tools',
                        'architecture' => 'Alat Survey Arsitektur',
                        'civil' => 'Alat Sipil',
                        'mep' => 'Alat MEP & Utility',
                    ]),
                Tables\Filters\SelectFilter::make('condition'),
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\Filter::make('availability')
                    ->label('Availability Status')
                    ->form([
                        Forms\Components\Select::make('availability_status')
                            ->options([
                                'available' => 'Available',
                                'on_loan' => 'On Loan',
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['availability_status'] === 'on_loan',
                            fn (Builder $query): Builder => $query->whereHas('assetLoans', function (Builder $q) {
                                $q->where('status', 'out')->whereNull('actual_return_date');
                            }),
                            fn (Builder $query): Builder => $query->when(
                                $data['availability_status'] === 'available',
                                fn (Builder $query): Builder => $query->whereDoesntHave('assetLoans', function (Builder $q) {
                                    $q->where('status', 'out')->whereNull('actual_return_date');
                                })
                            )
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'view' => Pages\ViewAsset::route('/{record}'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }


}