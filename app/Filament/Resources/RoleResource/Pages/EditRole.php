<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Role Deleted')
                        ->body('Role has been deleted successfully.')
                        ->icon('heroicon-o-trash')
                        ->iconColor('danger')
                ),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Role Updated')
            ->body('Role has been updated successfully.')
            ->icon('heroicon-o-pencil')
            ->iconColor('success');
    }
}