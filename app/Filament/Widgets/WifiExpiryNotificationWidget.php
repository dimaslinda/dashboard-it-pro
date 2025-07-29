<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\WifiNetwork;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Services\FontteService;
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
            'expiringToday' => WifiNetwork::whereDate('service_expiry_date', $today)
                ->where('status', 'active')
                ->with('provider')
                ->get(),
            'expiringIn7Days' => WifiNetwork::whereBetween('service_expiry_date', [$today->copy()->addDay(), $in7Days])
                ->where('status', 'active')
                ->with('provider')
                ->get(),
            'expiringIn30Days' => WifiNetwork::whereBetween('service_expiry_date', [$today->copy()->addDay(), $in30Days])
                ->where('status', 'active')
                ->with('provider')
                ->get(),
            'expired' => WifiNetwork::where('service_expiry_date', '<', $today)
                ->where('status', 'active')
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
                    // Create a test command for WiFi expiry check
                    $expiringWifi = WifiNetwork::whereBetween('service_expiry_date', [now(), now()->addDays(30)])
                        ->where('status', 'active')
                        ->with('provider')
                        ->get();
                    
                    if ($expiringWifi->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada WiFi yang Akan Expired')
                            ->body('Tidak ada jaringan WiFi yang akan expired dalam 30 hari ke depan.')
                            ->info()
                            ->send();
                        return;
                    }

                    // Send notification via Fontte
                    $fontte = new FontteService();
                    $message = "🚨 *PERINGATAN EXPIRY WiFi NETWORKS* 🚨\n\n";
                    $message .= "Jaringan WiFi yang akan expired dalam 30 hari:\n\n";
                    
                    foreach ($expiringWifi as $wifi) {
                        $daysLeft = now()->diffInDays($wifi->service_expiry_date, false);
                        $status = $daysLeft < 0 ? '❌ EXPIRED' : ($daysLeft <= 7 ? '⚠️ KRITIS' : '⏰ PERINGATAN');
                        $message .= "$status *{$wifi->name}*\n";
                        $message .= "📍 Lokasi: {$wifi->location}\n";
                        $message .= "🏢 Provider: " . ($wifi->provider ? $wifi->provider->name : 'Tidak ada') . "\n";
                        $message .= "📅 Expired: " . $wifi->service_expiry_date->format('d/m/Y') . "\n";
                        $message .= "💰 Biaya: Rp " . number_format($wifi->monthly_cost ?? 0, 0, ',', '.') . "/bulan\n\n";
                    }
                    
                    $message .= "Segera lakukan perpanjangan untuk menghindari gangguan layanan!";
                    
                    $result = $fontte->sendMessage($message);
                    
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
}