<?php

namespace App\Filament\AssetSurvey\Resources\AssetSurveyResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetSurveyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssetSurvey extends EditRecord
{
    protected static string $resource = AssetSurveyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}