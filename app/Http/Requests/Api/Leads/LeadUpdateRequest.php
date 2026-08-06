<?php

namespace App\Http\Requests\Api\Leads;

use Illuminate\Foundation\Http\FormRequest;

class LeadUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'company_name' => is_string($this->company_name) ? trim($this->company_name) : $this->company_name,
            'currency' => is_string($this->currency) ? strtoupper(trim($this->currency)) : $this->currency,
        ]);
    }

    public function rules(): array
    {
        $user = $this->user();
        $salesRepRule = ($user && $user->role === 'admin')
            ? 'sometimes|nullable|integer|exists:users,id'
            : 'prohibited';

        return [
            'company_name' => 'sometimes|required|string|max:255',
            'contact_person' => 'sometimes|nullable|string|max:120',
            'contact_mobile' => 'sometimes|nullable|string|max:50',
            'contact_email' => 'sometimes|nullable|email|max:160',
            'contact_linkedin' => 'sometimes|nullable|string|max:255',
            'company_website' => 'sometimes|nullable|string|max:255',
            'event_id' => 'sometimes|nullable|integer|exists:events,id',
            'interested_package_id' => 'sometimes|nullable|integer|exists:packages,id',
            'expected_value' => 'sometimes|nullable|numeric|min:0',
            'currency' => 'sometimes|nullable|string|size:3',
            'probability' => 'sometimes|nullable|integer|min:0|max:100',
            'expected_close_date' => 'sometimes|nullable|date',
            'lead_notes' => 'sometimes|nullable|string|max:5000',
            'status' => 'sometimes|nullable|in:new,contacted,meeting,negotiation,won,lost',
            'lost_category_id' => 'sometimes|nullable|integer|exists:lost_categories,id',
            'lost_reason' => 'sometimes|nullable|string|max:255',
            'sales_rep_id' => $salesRepRule,
            'last_meeting' => 'sometimes|nullable|date',
            'next_followup' => 'sometimes|nullable|date',
            'country_ids' => 'sometimes|nullable|array',
            'country_ids.*' => 'integer|exists:countries,id',
        ];
    }
}
