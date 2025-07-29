<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Equipment Created')
            ->body('Equipment has been created successfully.')
            ->icon('heroicon-o-computer-desktop')
            ->iconColor('success');
    }
}
