<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  // app/Http/Requests/StoreClientRequest.php

  public function rules(): array
  {
    return [
      'name'     => 'required|string|max:255',
      'email'    => 'required|email|unique:clients,email',
      'phone'    => 'nullable|string|max:20',
      'job'      => 'nullable|string|max:255',
      'active'   => 'required|boolean',
      'code'     => 'nullable|string|unique:clients,code',
      'company_name'=>'required|string',
      'country_code'=>'required',
      'img'      => 'nullable',
      'form_id'  => 'nullable|exists:forms,id',
    ];
  }
}
