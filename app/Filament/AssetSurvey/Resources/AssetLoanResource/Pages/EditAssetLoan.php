<?php

namespace App\Filament\AssetSurvey\Resources\AssetLoanResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetLoan extends EditRecord
{
    protected static string $resource = AssetLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Loan updated successfully';
    }
}