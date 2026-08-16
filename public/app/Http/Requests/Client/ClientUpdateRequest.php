<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ClientUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes','string','max:255'],
            'phone'        => ['sometimes','string','max:30'],
            'job'          => ['sometimes','string','max:255'],
            'country_code' => ['sometimes','string','max:5'],
                  'image'  => ['sometimes','nullable'],
        'img'    => ['sometimes','nullable'],
        // خيار حذف الصورة
        'delete_image' => ['sometimes','boolean'],
        ];
    }
}
