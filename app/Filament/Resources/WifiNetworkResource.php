<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WifiNetworkResource\Pages;
use App\Filament\Resources\WifiNetworkResource\RelationManagers;
use App\Models\WifiNetwork;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WifiNetworkResource extends Resource
{
    protected static ?string $model = WifiNetwork::class;

    protected static ?string $navigationIcon = 'heroicon-o-wifi';

    protected static ?string $navigationLabel = 'WiFi Networks';

    protected static ?string $modelLabel = 'WiFi Network';

    protected static ?string $pluralModelLabel = 'WiFi Networks';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi WiFi')
                    ->schema([
                        Forms\Components\TextInput::make('ssid')
                            ->label('SSID')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('security_type')
                            ->label('Tipe Keamanan')
                            ->options([
                                'WPA2' => 'WPA2',
                                'WPA3' => 'WPA3',
                                'WEP' => 'WEP',
                                'Open' => 'Open'
                            ])
                            ->default('WPA2')
                            ->required(),
                        
                        Forms\Components\Select::make('frequency_band')
                            ->label('Frekuensi Band')
                            ->options([
                                '2.4GHz' => '2.4 GHz',
                                '5GHz' => '5 GHz',
                                'Dual' => 'Dual Band'
                            ])
                            ->default('2.4GHz')
                            ->required(),
                        
                        Forms\Components\TextInput::make('channel')
                            ->label('Channel')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(14),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Maintenance'
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Lokasi & Router')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('router_brand')
                            ->label('Merk Router')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('router_model')
                            ->label('Model Router')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('router_ip')
                            ->label('IP Router')
                            ->maxLength(45),
                        
                        Forms\Components\TextInput::make('router_admin_username')
                            ->label('Username Admin Router')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('router_admin_password')
                            ->label('Password Admin Router')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Konfigurasi Lanjutan')
                    ->schema([
                        Forms\Components\TextInput::make('max_devices')
                            ->label('Maksimal Device')
                            ->numeric()
                            ->minValue(1),
                        
                        Forms\Components\Toggle::make('guest_network')
                            ->label('Guest Network')
                            ->default(false),
                        
                        Forms\Components\TextInput::make('guest_ssid')
                            ->label('Guest SSID')
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('guest_network')),
                        
                        Forms\Components\TextInput::make('guest_password')
                            ->label('Guest Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255)
                            ->visible(fn (Forms\Get $get) => $get('guest_network')),
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
                Tables\Columns\TextColumn::make('ssid')
                    ->label('SSID')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('security_type')
                    ->label('Keamanan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'WPA3' => 'success',
                        'WPA2' => 'info',
                        'WEP' => 'warning',
                        'Open' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('frequency_band')
                    ->label('Band')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('channel')
                    ->label('Channel')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('router_brand')
                    ->label('Router')
                    ->formatStateUsing(fn ($record) => $record->router_brand . ' ' . $record->router_model)
                    ->searchable(['router_brand', 'router_model'])
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('router_ip')
                    ->label('IP Router')
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('guest_network')
                    ->label('Guest')
                    ->boolean()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'maintenance',
                        'danger' => 'inactive',
                    ]),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance'
                    ]),
                
                Tables\Filters\SelectFilter::make('security_type')
                    ->label('Tipe Keamanan')
                    ->options([
                        'WPA2' => 'WPA2',
                        'WPA3' => 'WPA3',
                        'WEP' => 'WEP',
                        'Open' => 'Open'
                    ]),
                
                Tables\Filters\SelectFilter::make('frequency_band')
                    ->label('Frekuensi Band')
                    ->options([
                        '2.4GHz' => '2.4 GHz',
                        '5GHz' => '5 GHz',
                        'Dual' => 'Dual Band'
                    ]),
                
                Tables\Filters\TernaryFilter::make('guest_network')
                    ->label('Guest Network'),
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
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListWifiNetworks::route('/'),
            'create' => Pages\CreateWifiNetwork::route('/create'),
            'edit' => Pages\EditWifiNetwork::route('/{record}/edit'),
        ];
    }
}
