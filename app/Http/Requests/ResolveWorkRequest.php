<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ResolveWorkRequest extends FormRequest
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
            'completion_date' => ['nullable', 'date'],
            'actual_effort' => ['nullable', 'numeric', 'min:0'],

            // Feature request only
            'post_implementation_notes' => ['nullable', 'string'],
            'roi_impact' => ['nullable', 'string'],
            'quality_impact' => ['nullable', 'string']
        ];
    }
}
