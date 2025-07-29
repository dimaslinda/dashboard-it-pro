<?php

namespace App\Filament\Resources\EmailAccountResource\Pages;

use App\Filament\Resources\EmailAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListEmailAccounts extends ListRecords
{
    protected static string $resource = EmailAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Email Account Created')
                        ->body('Email account has been created successfully.')
                        ->icon('heroicon-o-envelope')
                        ->iconColor('success')
                ),
        ];
    }
}
