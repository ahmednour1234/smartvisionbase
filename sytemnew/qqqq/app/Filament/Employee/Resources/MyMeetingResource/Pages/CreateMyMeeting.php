<?php

namespace App\Filament\Employee\Resources\MyMeetingResource\Pages;

use App\Filament\Employee\Resources\MyMeetingResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateMyMeeting extends CreateRecord
{
    protected static string $resource = MyMeetingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }
}
