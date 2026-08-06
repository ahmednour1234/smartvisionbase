<?php

namespace App\Filament\Employee\Resources\MyMeetingResource\Pages;

use App\Filament\Employee\Resources\MyMeetingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyMeetings extends ListRecords
{
    protected static string $resource = MyMeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
