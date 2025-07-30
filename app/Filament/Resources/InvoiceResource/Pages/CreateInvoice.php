<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Invoice created')
            ->body('The invoice has been created successfully.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-calculate total if not set
        if (!isset($data['total_amount']) || $data['total_amount'] == 0) {
            $data['total_amount'] = ($data['amount'] ?? 0) + ($data['tax_amount'] ?? 0);
        }

        return $data;
    }
}