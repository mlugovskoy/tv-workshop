<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RepairUpdateRequest extends FormRequest
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
            'status' => ['sometimes', 'string'],
            'problem_description' => ['sometimes', 'string'],
            'diagnosis' => ['sometimes', 'nullable', 'string'],
            'repair_description' => ['sometimes', 'nullable', 'string'],
            'estimated_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'final_price' => ['sometimes', 'nullable', 'numeric', 'min:0']
        ];
    }
}
