<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Website;
use App\Services\FontteService;
use Carbon\Carbon;

class CheckExpiryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'expiry:check {--days=3 : Days before expiry to send notification}';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring domains and hosting, send WhatsApp notifications';

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
        $whatsappTarget = env('FONTTE_NOTIFICATION_TARGET');

        if (!$whatsappTarget) {
            $this->error('WhatsApp notification target not configured!');
            return 1;
        }

        $this->info("Checking for domains and hosting expiring in {$days} days...");

        // Check domain expiry
        $expiringDomains = Website::domainExpiringIn($days)->get();

        foreach ($expiringDomains as $website) {
            $daysLeft = $website->getDaysUntilDomainExpiry();
            
            $this->info("Sending domain expiry notification for: {$website->name}");
            
            $result = $this->fontteService->sendDomainExpiryNotification(
                $whatsappTarget,
                $website,
                $daysLeft
            );

            if ($result['success']) {
                $this->info("✅ Domain notification sent for {$website->name}");
            } else {
                $this->error("❌ Failed to send domain notification for {$website->name}: {$result['error']}");
            }
        }

        // Check hosting expiry
        $expiringHosting = Website::hostingExpiringIn($days)->get();

        foreach ($expiringHosting as $website) {
            $daysLeft = $website->getDaysUntilHostingExpiry();
            
            $this->info("Sending hosting expiry notification for: {$website->name}");
            
            $result = $this->fontteService->sendHostingExpiryNotification(
                $whatsappTarget,
                $website,
                $daysLeft
            );

            if ($result['success']) {
                $this->info("✅ Hosting notification sent for {$website->name}");
            } else {
                $this->error("❌ Failed to send hosting notification for {$website->name}: {$result['error']}");
            }
        }

        $totalNotifications = $expiringDomains->count() + $expiringHosting->count();
        
        if ($totalNotifications > 0) {
            $this->info("\n📱 Total notifications sent: {$totalNotifications}");
        } else {
            $this->info("\n✅ No expiring domains or hosting found for the next {$days} days.");
        }

        return 0;
    }
}