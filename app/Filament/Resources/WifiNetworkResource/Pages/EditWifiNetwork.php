<?php

namespace App\Filament\Resources\WifiNetworkResource\Pages;

use App\Filament\Resources\WifiNetworkResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWifiNetwork extends EditRecord
{
    protected static string $resource = WifiNetworkResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
