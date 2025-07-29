<?php

namespace App\Filament\Resources\InternetProviderResource\Pages;

use App\Filament\Resources\InternetProviderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInternetProvider extends EditRecord
{
    protected static string $resource = InternetProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
