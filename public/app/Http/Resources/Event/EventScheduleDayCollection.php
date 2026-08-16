<?php

namespace App\Http\Resources\Event;

use Illuminate\Http\Resources\Json\ResourceCollection;

class EventScheduleDayCollection extends ResourceCollection
{
    public $collects = EventScheduleDayResource::class;

    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }
}
