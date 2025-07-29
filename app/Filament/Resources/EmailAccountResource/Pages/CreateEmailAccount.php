<?php

namespace App\Filament\Resources\EmailAccountResource\Pages;

use App\Filament\Resources\EmailAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateEmailAccount extends CreateRecord
{
    protected static string $resource = EmailAccountResource::class;
    
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Email Account Created')
            ->body('Email account has been created successfully.')
            ->icon('heroicon-o-envelope')
            ->iconColor('success');
    }
}
