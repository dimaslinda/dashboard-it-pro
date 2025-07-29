<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WifiNetwork;
use App\Services\FontteService;
use Carbon\Carbon;

class CheckWifiExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'wifi:expiry-check {--days=3 : Days before expiry to send notification}';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring WiFi networks, send WhatsApp notifications';

    private FontteService $fontteService;

    public function __construct(FontteService $fontteService)
    {
        parent::__construct();
        $this->fontteService = $fontteService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = now()->addDays($days);
        $whatsappTarget = config('services.fontte.notification_target');

        if (!$whatsappTarget) {
            $this->error('WhatsApp notification target not configured!');
            return 1;
        }

        $this->info("Checking for WiFi networks expiring in {$days} days...");

        // Check WiFi expiry
        $expiringWifi = WifiNetwork::where('service_expiry_date', '<=', $targetDate)
            ->where('service_expiry_date', '>=', now())
            ->where('status', 'active')
            ->with('provider')
            ->get();

        foreach ($expiringWifi as $wifi) {
            $daysLeft = now()->diffInDays($wifi->service_expiry_date, false);
            
            $this->info("Sending WiFi expiry notification for: {$wifi->name}");
            
            $result = $this->fontteService->sendWifiExpiryNotification(
                $whatsappTarget,
                $wifi,
                $daysLeft
            );

            if ($result['success']) {
                $this->info("✅ WiFi notification sent for {$wifi->name}");
            } else {
                $this->error("❌ Failed to send WiFi notification for {$wifi->name}: {$result['error']}");
            }
        }

        $totalNotifications = $expiringWifi->count();
        
        if ($totalNotifications > 0) {
            $this->info("\n📱 Total WiFi notifications sent: {$totalNotifications}");
        } else {
            $this->info("\n✅ No expiring WiFi networks found for the next {$days} days.");
        }

        return 0;
    }
}