<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditLead extends EditRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        $data['updated_by'] = $user?->id;
        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
