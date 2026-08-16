<?php

namespace App\Http\Resources\Speaker;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class SpeakerCollection extends ResourceCollection
{
    public $collects = SpeakerResource::class;

    public function toArray($request)
    {
        // فقط البيانات؛ الـ meta سنضعها في with()
        return [
            'data' => $this->collection,
        ];
    }

    public function with($request)
    {
        /** @var LengthAwarePaginator $p */
        $p = $this->resource;

        return [
            'pagination' => [
                'current_page' => $p->currentPage(),
                'per_page'     => $p->perPage(),
                'total'        => $p->total(),
                'last_page'    => $p->lastPage(),
                'from'         => $p->firstItem(),
                'to'           => $p->lastItem(),
                'has_more'     => $p->hasMorePages(),
            ],
        ];
    }
}
