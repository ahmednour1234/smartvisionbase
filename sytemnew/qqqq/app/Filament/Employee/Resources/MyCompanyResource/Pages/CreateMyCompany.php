<?php

namespace App\Filament\Employee\Resources\MyCompanyResource\Pages;

use App\Filament\Employee\Resources\MyCompanyResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyCompany extends CreateRecord
{
    protected static string $resource = MyCompanyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['owner_id'] = Auth::id();

        return $data;
    }
}
