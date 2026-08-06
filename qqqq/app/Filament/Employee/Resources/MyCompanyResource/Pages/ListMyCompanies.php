<?php

namespace App\Filament\Employee\Resources\MyCompanyResource\Pages;

use App\Filament\Employee\Resources\MyCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyCompanies extends ListRecords
{
    protected static string $resource = MyCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
