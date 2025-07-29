<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Website;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Services\FontteService;
use Illuminate\Support\Facades\Artisan;

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
}