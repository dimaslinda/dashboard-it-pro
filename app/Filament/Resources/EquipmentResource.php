<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?string $navigationLabel = 'Equipment';

    protected static ?string $modelLabel = 'Equipment';

    protected static ?string $pluralModelLabel = 'Equipment';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Perangkat')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe Perangkat')
                            ->options([
                                'cctv' => 'CCTV',
                                'router' => 'Router',
                                'switch' => 'Switch',
                                'firewall' => 'Firewall',
                                'server' => 'Server',
                                'printer' => 'Printer',
                                'ups' => 'UPS',
                                'other' => 'Lainnya'
                            ])
                            ->required(),
                        
                        Forms\Components\TextInput::make('brand')
                            ->label('Merk')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('model')
                            ->label('Model')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('serial_number')
                            ->label('Serial Number')
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                'maintenance' => 'Maintenance',
                                'broken' => 'Rusak',
                                'retired' => 'Pensiun'
                            ])
                            ->default('active')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Jaringan & Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('mac_address')
                            ->label('MAC Address')
                            ->maxLength(17),
                        
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->maxLength(45),
                        
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('admin_username')
                            ->label('Username Admin')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('admin_password')
                            ->label('Password Admin')
                            ->password()
                            ->revealable()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('firmware_version')
                            ->label('Versi Firmware')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Pembelian & Garansi')
                    ->schema([
                        Forms\Components\DatePicker::make('purchase_date')
                            ->label('Tanggal Pembelian'),
                        
                        Forms\Components\TextInput::make('purchase_price')
                            ->label('Harga Pembelian')
                            ->numeric()
                            ->prefix('Rp'),
                        
                        Forms\Components\TextInput::make('vendor')
                            ->label('Vendor')
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('warranty_start')
                            ->label('Mulai Garansi'),
                        
                        Forms\Components\DatePicker::make('warranty_end')
                            ->label('Akhir Garansi'),
                        
                        Forms\Components\DatePicker::make('last_maintenance')
                            ->label('Maintenance Terakhir'),
                        
                        Forms\Components\DatePicker::make('next_maintenance')
                            ->label('Maintenance Berikutnya'),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Spesifikasi & Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('specifications')
                            ->label('Spesifikasi')
                            ->rows(3)
                            ->columnSpanFull(),
                        
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
                    ->label('Nama Perangkat')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'cctv',
                        'success' => 'router',
                        'info' => 'switch',
                        'warning' => 'firewall',
                        'danger' => 'server',
                        'gray' => ['printer', 'ups', 'other'],
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cctv' => 'CCTV',
                        'router' => 'Router',
                        'switch' => 'Switch',
                        'firewall' => 'Firewall',
                        'server' => 'Server',
                        'printer' => 'Printer',
                        'ups' => 'UPS',
                        'other' => 'Lainnya',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('brand')
                    ->label('Merk')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('model')
                    ->label('Model')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('location')
                    ->label('Lokasi')
                    ->searchable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('warranty_end')
                    ->label('Garansi Berakhir')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->warranty_end && $record->warranty_end->isPast() ? 'danger' : 'success')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('next_maintenance')
                    ->label('Maintenance Berikutnya')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->next_maintenance && $record->next_maintenance->isPast() ? 'warning' : 'success')
                    ->toggleable(),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'maintenance',
                        'danger' => ['inactive', 'broken'],
                        'gray' => 'retired',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance',
                        'broken' => 'Rusak',
                        'retired' => 'Pensiun',
                        default => $state,
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe Perangkat')
                    ->options([
                        'cctv' => 'CCTV',
                        'router' => 'Router',
                        'switch' => 'Switch',
                        'firewall' => 'Firewall',
                        'server' => 'Server',
                        'printer' => 'Printer',
                        'ups' => 'UPS',
                        'other' => 'Lainnya'
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'maintenance' => 'Maintenance',
                        'broken' => 'Rusak',
                        'retired' => 'Pensiun'
                    ]),
                
                Tables\Filters\Filter::make('warranty_expiring')
                    ->label('Garansi Akan Habis')
                    ->query(fn (Builder $query): Builder => $query->where('warranty_end', '<=', now()->addDays(30))),
                
                Tables\Filters\Filter::make('maintenance_due')
                    ->label('Perlu Maintenance')
                    ->query(fn (Builder $query): Builder => $query->where('next_maintenance', '<=', now())),
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
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }
}
