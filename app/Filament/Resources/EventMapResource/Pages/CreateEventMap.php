<?php

namespace App\Filament\Resources\EventMapResource\Pages;

use App\Filament\Resources\EventMapResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEventMap extends CreateRecord
{
    protected static string $resource = EventMapResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (request()->filled('event_id')) {
            $data['event_id'] = request()->integer('event_id');
        }

        if (request()->filled('booth')) {
            $data['booth'] = request()->integer('booth');
        }

        return $data;
    }
}