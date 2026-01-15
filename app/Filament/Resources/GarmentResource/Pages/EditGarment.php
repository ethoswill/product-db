<?php

namespace App\Filament\Resources\GarmentResource\Pages;

use App\Filament\Resources\GarmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGarment extends EditRecord
{
    protected static string $resource = GarmentResource::class;


    protected function afterSave(): void
    {
        // Redirect to view page after saving
        $this->redirect(GarmentResource::getUrl('view', ['record' => $this->record]));
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
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

    protected function getFormActions(): array
    {
        return parent::getFormActions();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ensure variants are set to empty array
        if (!isset($data['variants'])) {
            $data['variants'] = [];
        }

        // Ensure measurements are set to empty array
        if (!isset($data['measurements'])) {
            $data['measurements'] = [];
        }

        // Ensure cubic_dimensions are set to null
        if (!isset($data['cubic_dimensions'])) {
            $data['cubic_dimensions'] = null;
        }
        
        return $data;
    }
}
