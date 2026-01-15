<?php

namespace App\Filament\Resources\GarmentResource\Pages;

use App\Filament\Resources\GarmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewGarment extends ViewRecord
{
    protected static string $resource = GarmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }
}
