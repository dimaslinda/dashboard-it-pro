<?php

namespace App\Filament\Resources\WifiNetworkResource\Pages;

use App\Filament\Resources\WifiNetworkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListWifiNetworks extends ListRecords
{
    protected static string $resource = WifiNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('WiFi Network Created')
                        ->body('WiFi network has been created successfully.')
                        ->icon('heroicon-o-wifi')
                        ->iconColor('success')
                ),
        ];
    }
}
