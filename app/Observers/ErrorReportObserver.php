<?php

namespace App\Observers;

use App\Models\ErrorReport;
use App\Services\Log\ActivityLogService;
use App\Services\Sla\SlaCalculator;
use App\Services\TicketService;
use Illuminate\Support\Carbon;

class ErrorReportObserver
{
    public function __construct(
        private readonly ActivityLogService $logService,
        private readonly TicketService $ticketService
    ) {}

    public function creating(ErrorReport $errorReport): void
    {
        // hitung due date
        if (empty($errorReport->due_date) && ! empty($errorReport->priority)) {
            $priority = $errorReport->priority->value;

            $errorReport->due_date = SlaCalculator::calculateDueDate(
                priority: $priority,
                from: $errorReport->date_reported ?? now()
            );
        }

        // update sla
        if ($errorReport->due_date) {
            $errorReport->sla_time_remaining = SlaCalculator::calculateTimeRemaining(
                dueDate: Carbon::parse($errorReport->due_date)
            );
            $errorReport->sla_time_elapsed = 0;
            $errorReport->sla_breached = false;
        }
    }
    /**
     * Handle the ErrorReport "created" event.
     */
    public function created(ErrorReport $errorReport): void
    {
        $this->logService->logCreated($errorReport, [
            'title' => $errorReport->title,
            'priority' => $errorReport->priority,
            'status' => $errorReport->status
        ]);
    }

    public function updating(ErrorReport $errorReport): void
    {
        if ($errorReport->isDirty('status')) {
            $newStatus = $errorReport->status->value;

            // isi start date
            if ($newStatus === 'in_progress' && is_null($errorReport->start_date)) {
                $errorReport->start_date = now();
            }

            // hitung resolution time dan final sla saat status completed
            if ($newStatus === 'completed') {
                $errorReport->completion_date = $errorReport->completion_date ?? now();

                if ($errorReport->due_date) {
                    $errorReport->sla_breached = SlaCalculator::isSlaBreached(
                        dueDate: $errorReport->due_date,
                        completionTime: $errorReport->completion_date
                    );
                    $errorReport->sla_time_elapsed = SlaCalculator::calculateTimeElapsed(
                        startTime: $errorReport->date_reported,
                        endTime: $errorReport->completion_date
                    );
                    $errorReport->sla_time_remaining = 0;
                }
            }
        }
    }

    /**
     * Handle the ErrorReport "updated" event.
     */
    public function updated(ErrorReport $errorReport): void
    {
        // ambil perubahan field
        $changes = [];

        foreach ($errorReport->getChanges() as $field => $newValue) {

            if (in_array($field, [
                'updated_at',
                'status',
                'assigned_to_id'
            ])) {
                continue;
            }

            $changes[$field] = [
                'old' => $errorReport->getOriginal($field),
                'new' => $newValue,
            ];
        }

        if (!empty($changes)) {
            $this->logService->logUpdated($errorReport, $changes);
        }

        // sync metrics from converted resource to ticket
        if ($errorReport->wasChanged('status')) {
            $newStatus = $errorReport->status->value;

            if ($newStatus === 'completed' && $errorReport->source_ticket_id) {
                $this->ticketService->syncResolutionFromConvertedResource(
                    sourceTicketId: $errorReport->source_ticket_id,
                    completionDate: $errorReport->completion_date
                );
            }
        }
    }

    /**
     * Handle the ErrorReport "deleted" event.
     */
    public function deleted(ErrorReport $errorReport): void
    {
        //
    }

    /**
     * Handle the ErrorReport "restored" event.
     */
    public function restored(ErrorReport $errorReport): void
    {
        //
    }

    /**
     * Handle the ErrorReport "force deleted" event.
     */
    public function forceDeleted(ErrorReport $errorReport): void
    {
        //
    }
}
