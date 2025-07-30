<?php

namespace App\Filament\AssetSurvey\Resources\AssetProcurementResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetProcurementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAssetProcurement extends CreateRecord
{
    protected static string $resource = AssetProcurementResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set approval if user has permission
        if (Auth::user()->can('update_asset::procurement')) {
            $data['approved_by'] = Auth::id();
            $data['approval_date'] = now();
            $data['status'] = 'approved';
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Procurement request created successfully';
    }
}