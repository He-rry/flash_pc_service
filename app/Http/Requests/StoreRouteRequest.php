<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-routes') ?? false;
    }

    public function rules(): array
    {
        return [
            'route_name' => 'required|string|max:255',
            'waypoints' => 'required',
        ];
    }
}
