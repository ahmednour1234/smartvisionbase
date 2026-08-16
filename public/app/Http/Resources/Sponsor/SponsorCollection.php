<?php

namespace App\Http\Resources\Sponsor;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class SponsorCollection extends ResourceCollection
{
    public $collects = SponsorResource::class;

    public function toArray($request)
    {
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
