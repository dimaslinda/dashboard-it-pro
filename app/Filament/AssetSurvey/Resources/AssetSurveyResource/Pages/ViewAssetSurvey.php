<?php

namespace App\Filament\AssetSurvey\Resources\AssetSurveyResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAssetSurvey extends ViewRecord
{
    protected static string $resource = AssetSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}