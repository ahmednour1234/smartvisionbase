<?php

namespace App\Filament\Employee\Resources\MyCompanyResource\Pages;

use App\Filament\Employee\Resources\MyCompanyResource;
use Filament\Resources\Pages\EditRecord;

class EditMyCompany extends EditRecord
{
    protected static string $resource = MyCompanyResource::class;
}
