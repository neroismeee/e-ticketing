<?php

namespace App\Services\ErrorReport;

use App\Enums\ErrorReportStatus;
use App\Models\ErrorReport;
use App\Services\StatusHistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ErrorReportWorkService
{
    public function __construct(
        private readonly StatusHistoryService $statusHistoryService
    ) {}

    public function start(ErrorReport $error, array $data = []): ErrorReport
    {
        $currentStatus = $error->status->value;

        if ($error->status !== ErrorReportStatus::PendingApproval) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot start error report with status {$currentStatus}"
                ]
            ]);
        }

        DB::transaction(function () use ($error, $data) {
            $error->update([
                'status' => ErrorReportStatus::InProgress->value,
                'start_date' => $data['start_date'] ?? now(),
                'estimated_effort' => $data['estimated_effort'] ?? $error->estimated_effort
            ]);

            $this->statusHistoryService->update(
                resource: $error,
                newStatus: ErrorReportStatus::InProgress->value,
            );
        });

        return $error->load(['reporter', 'assignedUser']);
    }

    public function resolve(ErrorReport $error, array $data = []): ErrorReport
    {
        $currentStatus = $error->status->value;

        $resolvableStatuses = [
            ErrorReportStatus::InProgress,
            ErrorReportStatus::Overdue,
        ];

        if (! in_array($currentStatus, $resolvableStatuses)) {
            throw ValidationException::withMessages([
                'status' => [
                    "Cannot resolve error report with status '{$currentStatus}'."
                ]
            ]);
        }

        DB::transaction(function () use ($error, $data) {
            $error->update([
                'status' => ErrorReportStatus::Completed->value,
                'completion_date' => $data['completion_date'] ?? now(),
                'actual_effort' => $data['actual_effort'] ?? $error->actual_effort
            ]);

            $this->statusHistoryService->update(
                resource: $error,
                newStatus: ErrorReportStatus::Completed->value
            );
        });

        return $error->load(['reporter', 'assignedUser']);
    }
}
