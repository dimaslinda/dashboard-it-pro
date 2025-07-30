<?php

namespace App\Filament\AssetSurvey\Resources\AssetLoanResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAssetLoan extends CreateRecord
{
    protected static string $resource = AssetLoanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-set approval if user has permission
        if (Auth::user()->can('approve_loans')) {
            $data['approved_by'] = Auth::id();
            $data['approval_date'] = now();
            $data['status'] = 'approved';
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Loan request created successfully';
    }
}