<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use App\Filament\Resources\InvoiceResource\Pages\YearlyReport;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Manajemen Keuangan';

    protected static ?string $modelLabel = 'Faktur';

    protected static ?string $pluralModelLabel = 'Faktur';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Faktur')
                    ->schema([
                        Forms\Components\TextInput::make('invoice_number')
                            ->label('Nomor Faktur')
                            ->default(fn () => Invoice::generateInvoiceNumber())
                            ->required()
                            ->unique(Invoice::class, 'invoice_number', ignoreRecord: true)
                            ->maxLength(255),
                        
                        Forms\Components\DatePicker::make('invoice_date')
                            ->label('Tanggal Faktur')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->default(now()->addDays(30))
                            ->required(),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'sent' => 'Terkirim',
                                'paid' => 'Dibayar',
                                'overdue' => 'Terlambat',
                                'cancelled' => 'Dibatalkan',
                            ])
                            ->default('draft')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Informasi Klien')
                    ->schema([
                        Forms\Components\TextInput::make('client_name')
                            ->label('Nama Klien')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('client_email')
                            ->label('Email Klien')
                            ->email()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('client_phone')
                            ->label('Telepon Klien')
                            ->tel()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('client_address')
                            ->label('Alamat Klien')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Layanan')
                    ->schema([
                        Forms\Components\Select::make('service_type')
                            ->label('Jenis Layanan')
                            ->options([
                                'domain' => 'Registrasi/Perpanjangan Domain',
                                'hosting' => 'Web Hosting',
                                'wifi' => 'Layanan WiFi/Internet',
                                'equipment' => 'Pembelian/Sewa Peralatan',
                                'maintenance' => 'Layanan Pemeliharaan',
                                'consultation' => 'Konsultasi IT',
                                'development' => 'Pengembangan Website',
                                'support' => 'Dukungan Teknis',
                                'other' => 'Layanan Lainnya',
                            ])
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Layanan')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                        
                        Forms\Components\TextInput::make('reference_type')
                            ->label('Jenis Referensi')
                            ->placeholder('contoh: website, equipment, wifi_network')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('reference_id')
                            ->label('ID Referensi')
                            ->placeholder('ID dari record terkait')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Keuangan')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah (Sebelum Pajak)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $taxAmount = $get('tax_amount') ?? 0;
                                $set('total_amount', $state + $taxAmount);
                            }),
                        
                        Forms\Components\TextInput::make('tax_amount')
                            ->label('Jumlah Pajak')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $amount = $get('amount') ?? 0;
                                $set('total_amount', $amount + $state);
                            }),
                        
                        Forms\Components\TextInput::make('total_amount')
                            ->label('Total Jumlah')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->readOnly(),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Informasi Pembayaran')
                    ->schema([
                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Tanggal Dibayar'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'bank_transfer' => 'Transfer Bank',
                                'cash' => 'Tunai',
                                'credit_card' => 'Kartu Kredit',
                                'debit_card' => 'Kartu Debit',
                                'e_wallet' => 'E-Wallet',
                                'check' => 'Cek',
                                'other' => 'Lainnya',
                            ]),
                        
                        Forms\Components\Textarea::make('payment_notes')
                            ->label('Catatan Pembayaran')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Catatan Tambahan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('File Invoice')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('invoice_file')
                            ->label('Upload File Invoice')
                            ->collection('invoices')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
                            ->helperText('Maksimal ukuran file: 1MB. Format yang didukung: PDF, JPG, PNG')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('No. Faktur')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('client_name')
                    ->label('Klien')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('service_type')
                    ->label('Layanan')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'domain' => 'info',
                        'hosting' => 'success',
                        'wifi' => 'warning',
                        'equipment' => 'gray',
                        'maintenance' => 'purple',
                        default => 'primary',
                    }),
                
                Tables\Columns\TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('invoice_date')
                    ->label('Tanggal Faktur')
                    ->date()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) => $record->isOverdue() ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('paid_date')
                    ->label('Tanggal Dibayar')
                    ->date()
                    ->sortable()
                    ->placeholder('Belum dibayar'),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'info' => 'sent',
                        'danger' => 'overdue',
                        'gray' => 'cancelled',
                        'warning' => 'draft',
                    ]),
                
                Tables\Columns\TextColumn::make('media_count')
                    ->label('File Invoice')
                    ->counts('media')
                    ->badge()
                    ->color('info')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Terkirim',
                        'paid' => 'Dibayar',
                        'overdue' => 'Terlambat',
                        'cancelled' => 'Dibatalkan',
                    ]),
                
                SelectFilter::make('service_type')
                    ->label('Jenis Layanan')
                    ->options([
                        'domain' => 'Domain',
                        'hosting' => 'Hosting',
                        'wifi' => 'WiFi/Internet',
                        'equipment' => 'Peralatan',
                        'maintenance' => 'Pemeliharaan',
                        'consultation' => 'Konsultasi',
                        'development' => 'Pengembangan',
                        'support' => 'Dukungan',
                        'other' => 'Lainnya',
                    ]),
                
                Filter::make('paid_this_year')
                    ->label('Dibayar Tahun Ini')
                    ->query(fn (Builder $query): Builder => $query->paid()->inYear(date('Y'))),
                
                Filter::make('overdue')
                    ->label('Overdue')
                    ->query(fn (Builder $query): Builder => $query->where('status', '!=', 'paid')->where('due_date', '<', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\EditAction::make(),
                
                Action::make('download_files')
                    ->label('Download File')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('info')
                    ->visible(fn ($record) => $record->media->count() > 0)
                    ->action(function ($record) {
                        $files = $record->media;
                        if ($files->count() === 1) {
                            return redirect($files->first()->getUrl());
                        }
                        
                        // Jika ada multiple files, buka semua dalam tab baru
                        $urls = $files->map(fn($file) => $file->getUrl())->toArray();
                        
                        Notification::make()
                            ->title('Multiple Files Available')
                            ->body('Klik pada file di detail view untuk download individual file.')
                            ->info()
                            ->send();
                    }),
                
                Action::make('mark_as_paid')
                    ->label('Mark as Paid')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'paid')
                    ->form([
                        Forms\Components\DatePicker::make('paid_date')
                            ->label('Paid Date')
                            ->default(now())
                            ->required(),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'bank_transfer' => 'Bank Transfer',
                                'cash' => 'Cash',
                                'credit_card' => 'Credit Card',
                                'debit_card' => 'Debit Card',
                                'e_wallet' => 'E-Wallet',
                                'check' => 'Check',
                                'other' => 'Other',
                            ])
                            ->required(),
                        
                        Forms\Components\Textarea::make('payment_notes')
                            ->label('Payment Notes')
                            ->rows(2),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update([
                            'status' => 'paid',
                            'paid_date' => $data['paid_date'],
                            'payment_method' => $data['payment_method'],
                            'payment_notes' => $data['payment_notes'],
                        ]);
                        
                        Notification::make()
                            ->title('Invoice marked as paid')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('invoice_date', 'desc');
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'yearly-report' => Pages\YearlyReport::route('/yearly-report'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'overdue')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', 'overdue')->count() > 0 ? 'danger' : null;
    }
}