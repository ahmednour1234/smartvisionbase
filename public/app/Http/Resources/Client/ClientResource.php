<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'phone'         => $this->phone,
            'job'           => $this->job,
            'img'=>$this->img,
            'country_code'  => $this->country_code,
'status' => in_array(strtolower((string) $this->status), ['complete','verified'], true)
    ? 'verify'
    : (string) $this->status,
            'email_verified_at' => optional($this->email_verified_at)->toIso8601String(),
            'created_at'    => optional($this->created_at)->toIso8601String(),
        ];
    }
}
