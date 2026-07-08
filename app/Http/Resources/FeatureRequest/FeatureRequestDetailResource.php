<?php

namespace App\Http\Resources\FeatureRequest;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TagResource;

class FeatureRequestDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return ([
            'id' => $this->id,
            'title' => $this->title,
            'request_type' => [
                'value' => $this->request_type->value,
                'label' => $this->request_type->label()
            ],
            'priority' => [
                'value' => $this->priority->value,
                'label' => $this->priority->label()
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label()
            ],
            'progress' => $this->progress,
            'description' => $this->description,
            'reporter' => $this->reporter ? [
                'id' => $this->reporter->id,
                'name' => $this->reporter->name,
                'username' => $this->reporter->username
            ] : null,
            'assigned_user' => $this->assignedUser ? [
                'id' => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
                'username' => $this->assignedUser->username,
            ] : null,
            'assigned_team' => $this->assigned_team ? [
                'value' => $this->assigned_team->value,
                'label' => $this->assigned_team->label(),
            ] : null,
            'dates' => [
                'submitted' => $this->date_submitted?->format('Y-m-d H:i:s'),
                'approval' => $this->approval_date?->format('Y-m-d H:i:s'),
                'assignment' => $this->assignment_date?->format('Y-m-d H:i:s'),
                'start' => $this->start_date?->format('Y-m-d H:i:s'),
                'due' => $this->due_date?->format('Y-m-d H:i:s'),
                'completion' => $this->completion_date?->format('Y-m-d H:i:s'),
                'review' => $this->review_date?->format('Y-m-d H:i:s'),
            ],
            'approval' => ($this->approved_by || $this->rejection_reason) ? [
                'approved_by' => $this->approver ? [
                    'id' => $this->approver->id,
                    'name' => $this->approver->name,
                    'username' => $this->approver->username,
                ] : null,
                'rejection_reason' => $this->rejection_reason
            ] : null,
            'sla' => [
                'breached' => $this->sla_breached,
                'time_elapsed' => $this->sla_time_elapsed,
                'time_remaining' => $this->sla_time_remaining,
            ],
            'effort' => [
                'estimated' => $this->estimated_effort,
                'actual' => $this->actual_effort,
            ],
            'impact' => [
                'roi' => $this->roi_impact,
                'quality' => $this->quality_impact
            ],
            'post_implementation_notes' => $this->post_implementation_notes,
            'source' => [
                'is_direct_input' => $this->is_direct_input,
                'source_ticket_id' => $this->source_ticket_id
            ],
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ]);
    }
}
