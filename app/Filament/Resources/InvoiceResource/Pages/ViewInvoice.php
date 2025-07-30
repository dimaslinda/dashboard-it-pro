<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

use Filament\Infolists\Components\SpatieMediaLibraryImageEntry;
use Filament\Support\Colors\Color;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('download_files')
                ->label('Download File')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('info')
                ->visible(fn ($record) => $record->media->count() > 0)
                ->url(fn ($record) => $record->media->first()?->getUrl())
                ->openUrlInNewTab(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Faktur')
                    ->schema([
                        TextEntry::make('invoice_number')
                            ->label('Nomor Faktur')
                            ->weight('bold'),
                        
                        TextEntry::make('invoice_date')
                            ->label('Tanggal Faktur')
                            ->date(),
                        
                        TextEntry::make('due_date')
                            ->label('Jatuh Tempo')
                            ->date()
                            ->color(fn ($record) => $record->isOverdue() ? 'danger' : 'success'),
                        
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'sent' => 'info',
                                'overdue' => 'danger',
                                'cancelled' => 'gray',
                                'draft' => 'warning',
                                default => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'paid' => 'Dibayar',
                                'sent' => 'Terkirim',
                                'overdue' => 'Terlambat',
                                'cancelled' => 'Dibatalkan',
                                'draft' => 'Draft',
                                default => $state,
                            }),
                    ])
                    ->columns(2),
                
                Section::make('Informasi Klien')
                    ->schema([
                        TextEntry::make('client_name')
                            ->label('Nama Klien'),
                        
                        TextEntry::make('client_email')
                            ->label('Email Klien')
                            ->copyable(),
                        
                        TextEntry::make('client_phone')
                            ->label('Telepon Klien')
                            ->copyable(),
                        
                        TextEntry::make('client_address')
                            ->label('Alamat Klien')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                
                Section::make('Detail Layanan')
                    ->schema([
                        TextEntry::make('service_type')
                            ->label('Jenis Layanan')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'domain' => 'info',
                                'hosting' => 'success',
                                'wifi' => 'warning',
                                'equipment' => 'gray',
                                'maintenance' => 'purple',
                                'electric_token' => 'danger',
                                default => 'primary',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'domain' => 'Domain',
                                'hosting' => 'Hosting',
                                'wifi' => 'WiFi/Internet',
                                'equipment' => 'Peralatan',
                                'maintenance' => 'Pemeliharaan',
                                'consultation' => 'Konsultasi',
                                'development' => 'Pengembangan',
                                'support' => 'Dukungan',
                                'electric_token' => 'Token Listrik',
                                'other' => 'Lainnya',
                                default => $state,
                            }),
                        
                        TextEntry::make('description')
                            ->label('Deskripsi')
                            ->columnSpanFull(),
                    ]),
                
                Section::make('Informasi Pembayaran')
                    ->schema([
                        TextEntry::make('amount')
                            ->label('Subtotal')
                            ->money('IDR'),
                        
                        TextEntry::make('tax_amount')
                            ->label('Pajak')
                            ->money('IDR'),
                        
                        TextEntry::make('total_amount')
                            ->label('Total')
                            ->money('IDR')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('paid_date')
                            ->label('Tanggal Dibayar')
                            ->date()
                            ->placeholder('Belum dibayar'),
                        
                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'bank_transfer' => 'Transfer Bank',
                                'cash' => 'Tunai',
                                'credit_card' => 'Kartu Kredit',
                                'debit_card' => 'Kartu Debit',
                                'e_wallet' => 'E-Wallet',
                                'check' => 'Cek',
                                'other' => 'Lainnya',
                                default => $state,
                            }),
                        
                        TextEntry::make('payment_notes')
                            ->label('Catatan Pembayaran')
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
                
                Section::make('File Invoice')
                    ->schema([
                        TextEntry::make('media')
                            ->label('File Invoice/Bukti Pembayaran')
                            ->formatStateUsing(function ($record) {
                                if ($record->media->count() === 0) {
                                    return 'Tidak ada file';
                                }
                                
                                $files = $record->media->map(function ($file) {
                                    $fileName = $file->name . '.' . $file->extension;
                                    $fileSize = number_format($file->size / 1024, 2) . ' KB';
                                    $downloadUrl = $file->getUrl();
                                    
                                    return "<a href='{$downloadUrl}' target='_blank' class='text-blue-600 hover:text-blue-800 underline'>{$fileName}</a> ({$fileSize})";
                                })->join('<br>');
                                
                                return $files;
                            })
                            ->html()
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record) => $record->media->count() > 0),
            ]);
    }
}