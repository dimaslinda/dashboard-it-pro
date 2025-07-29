<?php

namespace App\Filament\Resources\WebsiteResource\Pages;

use App\Filament\Resources\WebsiteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateWebsite extends CreateRecord
{
    protected static string $resource = WebsiteResource::class;
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Website Created')
            ->body('Website has been created successfully.')
            ->icon('heroicon-o-globe-alt')
            ->iconColor('success');
    }
}
