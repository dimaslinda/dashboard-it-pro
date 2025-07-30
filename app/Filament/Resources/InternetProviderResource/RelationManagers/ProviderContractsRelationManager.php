<?php

namespace App\Filament\Resources\InternetProviderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProviderContractsRelationManager extends RelationManager
{
    protected static string $relationship = 'providerContracts';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kontrak')
                    ->schema([
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
                            ->required()
                            ->default('active'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Layanan & Harga')
                    ->schema([
                        Forms\Components\TextInput::make('speed_package')
                            ->label('Paket Kecepatan')
                            ->maxLength(100),
                        
                        Forms\Components\TextInput::make('bandwidth_mbps')
                            ->label('Bandwidth (Mbps)')
                            ->numeric()
                            ->suffix('Mbps'),
                        
                        Forms\Components\Select::make('connection_type')
                            ->label('Tipe Koneksi')
                            ->options([
                                'fiber' => 'Fiber',
                                'cable' => 'Kabel',
                                'dsl' => 'DSL',
                                'wireless' => 'Nirkabel',
                                'satellite' => 'Satelit',
                            ]),
                        
                        Forms\Components\TextInput::make('monthly_cost')
                            ->label('Biaya Bulanan')
                            ->numeric()
                            ->prefix('IDR')
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('installation_cost')
                            ->label('Biaya Instalasi')
                            ->numeric()
                            ->prefix('IDR')
                            ->step(0.01),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Tanggal Kontrak')
                    ->schema([
                        Forms\Components\DatePicker::make('contract_start_date')
                            ->label('Tanggal Mulai Kontrak')
                            ->required(),
                        
                        Forms\Components\TextInput::make('contract_duration_months')
                            ->label('Durasi Kontrak (Bulan)')
                            ->numeric()
                            ->suffix('bulan'),
                        
                        Forms\Components\DatePicker::make('service_expiry_date')
                            ->label('Tanggal Berakhir Layanan')
                            ->required(),
                    ])
                    ->columns(3),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->maxLength(1000)
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('company_name')
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Nama Perusahaan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('speed_package')
                    ->label('Paket Kecepatan')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('bandwidth_mbps')
                    ->label('Bandwidth')
                    ->suffix(' Mbps')
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('connection_type')
                    ->label('Koneksi')
                    ->colors([
                        'success' => 'fiber',
                        'primary' => 'cable',
                        'warning' => 'dsl',
                        'info' => 'wireless',
                        'secondary' => 'satellite',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'fiber' => 'Fiber',
                        'cable' => 'Kabel',
                        'dsl' => 'DSL',
                        'wireless' => 'Nirkabel',
                        'satellite' => 'Satelit',
                        default => ucfirst($state)
                    }),
                
                Tables\Columns\TextColumn::make('monthly_cost')
                    ->label('Biaya Bulanan')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('contract_start_date')
                    ->label('Tanggal Mulai')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('service_expiry_date')
                    ->label('Tanggal Berakhir')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->isExpired() ? 'danger' : ($record->isExpiringSoon() ? 'warning' : 'success')),
                
                Tables\Columns\BadgeColumn::make('contract_status')
                    ->label('Status')
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
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('contract_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'expired' => 'Kedaluwarsa',
                        'cancelled' => 'Dibatalkan',
                    ]),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Akan Berakhir')
                    ->query(fn (Builder $query): Builder => $query->expiringSoon()),
                
                Tables\Filters\Filter::make('expired')
                    ->label('Kedaluwarsa')
                    ->query(fn (Builder $query): Builder => $query->expired()),
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
            ])
            ->defaultSort('service_expiry_date', 'asc');
    }
}