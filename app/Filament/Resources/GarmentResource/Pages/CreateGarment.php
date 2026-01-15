<?php

namespace App\Filament\Resources\GarmentResource\Pages;

use App\Filament\Resources\GarmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateGarment extends CreateRecord
{
    protected static string $resource = GarmentResource::class;

    public function getTitle(): string
    {
        return 'Create On Hand Inventory Product';
    }

    protected function afterCreate(): void
    {
        // Redirect to view page after creating
        $this->redirect(GarmentResource::getUrl('view', ['record' => $this->record]));
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

    protected function getHeaderActions(): array
    {
        return [];
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
