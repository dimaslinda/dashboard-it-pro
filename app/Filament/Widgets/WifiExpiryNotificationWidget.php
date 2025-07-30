<?php

namespace App\Filament\Widgets;

use App\Models\WifiNetwork;
use App\Models\InternetProvider;
use App\Models\ProviderContract;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Services\FontteService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Artisan;

class WifiExpiryNotificationWidget extends Widget implements HasForms, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;

    protected static string $view = 'filament.widgets.wifi-expiry-notification-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function getViewData(): array
    {
        $today = now();
        $in7Days = $today->copy()->addDays(7);
        $in30Days = $today->copy()->addDays(30);

        return [
            'expiringToday' => ProviderContract::whereHas('wifiNetworks', function($query) {
                    $query->where('status', 'active');
                })
                ->whereDate('service_expiry_date', $today)
                ->with('provider')
                ->get(),
            'expiringIn7Days' => ProviderContract::whereHas('wifiNetworks', function($query) {
                    $query->where('status', 'active');
                })
                ->whereBetween('service_expiry_date', [$today->copy()->addDay(), $in7Days])
                ->with('provider')
                ->get(),
            'expiringIn30Days' => ProviderContract::whereHas('wifiNetworks', function($query) {
                    $query->where('status', 'active');
                })
                ->whereBetween('service_expiry_date', [$today->copy()->addDay(), $in30Days])
                ->with('provider')
                ->get(),
            'expired' => ProviderContract::whereHas('wifiNetworks', function($query) {
                    $query->where('status', 'active');
                })
                ->where('service_expiry_date', '<', $today)
                ->with('provider')
                ->get(),
        ];
    }

    public function testWifiNotificationAction(): Action
    {
        return Action::make('testWifiNotification')
            ->label('Test Notifikasi WiFi')
            ->icon('heroicon-o-wifi')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('Test Notifikasi WhatsApp WiFi')
            ->modalDescription('Apakah Anda yakin ingin mengirim test notifikasi untuk WiFi yang akan expired dalam 30 hari?')
            ->action(function () {
                try {
                    // Create a test command for contract expiry check
                    $expiringContracts = ProviderContract::whereHas('wifiNetworks', function($query) {
                            $query->where('status', 'active');
                        })
                        ->whereBetween('service_expiry_date', [now(), now()->addDays(30)])
                        ->with('provider')
                        ->get();

                    if ($expiringContracts->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Kontrak yang Akan Expired')
                            ->body('Tidak ada kontrak internet yang akan expired dalam 30 hari ke depan.')
                            ->info()
                            ->send();
                        return;
                    }

                    // Send notification via Fontte
                    $fontte = new FontteService();
                    $message = "🚨 *PERINGATAN EXPIRY KONTRAK INTERNET* 🚨\n\n";
                    $message .= "Kontrak internet yang akan expired dalam 30 hari:\n\n";

                    foreach ($expiringContracts as $contract) {
                        $expiryDate = $contract->service_expiry_date ?? null;
                        if (!$expiryDate) continue;
                        
                        $daysLeft = now()->diffInDays($expiryDate, false);
                        $status = $daysLeft < 0 ? '❌ EXPIRED' : ($daysLeft <= 7 ? '⚠️ KRITIS' : '⏰ PERINGATAN');
                        $message .= "$status *{$contract->provider->name}* - {$contract->company_name}\n";
                        $message .= "📅 Expired: " . $expiryDate->format('d/m/Y') . "\n";
                        $message .= "💰 Biaya: Rp " . number_format($contract->monthly_cost ?? 0, 0, ',', '.') . "/bulan\n\n";
                    }

                    $message .= "Segera lakukan perpanjangan untuk menghindari gangguan layanan!";

                    $result = $fontte->sendMessage(env('FONTTE_NOTIFICATION_TARGET'), $message);

                    if ($result['success']) {
                        Notification::make()
                            ->title('Test Notifikasi WiFi Berhasil')
                            ->body('Notifikasi WiFi expiry telah dikirim via WhatsApp.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal Mengirim Notifikasi')
                            ->body('Error: ' . ($result['message'] ?? 'Unknown error'))
                            ->danger()
                            ->send();
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body('Gagal menjalankan test notifikasi WiFi: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public function markAsPaidAction(): Action
    {
        return Action::make('markAsPaid')
            ->label('Sudah Bayar')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pembayaran')
            ->modalDescription('Apakah Anda yakin ingin menandai kontrak internet ini sebagai sudah dibayar? Tanggal expiry akan diperpanjang ke bulan berikutnya.')
            ->modalSubmitActionLabel('Ya, Sudah Bayar')
            ->form([
                \Filament\Forms\Components\Select::make('contract_id')
                    ->label('Pilih Kontrak Internet')
                    ->options(function () {
                        $today = now();
                        $in30Days = $today->copy()->addDays(30);
                        
                        return ProviderContract::whereHas('wifiNetworks', function($query) {
                                $query->where('status', 'active');
                            })
                            ->where('service_expiry_date', '<=', $in30Days)
                            ->with('provider')
                            ->get()
                            ->mapWithKeys(function ($contract) {
                                $expiryDate = $contract->service_expiry_date ? $contract->service_expiry_date->format('d M Y') : 'No Date';
                                return [$contract->id => "{$contract->provider->name} - {$contract->company_name} - Expired: {$expiryDate}"];
                            });
                    })
                    ->required()
                    ->searchable()
                    ->placeholder('Pilih kontrak yang sudah dibayar')
            ])
            ->action(function (array $data) {
                try {
                    $contract = ProviderContract::with('provider')->findOrFail($data['contract_id']);
                    
                    // Get current expiry date from contract or today if null
                    $currentExpiry = $contract->service_expiry_date ? 
                        Carbon::parse($contract->service_expiry_date) : 
                        now();
                    
                    // Add one month to the current expiry date
                    $newExpiryDate = $currentExpiry->addMonth();
                    
                    // Update the expiry date in contract
                    $contract->update([
                        'service_expiry_date' => $newExpiryDate
                    ]);
                    
                    Notification::make()
                        ->title('Pembayaran Berhasil Dicatat!')
                        ->body("Kontrak {$contract->provider->name} - {$contract->company_name} telah diperpanjang hingga {$newExpiryDate->format('d M Y')}")
                        ->success()
                        ->send();
                        
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body('Gagal memperbarui tanggal expiry: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
