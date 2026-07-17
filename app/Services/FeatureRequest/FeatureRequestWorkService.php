<?php

namespace App\Services\FeatureRequest;

use App\Enums\FeatureRequestStatus;
use App\Models\FeatureRequest;
use App\Services\StatusHistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FeatureRequestWorkService
{
    public function __construct(
        private readonly StatusHistoryService $statusHistoryService
    ) {}

    public function start(FeatureRequest $feature, array $data): FeatureRequest
    {
        $currentStatus = $feature->status->value;

        if ($feature->status !== FeatureRequestStatus::Assigned->value) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot start feature request with status '{$currentStatus}'"
                ]
            ]);
        }

        DB::transaction(function () use ($feature, $data) {
            $feature->update([
                'status' => FeatureRequestStatus::Development->value,
                'start_date' => $data['start_date'] ?? now(),
                'estimated_effort' => $data['estimated_effort'] ?? $feature->estimated_effort
            ]);

            $this->statusHistoryService->update(
                resource: $feature,
                newStatus: FeatureRequestStatus::Development->value,
            );
        });
        
        return $feature->load(['reporter', 'assignedUser']);
    }

    public function resolve(FeatureRequest $feature, array $data): FeatureRequest
    {
        $currentStatus = $feature->status->value;

        if ($currentStatus !== FeatureRequestStatus::Validation) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot resolve feature request with status {$currentStatus}"
                ]
            ]);
        }

        DB::transaction(function () use ($feature, $data) {
            $feature->update([
                'status' => FeatureRequestStatus::Completed,
                'completion_date' => $data['completion_date'] ?? now(),
                'actual_effort' => $data['actual_effort'] ?? $feature->actual_effort,
                'progress' => 100,
                'post_implementation_notes' => $data['post_implementation_notes'] ?? $feature->post_implementation_notes,
                'roi_impact' => $data['roi_impact'] ?? $feature->roi_impact,
                'quality_impact' => $data['quality_impact'],
                'review_date' => now(),
            ]);

            $this->statusHistoryService->update(
                resource: $feature,
                newStatus: FeatureRequestStatus::Completed->value,
            );
        });

        return $feature->load(['reporter', 'assignedUser']);
    }

    public function close(FeatureRequest $feature, string $reason): FeatureRequest
    {
        $currentStatus = $feature->status->value;

        if (in_array($currentStatus, FeatureRequestStatus::terminalStatuses())) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot closed feature request with status {$currentStatus}"
                ]
            ]);
        }

        DB::transaction(function () use ($feature, $reason) {
            $feature->update([
                'status' => FeatureRequestStatus::Cancelled,
                'rejection_reason' => $reason ?? $feature->rejection_reason  
            ]);

            $this->statusHistoryService->update(
                resource: $feature,
                newStatus: FeatureRequestStatus::Cancelled->value,
            );
        });

        return $feature->load(['reporter', 'assignedUser']);
    }
}