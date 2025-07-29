<?php

namespace App\Filament\Resources\WifiNetworkResource\Pages;

use App\Filament\Resources\WifiNetworkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWifiNetwork extends CreateRecord
{
    protected static string $resource = WifiNetworkResource::class;
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('WiFi Network Created')
            ->body('WiFi network has been created successfully.')
            ->icon('heroicon-o-wifi')
            ->iconColor('success');
    }
}
