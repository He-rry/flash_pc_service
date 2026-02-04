<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRouteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-routes') ?? false;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,csv',
            'route_name' => 'required|string|max:255',
        ];
    }
}
