<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Guest: customer report from public form. Logged-in admin: must have manage-services.
        return ! $this->user() || $this->user()->can('manage-services');
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|min:7|max:20',
            'customer_address' => 'required|string',
            'service_type_id' => 'required|exists:service_types,id',
            'lat' => 'nullable|numeric',
            'long' => 'nullable|numeric',
        ];
    }
}
