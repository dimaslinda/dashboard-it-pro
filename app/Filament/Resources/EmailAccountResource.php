<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailAccountResource\Pages;
use App\Filament\Resources\EmailAccountResource\RelationManagers;
use App\Models\EmailAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class EmailAccountResource extends Resource
{
    protected static ?string $model = EmailAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    
    protected static ?string $navigationGroup = 'Manajemen IT';
    
    protected static ?string $modelLabel = 'Akun Email';
    
    protected static ?string $pluralModelLabel = 'Akun Email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Dasar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->required()
                                    ->revealable()
                                    ->maxLength(255),
                            ]),
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('provider')
                                    ->label('Provider')
                                    ->placeholder('Gmail, Outlook, dll.')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('department')
                                    ->label('Departemen')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('assigned_to')
                                    ->label('Ditugaskan Kepada')
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                                'suspended' => 'Ditangguhkan',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
                    
                Section::make('Konfigurasi Server')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('smtp_server')
                                    ->label('Server SMTP')
                                    ->placeholder('smtp.gmail.com')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('smtp_port')
                                    ->label('Port SMTP')
                                    ->numeric()
                                    ->placeholder('587')
                                    ->minValue(1)
                                    ->maxValue(65535),
                            ]),
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('imap_server')
                                    ->label('Server IMAP')
                                    ->placeholder('imap.gmail.com')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('imap_port')
                                    ->label('Port IMAP')
                                    ->numeric()
                                    ->placeholder('993')
                                    ->minValue(1)
                                    ->maxValue(65535),
                            ]),
                        Forms\Components\Toggle::make('ssl_enabled')
                            ->label('SSL/TLS Diaktifkan')
                            ->default(true),
                    ])
                    ->collapsible(),
                    
                Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->maxLength(65535),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('provider')
                    ->label('Provider')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('department')
                    ->label('Departemen')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assigned_to')
                    ->label('Ditugaskan Kepada')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ssl_enabled')
                    ->label('SSL')
                    ->boolean()
                    ->trueIcon('heroicon-o-shield-check')
                    ->falseIcon('heroicon-o-shield-exclamation')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'warning',
                        'suspended' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                        default => $state,
                    }),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'suspended' => 'Ditangguhkan',
                    ]),
                SelectFilter::make('provider')
                    ->label('Provider')
                    ->options(fn (): array => EmailAccount::distinct('provider')
                        ->whereNotNull('provider')
                        ->pluck('provider', 'provider')
                        ->toArray()),
                Tables\Filters\Filter::make('ssl_enabled')
                    ->label('SSL Diaktifkan')
                    ->query(fn (Builder $query): Builder => $query->where('ssl_enabled', true)),
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
            'index' => Pages\ListEmailAccounts::route('/'),
            'create' => Pages\CreateEmailAccount::route('/create'),
            'edit' => Pages\EditEmailAccount::route('/{record}/edit'),
        ];
    }
}
