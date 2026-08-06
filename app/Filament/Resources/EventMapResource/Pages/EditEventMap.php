<?php

namespace App\Filament\Resources\EventMapResource\Pages;

use App\Filament\Resources\EventMapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventMap extends EditRecord
{
    protected static string $resource = EventMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}