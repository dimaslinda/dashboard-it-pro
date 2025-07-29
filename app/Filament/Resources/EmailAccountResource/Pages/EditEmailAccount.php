<?php

namespace App\Filament\Resources\EmailAccountResource\Pages;

use App\Filament\Resources\EmailAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditEmailAccount extends EditRecord
{
    protected static string $resource = EmailAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Email Account Deleted')
                        ->body('Email account has been deleted successfully.')
                        ->icon('heroicon-o-trash')
                        ->iconColor('danger')
                ),
        ];
    }
    
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Email Account Updated')
            ->body('Email account has been updated successfully.')
            ->icon('heroicon-o-pencil')
            ->iconColor('success');
    }
}
