<?php

namespace App\Observers;

use App\Models\Ticket;
use App\Services\Effort\EffortCalculator;
use App\Services\Log\ActivityLogService;
use App\Services\Sla\SlaCalculator;

class TicketObserver
{
    public function __construct(
        private readonly ActivityLogService $logService
    ) {}

    public function creating(Ticket $ticket): void
    {
        // hitung due date
        if (empty($ticket->due_date) && ! empty($ticket->priority)) {
            $priority = $ticket->priority->value;

            $ticket->due_date = SlaCalculator::calculateDueDate(
                priority: $priority,
                from: $ticket->date_reported ?? now()
            );
        }

        // hitung sla awal
        if ($ticket->due_date) {
            $ticket->sla_breached = false;
        }
    }
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {
        $this->logService->logCreated($ticket, [
            'title' => $ticket->title,
            'category' => $ticket->category,
            'priority' => $ticket->priority,
            'status' => $ticket->status,
        ]);
    }

    public function updating(Ticket $ticket): void
    {
        // hitung response time
        if (
            $ticket->isDirty('assigned_to_id')
            && ! is_null($ticket->assigned_to_id)
            && is_null($ticket->getOriginal('assigned_to_id'))
            && $ticket->date_reported
        ) {
            $ticket->response_time = SlaCalculator::calculateResponseTime(
                reportedAt: $ticket->date_reported,
                assignedAt: $ticket->assignment_date
            );
        }

        // hitung resolution time
        if ($ticket->isDirty('status')) {
            $newStatus = $ticket->status->value;

            if (in_array($newStatus, ['resolved', 'closed'])) {
                if ($newStatus === 'resolved' && is_null($ticket->resolved_date)) {
                    $ticket->resolved_date = now();
                }

                if ($newStatus === 'closed' && is_null($ticket->closed_date)) {
                    $ticket->closed_date = now();
                }

                if ($ticket->date_reported) {
                    $ticket->resolution_time = EffortCalculator::calculateResolutionTime(
                        reportedAt: $ticket->date_reported,
                        resolvedAt: $ticket->resolved_date
                    );
                }

                // final sla check
                if ($ticket->due_date) {
                    $ticket->sla_breached = SlaCalculator::isSlaBreached(
                        dueDate: $ticket->due_date,
                        completionTime: $ticket->resolved_date
                    );
                }
            }
        }
    }
    /**
     * Handle the Ticket "updated" event.
     */
    public function updated(Ticket $ticket): void
    {
        // ambil perubahan field
        $changes = [];

        foreach ($ticket->getChanges() as $field => $newValue) {

            if (in_array($field, [
                'updated_at',
                'status',
                'assigned_to_id'
            ])) {
                continue;
            }

            $changes[$field] = [
                'old' => $ticket->getOriginal($field),
                'new' => $newValue,
            ];
        }

        if (!empty($changes)) {
            $this->logService->logUpdated($ticket, $changes);
        }
    }

    /**
     * Handle the Ticket "deleted" event.
     */
    public function deleted(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "restored" event.
     */
    public function restored(Ticket $ticket): void
    {
        //
    }

    /**
     * Handle the Ticket "force deleted" event.
     */
    public function forceDeleted(Ticket $ticket): void
    {
        //
    }
}
