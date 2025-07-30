<?php

namespace App\Filament\AssetSurvey\Resources\AssetProcurementResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetProcurement extends EditRecord
{
    protected static string $resource = AssetProcurementResource::class;

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
        return 'Procurement updated successfully';
    }
}