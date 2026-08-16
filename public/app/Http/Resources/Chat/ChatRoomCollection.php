<?php

namespace App\Http\Resources\Chat;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

class ChatRoomCollection extends ResourceCollection
{
    public $collects = ChatRoomResource::class;

    public function toArray($request)
    {
        return ['data' => $this->collection];
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
