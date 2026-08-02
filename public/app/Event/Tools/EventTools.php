<?php

namespace App\Event\Tools;

use Illuminate\Support\Facades\Artisan;

class EventTools
{
    public static function runCommand(): int
    {
        return Artisan::call('events:update');
}
}
