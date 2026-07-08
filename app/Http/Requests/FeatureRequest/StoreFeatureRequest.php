<?php

namespace App\Http\Requests\FeatureRequest;

use App\Enums\Priorities;
use App\Enums\RequestType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreFeatureRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'title' => Str::title(trim($this->title)),
        ]);  
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['string', 'required', 'max:200'],
            'description' => ['required', 'string'],
            'request_type' => ['required', 'string', Rule::in(RequestType::values())],
            'priority' => ['required', 'string', Rule::in(Priorities::values())],
            'due_date' => ['nullable', 'date', 'after:now'],
            'estimated_effort' => ['nullable', 'numeric', 'min:0'],
            'roi_impact' => ['nullable', 'string'],
            'quality_impact' => ['nullable', 'string'],
        ];
    }
}
