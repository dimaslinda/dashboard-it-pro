<?php

namespace App\Filament\AssetSurvey\Resources\AssetResource\Pages;

use App\Filament\AssetSurvey\Resources\AssetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAsset extends CreateRecord
{
    protected static string $resource = AssetResource::class;


}