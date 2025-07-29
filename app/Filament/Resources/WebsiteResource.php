<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WebsiteResource\Pages;
use App\Filament\Resources\WebsiteResource\RelationManagers;
use App\Models\Website;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebsiteResource extends Resource
{
    protected static ?string $model = Website::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'Websites';

    protected static ?string $modelLabel = 'Website';

    protected static ?string $pluralModelLabel = 'Websites';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Website')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Website')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('domain')
                            ->label('Domain')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Maintenance',
                                'expired' => 'Expired'
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Hosting & Domain')
                    ->schema([
                        Forms\Components\TextInput::make('hosting_provider')
                            ->label('Provider Hosting')
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('hosting_expiry')
                            ->label('Tanggal Expired Hosting'),
                        
                        Forms\Components\TextInput::make('registrar')
                            ->label('Registrar Domain')
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('domain_expiry')
                            ->label('Tanggal Expired Domain'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Kredensial Admin')
                    ->schema([
                        Forms\Components\TextInput::make('admin_username')
                            ->label('Username Admin')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Password Admin')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                        

                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('FTP Details')
                    ->schema([
                        Forms\Components\TextInput::make('ftp_host')
                            ->label('FTP Host')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ftp_username')
                            ->label('FTP Username')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ftp_password')
                            ->label('FTP Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('ftp_port')
                            ->label('FTP Port')
                            ->numeric()
                            ->default(21),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Database')
                    ->schema([
                        Forms\Components\TextInput::make('database_host')
                            ->label('Database Host')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('database_name')
                            ->label('Database Name')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('database_username')
                            ->label('Database Username')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('database_password')
                            ->label('Database Password')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
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
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Website')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->searchable()
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab(),
                
                Tables\Columns\TextColumn::make('domain')
                    ->label('Domain')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('hosting_provider')
                    ->label('Provider Hosting')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('hosting_expiry')
                    ->label('Expired Hosting')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->hosting_expiry && $record->hosting_expiry->isPast() ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('domain_expiry')
                    ->label('Expired Domain')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->domain_expiry && $record->domain_expiry->isPast() ? 'danger' : 'success'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'maintenance',
                        'danger' => ['inactive', 'expired'],
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
                        'maintenance' => 'Maintenance',
                        'expired' => 'Expired'
                    ]),
                
                Tables\Filters\SelectFilter::make('hosting_provider')
                    ->label('Provider Hosting')
                    ->options([
                        'HostGator' => 'HostGator',
                        'SiteGround' => 'SiteGround',
                        'Bluehost' => 'Bluehost',
                        'DigitalOcean' => 'DigitalOcean',
                        'AWS' => 'AWS',
                        'GoDaddy' => 'GoDaddy',
                        'Namecheap' => 'Namecheap',
                    ]),
                
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Akan Expired')
                    ->query(fn (Builder $query): Builder => $query->where(function ($query) {
                        $query->where('hosting_expiry', '<=', now()->addDays(30))
                              ->orWhere('domain_expiry', '<=', now()->addDays(30));
                    })),
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
            'index' => Pages\ListWebsites::route('/'),
            'create' => Pages\CreateWebsite::route('/create'),
            'edit' => Pages\EditWebsite::route('/{record}/edit'),
        ];
    }
}
