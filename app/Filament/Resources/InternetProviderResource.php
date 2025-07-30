<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InternetProviderResource\Pages;
use App\Filament\Resources\InternetProviderResource\RelationManagers;
use App\Models\InternetProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InternetProviderResource extends Resource
{
    protected static ?string $model = InternetProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-signal';

    protected static ?string $navigationGroup = 'Manajemen Jaringan';

    protected static ?string $modelLabel = 'Penyedia Internet';

    protected static ?string $pluralModelLabel = 'Penyedia Internet';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Provider')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Telepon Kontak')
                            ->tel()
                            ->maxLength(20),
                        
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email Kontak')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                    ])
                    ->columns(2),


                Forms\Components\Section::make('Informasi Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->maxLength(1000)
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('contact_phone')
                    ->label('Telepon Kontak')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('contact_email')
                    ->label('Email Kontak')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('website')
                    ->label('Website')
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        default => ucfirst($state)
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('provider_contracts_count')
                    ->counts('providerContracts')
                    ->label('Kontrak')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('wifi_networks_count')
                    ->counts('wifiNetworks')
                    ->label('Jaringan WiFi')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                    ]),
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
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ProviderContractsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInternetProviders::route('/'),
            'create' => Pages\CreateInternetProvider::route('/create'),
            'edit' => Pages\EditInternetProvider::route('/{record}/edit'),
        ];
    }
}
