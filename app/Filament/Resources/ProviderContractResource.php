<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProviderContractResource\Pages;
use App\Filament\Resources\ProviderContractResource\RelationManagers;
use App\Models\ProviderContract;
use App\Models\InternetProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderContractResource extends Resource
{
    protected static ?string $model = ProviderContract::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Kontrak Provider';

    protected static ?string $modelLabel = 'Kontrak Provider';

    protected static ?string $pluralModelLabel = 'Kontrak Provider';

    protected static ?string $navigationGroup = 'Manajemen Jaringan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kontrak')
                    ->schema([
                        Forms\Components\Select::make('provider_id')
                            ->label('Internet Provider')
                            ->relationship('provider', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('contact_phone')
                                    ->tel()
                                    ->maxLength(20),
                                Forms\Components\TextInput::make('contact_email')
                                    ->email()
                                    ->maxLength(255),
                            ]),
                        
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nama Perusahaan')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('contract_status')
                            ->label('Status Kontrak')
                            ->options([
                                'active' => 'Aktif',
                                'expired' => 'Kedaluwarsa',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Layanan & Harga')
                    ->schema([
                        Forms\Components\TextInput::make('speed_package')
                            ->label('Paket Kecepatan')
                            ->placeholder('e.g., 100 Mbps Unlimited')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('bandwidth_mbps')
                            ->label('Bandwidth (Mbps)')
                            ->numeric()
                            ->minValue(1),
                        
                        Forms\Components\Select::make('connection_type')
                            ->label('Connection Type')
                            ->options([
                                'Fiber Optic' => 'Fiber Optic',
                                'Cable' => 'Cable',
                                'DSL' => 'DSL',
                                'Wireless' => 'Wireless',
                                'Satellite' => 'Satellite',
                                'Dedicated Fiber' => 'Dedicated Fiber',
                            ])
                            ->placeholder('Select connection type'),
                        
                        Forms\Components\TextInput::make('monthly_cost')
                            ->label('Biaya Bulanan')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->step(1000)
                            ->required(),
                        
                        Forms\Components\TextInput::make('installation_cost')
                            ->label('Biaya Instalasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->step(1000),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Tanggal Kontrak')
                    ->schema([
                        Forms\Components\DatePicker::make('contract_start_date')
                            ->label('Tanggal Mulai Kontrak')
                            ->displayFormat('d/m/Y')
                            ->native(false),
                        
                        Forms\Components\TextInput::make('contract_duration_months')
                            ->label('Durasi Kontrak (Bulan)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(120)
                            ->suffix('bulan'),
                        
                        Forms\Components\DatePicker::make('service_expiry_date')
                            ->label('Tanggal Berakhir Layanan')
                            ->displayFormat('d/m/Y')
                            ->native(false)
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider.name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('speed_package')
                    ->label('Paket Kecepatan')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('bandwidth_mbps')
                    ->label('Bandwidth')
                    ->suffix(' Mbps')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('connection_type')
                    ->label('Koneksi')
                    ->badge()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('monthly_cost')
                    ->label('Biaya Bulanan')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('contract_start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('service_expiry_date')
                    ->label('Tanggal Berakhir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : ($record->daysUntilExpiry() <= 30 ? 'warning' : 'success')),
                
                Tables\Columns\TextColumn::make('contract_status')
                    ->label('Status')
                    ->badge()
                    ->colors([
                        'success' => 'active',
                        'danger' => 'expired',
                        'warning' => 'cancelled',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => 'Aktif',
                        'expired' => 'Kedaluwarsa', 
                        'cancelled' => 'Dibatalkan',
                        default => ucfirst($state)
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider_id')
                    ->label('Provider')
                    ->relationship('provider', 'name')
                    ->searchable()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('contract_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'expired' => 'Kedaluwarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Akan Berakhir (30 hari)')
                    ->query(fn (Builder $query): Builder => $query->expiringSoon(30)),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Kedaluwarsa')
                    ->query(fn (Builder $query): Builder => $query->expired()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('service_expiry_date', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\WifiNetworksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProviderContracts::route('/'),
            'create' => Pages\CreateProviderContract::route('/create'),
            'edit' => Pages\EditProviderContract::route('/{record}/edit'),
        ];
    }
}
