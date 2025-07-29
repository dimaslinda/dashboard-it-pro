<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Equipment Deleted')
                        ->body('Equipment has been deleted successfully.')
                        ->icon('heroicon-o-trash')
                        ->iconColor('danger')
                ),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Equipment Updated')
            ->body('Equipment has been updated successfully.')
            ->icon('heroicon-o-pencil')
            ->iconColor('success');
    }
}
