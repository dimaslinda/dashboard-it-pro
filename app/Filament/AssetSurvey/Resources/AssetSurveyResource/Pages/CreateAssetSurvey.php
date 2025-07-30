<?php

namespace App\Filament\AssetSurvey\Resources\AssetSurveyResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetSurveyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAssetSurvey extends CreateRecord
{
    protected static string $resource = AssetSurveyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}