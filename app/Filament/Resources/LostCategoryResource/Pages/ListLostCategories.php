<?php

namespace App\Filament\Resources\LostCategoryResource\Pages;

use App\Filament\Resources\LostCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLostCategories extends ListRecords
{
    protected static string $resource = LostCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
