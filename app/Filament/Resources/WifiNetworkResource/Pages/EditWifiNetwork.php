<?php

namespace App\Filament\Resources\WifiNetworkResource\Pages;

use App\Filament\Resources\WifiNetworkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditWifiNetwork extends EditRecord
{
    protected static string $resource = WifiNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('WiFi Network Deleted')
                        ->body('WiFi network has been deleted successfully.')
                        ->icon('heroicon-o-trash')
                        ->iconColor('danger')
                ),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('WiFi Network Updated')
            ->body('WiFi network has been updated successfully.')
            ->icon('heroicon-o-pencil')
            ->iconColor('success');
    }
}
