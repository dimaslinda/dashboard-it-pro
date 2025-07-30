<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            
            Actions\Action::make('mark_as_paid')
                ->label('Mark as Paid')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'paid')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->markAsPaid();
                    
                    Notification::make()
                        ->title('Invoice marked as paid')
                        ->success()
                        ->send();
                        
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Invoice updated')
            ->body('The invoice has been updated successfully.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Auto-calculate total if not set
        if (!isset($data['total_amount']) || $data['total_amount'] == 0) {
            $data['total_amount'] = ($data['amount'] ?? 0) + ($data['tax_amount'] ?? 0);
        }

        return $data;
    }
}