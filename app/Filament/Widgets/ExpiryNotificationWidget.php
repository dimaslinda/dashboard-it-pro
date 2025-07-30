<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Website;
use Filament\Actions\Action;
use Filament\Widgets\Widget;
use App\Services\FontteService;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Artisan;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ExpiryNotificationWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.expiry-notification-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public function getViewData(): array
    {
        $today = now();
        $in3Days = $today->copy()->addDays(3);
        $in7Days = $today->copy()->addDays(7);
        $in30Days = $today->copy()->addDays(30);

        return [
            'expiringToday' => [
                'domains' => Website::domainExpiringIn(0)->get(),
                'hosting' => Website::hostingExpiringIn(0)->get(),
            ],
            'expiringIn3Days' => [
                'domains' => Website::domainExpiringIn(3)->get(),
                'hosting' => Website::hostingExpiringIn(3)->get(),
            ],
            'expiringIn7Days' => [
                'domains' => Website::domainExpiringIn(7)->get(),
                'hosting' => Website::hostingExpiringIn(7)->get(),
            ],
            'expiringIn30Days' => [
                'domains' => Website::where('domain_expiry', '<=', $in30Days)
                    ->where('domain_expiry', '>', $today)
                    ->where('status', '!=', 'expired')
                    ->get(),
                'hosting' => Website::where('hosting_expiry', '<=', $in30Days)
                    ->where('hosting_expiry', '>', $today)
                    ->where('status', '!=', 'expired')
                    ->get(),
            ],
        ];
    }

    public function testNotificationAction(): Action
    {
        return Action::make('testNotification')
            ->label('Test Notifikasi')
            ->icon('heroicon-o-bell')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Test Notifikasi WhatsApp')
            ->modalDescription('Apakah Anda yakin ingin mengirim test notifikasi untuk domain dan hosting yang akan expired dalam 30 hari?')
            ->action(function () {
                try {
                    Artisan::call('expiry:check', ['--days' => 30]);
                    $output = Artisan::output();

                    if (str_contains($output, 'WhatsApp notification target not configured')) {
                        Notification::make()
                            ->title('Konfigurasi Belum Lengkap')
                            ->body('Silakan konfigurasi FONTTE_TOKEN dan FONTTE_NOTIFICATION_TARGET di file .env')
                            ->warning()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Test Notifikasi Berhasil')
                            ->body('Command expiry check telah dijalankan. Cek log untuk detail.')
                            ->success()
                            ->send();
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body('Gagal menjalankan test notifikasi: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public function markDomainAsPaidAction(): Action
    {
        return Action::make('markDomainAsPaid')
            ->label('Domain Sudah Bayar')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pembayaran Domain')
            ->modalDescription('Apakah Anda yakin ingin menandai domain ini sebagai sudah dibayar? Tanggal expiry akan diperpanjang ke tahun berikutnya.')
            ->modalSubmitActionLabel('Ya, Sudah Bayar')
            ->form([
                \Filament\Forms\Components\Select::make('website_id')
                    ->label('Pilih Website/Domain')
                    ->options(function () {
                        $today = now();
                        $in30Days = $today->copy()->addDays(30);

                        return Website::where('domain_expiry', '<=', $in30Days)
                            ->where('status', '!=', 'expired')
                            ->get()
                            ->mapWithKeys(function ($website) {
                                $expiryDate = $website->domain_expiry ? $website->domain_expiry->format('d M Y') : 'No Date';
                                return [$website->id => "{$website->name} - Domain Expired: {$expiryDate}"];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Pilih domain yang sudah dibayar'),

                \Filament\Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('Masukkan jumlah pembayaran domain')
                    ->required(),

                FileUpload::make('invoice')
                    ->label('Upload Invoice/Bukti Pembayaran')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                    ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
                    ->helperText('Upload bukti pembayaran domain (PDF, JPG, PNG - Max 1MB)')
                    ->required()
            ])
            ->action(function (array $data) {
                try {
                    $website = Website::findOrFail($data['website_id']);

                    // Create invoice record
                    $amount = $data['amount'] ?? 0;
                    $invoice = \App\Models\Invoice::create([
                        'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                        'client_name' => $website->domain,
                        'client_email' => '',
                        'service_type' => 'domain',
                        'description' => "Perpanjangan domain {$website->domain}",
                        'amount' => $amount,
                        'tax_amount' => 0,
                        'total_amount' => $amount,
                        'invoice_date' => now(),
                        'due_date' => now(),
                        'paid_date' => now(),
                        'status' => 'paid',
                        'payment_method' => 'manual',
                        'reference_type' => 'website',
                        'reference_id' => $website->id,
                    ]);

                    // Handle file upload to invoice
                    if (isset($data['invoice']) && !empty($data['invoice'])) {
                        $invoice->clearMediaCollection('invoices');
                        $invoice->addMedia(storage_path('app/public/' . $data['invoice']))
                            ->toMediaCollection('invoices');
                    }

                    // Get current domain expiry date or today if null
                    $currentExpiry = $website->domain_expiry ?
                        Carbon::parse($website->domain_expiry) :
                        now();

                    // Add one year to the current expiry date
                    $newExpiryDate = $currentExpiry->addYear();

                    // Update the domain expiry date
                    $website->update([
                        'domain_expiry' => $newExpiryDate
                    ]);

                    // Send WhatsApp notification
                    $fontteService = app(FontteService::class);
                    $target = env('FONTTE_NOTIFICATION_TARGET');
                    if ($target) {
                        $fontteService->sendDomainPaymentNotification($target, $website, $amount);
                    }

                    Notification::make()
                        ->title('Pembayaran Domain Berhasil Dicatat!')
                        ->body("Domain {$website->name} telah diperpanjang hingga {$newExpiryDate->format('d M Y')}")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body('Gagal memperbarui tanggal expiry domain: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public function markHostingAsPaidAction(): Action
    {
        return Action::make('markHostingAsPaid')
            ->label('Hosting Sudah Bayar')
            ->icon('heroicon-o-check-circle')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pembayaran Hosting')
            ->modalDescription('Apakah Anda yakin ingin menandai hosting ini sebagai sudah dibayar? Tanggal expiry akan diperpanjang ke tahun berikutnya.')
            ->modalSubmitActionLabel('Ya, Sudah Bayar')
            ->form([
                \Filament\Forms\Components\Select::make('website_id')
                    ->label('Pilih Website/Hosting')
                    ->options(function () {
                        $today = now();
                        $in30Days = $today->copy()->addDays(30);

                        return Website::where('hosting_expiry', '<=', $in30Days)
                            ->where('status', '!=', 'expired')
                            ->get()
                            ->mapWithKeys(function ($website) {
                                $expiryDate = $website->hosting_expiry ? $website->hosting_expiry->format('d M Y') : 'No Date';
                                return [$website->id => "{$website->name} - Hosting Expired: {$expiryDate}"];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Pilih hosting yang sudah dibayar'),

                \Filament\Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->placeholder('Masukkan jumlah pembayaran hosting')
                    ->required(),

                \Filament\Forms\Components\FileUpload::make('invoice')
                    ->label('Upload Invoice/Bukti Pembayaran')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                    ->maxSize(1024) // 1MB - sesuai dengan PHP upload limit
                    ->helperText('Upload bukti pembayaran hosting (PDF, JPG, PNG - Max 1MB)')
                    ->required()
            ])
            ->action(function (array $data) {
                try {
                    $website = Website::findOrFail($data['website_id']);

                    // Create invoice record
                    $invoice = \App\Models\Invoice::create([
                        'invoice_number' => \App\Models\Invoice::generateInvoiceNumber(),
                        'client_name' => $website->name,
                        'client_email' => '',
                        'service_type' => 'hosting',
                        'description' => "Perpanjangan hosting {$website->name}",
                        'amount' => $data['amount'] ?? 0,
                        'tax_amount' => 0,
                        'total_amount' => $data['amount'] ?? 0,
                        'invoice_date' => now(),
                        'due_date' => now(),
                        'paid_date' => now(),
                        'status' => 'paid',
                        'payment_method' => 'manual',
                        'reference_type' => 'website',
                        'reference_id' => $website->id,
                    ]);

                    // Handle file upload to invoice
                    if (isset($data['invoice']) && !empty($data['invoice'])) {
                        $invoice->clearMediaCollection('invoices');
                        $invoice->addMedia(storage_path('app/public/' . $data['invoice']))
                            ->toMediaCollection('invoices');
                    }

                    // Get current hosting expiry date or today if null
                    $currentExpiry = $website->hosting_expiry ?
                        Carbon::parse($website->hosting_expiry) :
                        now();

                    // Add one year to the current expiry date
                    $newExpiryDate = $currentExpiry->addYear();

                    // Update the hosting expiry date
                    $website->update([
                        'hosting_expiry' => $newExpiryDate
                    ]);

                    // Send WhatsApp notification
                    $fontteService = app(FontteService::class);
                    $target = env('FONTTE_NOTIFICATION_TARGET');
                    if ($target) {
                        $fontteService->sendHostingPaymentNotification($target, $website, $data['amount'] ?? 0);
                    }

                    Notification::make()
                        ->title('Pembayaran Hosting Berhasil Dicatat!')
                        ->body("Hosting {$website->name} telah diperpanjang hingga {$newExpiryDate->format('d M Y')}")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body('Gagal memperbarui tanggal expiry hosting: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
