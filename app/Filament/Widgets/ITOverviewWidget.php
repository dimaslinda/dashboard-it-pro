<?php

namespace App\Filament\Widgets;

use App\Models\EmailAccount;
use App\Models\Website;
use App\Models\WifiNetwork;
use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ITOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Email Accounts', EmailAccount::count())
                ->description('Active email accounts')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('success'),
            
            Stat::make('Total Websites', Website::count())
                ->description('Managed websites')
                ->descriptionIcon('heroicon-m-globe-alt')
                ->color('info'),
            
            Stat::make('WiFi Networks', WifiNetwork::count())
                ->description('Configured networks')
                ->descriptionIcon('heroicon-m-wifi')
                ->color('warning'),
            
            Stat::make('Equipment', Equipment::count())
                ->description('IT assets')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('primary'),
            
            Stat::make('Expiring Soon', $this->getExpiringSoon())
                ->description('Domains/Hosting expiring in 30 days')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
            
            Stat::make('Maintenance Due', $this->getMaintenanceDue())
                ->description('Equipment needing maintenance')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('warning'),
        ];
    }
    
    private function getExpiringSoon(): int
    {
        return Website::where(function ($query) {
            $query->where('hosting_expiry', '<=', now()->addDays(30))
                  ->orWhere('domain_expiry', '<=', now()->addDays(30));
        })->count();
    }
    
    private function getMaintenanceDue(): int
    {
        return Equipment::where('next_maintenance', '<=', now())->count();
    }
}