<?php

namespace App\Filament\Resources\LeadResource\Pages;

use App\Filament\Resources\LeadResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLead extends CreateRecord
{
    protected static string $resource = LeadResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();
        $data['created_by'] = $user?->id;
        $data['updated_by'] = $user?->id;
        if (empty($data['sales_rep_id'])) {
            $data['sales_rep_id'] = $user?->id;
        }
        return $data;
    }
}
