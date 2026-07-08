<?php

namespace App\Http\Requests\FeatureRequest;

use App\Enums\Priorities;
use App\Enums\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateFeatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        if ($this->filled('title')) {
            $this->merge([
                'title' => Str::title(trim($this->title))
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'sometimes', 'max:200'],
            'description' => ['sometimes', 'string'],
            'request_type' => ['sometimes', 'string', Rule::in(RequestType::values())],
            'priority' => ['sometimes', 'string', Rule::in(Priorities::values())],
            'due_date' => ['nullable', 'date', 'after:now'],
            'start_date' => ['nullable', 'date'],
            'estimated_effort' => ['nullable', 'numeric', 'min:0'],
            'actual_effort' => ['nullable', 'numeric', 'min:0'],
            'progress' => ['sometimes', 'integer', 'min:0', 'max:100'],
            'roi_impact' => ['nullable', 'string'],
            'quality_impact' => ['nullable', 'string'],
            'post_implementation_notes' => ['nullable', 'string']
        ];
    }
}
