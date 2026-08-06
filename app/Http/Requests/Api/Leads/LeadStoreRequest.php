<?php

namespace App\Http\Requests\Api\Leads;

use Illuminate\Foundation\Http\FormRequest;

class LeadStoreRequest extends FormRequest
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
            ? 'nullable|integer|exists:users,id'
            : 'prohibited';

        return [
            'company_name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:120',
            'contact_mobile' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email|max:160',
            'contact_linkedin' => 'nullable|string|max:255',
            'company_website' => 'nullable|string|max:255',
            'event_id' => 'nullable|integer|exists:events,id',
            'interested_package_id' => 'nullable|integer|exists:packages,id',
            'expected_value' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string|size:3',
            'probability' => 'nullable|integer|min:0|max:100',
            'expected_close_date' => 'nullable|date',
            'lead_notes' => 'nullable|string|max:5000',
            'status' => 'nullable|in:new,contacted,meeting,negotiation,won,lost',
            'lost_category_id' => 'nullable|integer|exists:lost_categories,id',
            'lost_reason' => 'nullable|string|max:255',
            'sales_rep_id' => $salesRepRule,
            'last_meeting' => 'nullable|date',
            'next_followup' => 'nullable|date',
            'country_ids' => 'nullable|array',
            'country_ids.*' => 'integer|exists:countries,id',
        ];
    }
}
