<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-services') ?? false;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'status_id' => 'required|exists:statuses,id',
            'service_type_id' => 'required|exists:service_types,id',
        ];
    }
}
