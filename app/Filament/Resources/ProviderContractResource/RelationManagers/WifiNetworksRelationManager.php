<?php

namespace App\Filament\Resources\ProviderContractResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WifiNetworksRelationManager extends RelationManager
{
    protected static string $relationship = 'wifiNetworks';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ssid')
                    ->label('SSID')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\TextInput::make('password')
                    ->label('Kata Sandi')
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
                        'Open' => 'Terbuka'
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
                
                Forms\Components\TextInput::make('location')
                    ->label('Lokasi')
                    ->maxLength(255),
                
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'maintenance' => 'Pemeliharaan'
                    ])
                    ->default('active')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ssid')
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
                    })
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'Open' => 'Terbuka',
                        default => $state
                    }),
                
                Tables\Columns\TextColumn::make('frequency_band')
                    ->label('Band')
                    ->badge(),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'maintenance',
                        'danger' => 'inactive',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'maintenance' => 'Pemeliharaan',
                        default => ucfirst($state)
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'maintenance' => 'Pemeliharaan',
                    ]),
                
                Tables\Filters\SelectFilter::make('security_type')
                    ->label('Tipe Keamanan')
                    ->options([
                        'WPA2' => 'WPA2',
                        'WPA3' => 'WPA3',
                        'WEP' => 'WEP',
                        'Open' => 'Terbuka',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}