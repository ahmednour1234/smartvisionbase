<?php

namespace App\Http\Requests\Api\Leads;

use Illuminate\Foundation\Http\FormRequest;

class LeadIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => is_string($this->q) ? trim($this->q) : $this->q,
            'status' => is_string($this->status) ? trim($this->status) : $this->status,
            'search_mode' => is_string($this->search_mode) ? trim($this->search_mode) : $this->search_mode,
        ]);
    }

    public function rules(): array
    {
        $user = $this->user();
        $salesRepRule = ($user && $user->role === 'admin')
            ? 'sometimes|integer|exists:users,id'
            : 'prohibited';

        return [
            'q' => 'sometimes|string|max:100',
            // smart: index-friendly search (default)
            // contains: legacy LIKE %term% (guarded by min length to reduce full scans)
            'search_mode' => 'sometimes|in:smart,contains',
            'status' => 'sometimes|in:new,contacted,meeting,negotiation,won,lost',
            'event_id' => 'sometimes|integer|exists:events,id',
            'interested_package_id' => 'sometimes|integer|exists:packages,id',
            'lost_category_id' => 'sometimes|integer|exists:lost_categories,id',
            'sales_rep_id' => $salesRepRule,
            'next_followup_from' => 'sometimes|date',
            'next_followup_to' => 'sometimes|date',
            'per_page' => 'sometimes|integer|min:5|max:100',
            'page' => 'sometimes|integer|min:1|max:1000000',
            'cursor' => 'sometimes|string|max:255',
        ];
    }
}
