<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Ticket;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTicketRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|in:' . implode(',', Ticket::CATEGORIES),
            'priority' => 'required|string|in:' . implode(',', Ticket::PRIORITIES),
            'status' => 'required|string|in:' . implode(',', Ticket::STATUSES),
            'reporter_id' => 'required|integer',
            'assigned_to_id' => 'nullable|integer',
            'assigned_team' => 'nullable|string|max:255|in:' . implode(',', Ticket::ASSIGNED_TEAMS),
            'date_reported' => 'required|date',
            'due_date' => 'nullable|date',
            'resolved_date' => 'nullable|date',
            'closed_date' => 'nullable|date',
            'sla_breached' => 'required|boolean',
            'response_time' => 'nullable|integer',
            'resolution_time' => 'nullable|integer',
            'estimated_effort' => 'nullable|integer',
            'actual_effort' => 'nullable|integer',
            'parent_ticket_id' => 'nullable|integer',
            'converted_to_type' => 'nullable|string|in:' . implode(',', Ticket::CONVERTED_TO_TYPES),
            'converted_to_id' => 'nullable|integer',
            'converted_at' => 'nullable|date',
            'conversion_reason' => 'nullable|string|max:255',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422)
        );
    }
}
