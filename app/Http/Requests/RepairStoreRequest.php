<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RepairStoreRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'client_name' => ['required_without:client_id', 'string', 'max:255'],
            'phone' => ['required_without:client_id', 'string', 'max:255'],

            'device_id' => ['nullable', 'integer', 'exists:devices,id'],
            'brand' => ['required_without:device_id', 'string', 'max:255'],
            'model' => ['required_without:device_id', 'string', 'max:255'],

            'problem_description' => ['required', 'string'],
            'estimated_price' => ['nullable', 'numeric', 'min:0']
        ];
    }
}
