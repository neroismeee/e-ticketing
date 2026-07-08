<?php

namespace App\Services;

use App\Enums\FeatureRequestStatus;
use App\Enums\Priorities;
use App\Models\FeatureRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use function Illuminate\Support\now;

class FeatureRequestService
{
    public function store(array $data): FeatureRequest
    {
        $feature = FeatureRequest::create([
            ...$data,
            'id' => $this->generateFeatureRequestId(),
            'reporter_id' => Auth::id(),
            'status' => FeatureRequestStatus::PendingApproval->value,
            'progress' => '0',
            'date_submitted' => now(),
            'is_direct_input' => true,
            'source_ticket_id' => null,
            'due_date' => $data['due_date'] ?? $this->calculateDueDate($data['priority'])
        ]);

        return $feature->load(['reporter', 'assignedUser', 'approver', 'tags']);
    }

    public function update(FeatureRequest $feature, array $data): FeatureRequest
    {
        if ($feature->isTerminal()) {
            $status = $feature->status->value;

            throw ValidationException::withMessages([
                'status' => ["Feature request with status {$status} cannot be edited."]
            ]);
        }

        $feature->update($data);

        return $feature->load(['reporter', 'assignedUser', 'approver','tags']);
    }

    public function updateProgress(FeatureRequest $feature, int $progress): FeatureRequest
    {
        if ($feature->isTerminal()){
            throw ValidationException::withMessages([
                'progress' => ['Cannot update progress of a completed/rejected/cancelled feature request.']
            ]);
        }

        $feature->update();

        return $feature->load(['reporter','assignedUser', 'approver', 'tags']);
    }

    public function delete(FeatureRequest $feature): void
    {
        if ($feature->isTerminal()) {
            throw ValidationException::withMessages([
                'status' => ['Terminal feature request cannot be deleted.']
            ]);
        }

        $feature->delete();
    }

    // Query
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return FeatureRequest::query()
        ->with(['reporter:id,name,username', 'assignedUser:id,name,username', 'approver:id,name,username', 'tags'])
        ->when(
            isset($filters['status']),
            fn ($q) => $q->byStatus($filters['status'])
        )
        ->when(
            isset($filters['priority']),
            fn($q) => $q->byPriority($filters['priority'])
        )
        ->when(
            isset($filters['request_type']),
            fn($q) => $q->byRequestType($filters['request_type'])
        )
        ->when(
            isset($filters['assigned_team']),
            fn($q) => $q->where('assigned_team', $filters['assigned_team'])
        )
        ->when(
            isset($filters['reporter_id']),
            fn($q) => $q->where('reporter_id', $filters['reporter_id'])
        )
        ->when(
            isset($filters['sla_breached']),
            fn($q) => $q->slaBreached()
        )
        ->when(
            isset($filters['overdue']),
            fn($q) => $q->overdue()
        )
        ->when(
            isset($filters['is_direct_input']),
            function ($q) use ($filters) {
                $isDirect = filter_var($filters['is_direct_input'], FILTER_VALIDATE_BOOLEAN);
                return $isDirect ? $q->directInput() : $q->fromTicket();
            }
        )
        ->when(
            ! empty($filters['tags']),
            fn ($q) => $q->withAnyTags($filters['tags'])
        )
        ->when(
            isset($filters['search']),
            fn ($q) => $q->where('title', 'like', '%' . $filters['search'] . '%')
        )
        ->latest('date_submitted')
        ->paginate(min($perPage, 50));
    }

    // Private
    private function generateFeatureRequestId(): string
    {
        $year = now()->year;

        $lastRecord = FeatureRequest::whereYear('created_at', $year)
        ->lockForUpdate()
        ->orderBy('id', 'desc')
        ->first();

        $nextNumber = $lastRecord ? (int) substr($lastRecord->id, -3) + 1 : 1;

        return sprintf('FR-%d-%03d', $year, $nextNumber);
    }

    private function calculateDueDate(string $priority): Carbon
    {
        $priorityEnum = Priorities::tryFrom($priority);
        $hours = $priorityEnum ? $priorityEnum->slaHours() : 48;

        return now()->addHours($hours);
    } 
}