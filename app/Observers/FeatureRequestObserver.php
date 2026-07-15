<?php

namespace App\Observers;

use App\Models\FeatureRequest;
use App\Services\Log\ActivityLogService;
use App\Services\Sla\SlaCalculator;
use App\Services\TicketService;
use Illuminate\Support\Carbon;

class FeatureRequestObserver
{
    public function __construct(
        private readonly ActivityLogService $logService,
        private readonly TicketService $ticketService
    ) {}

    public function creating(FeatureRequest $featureRequest): void
    {
        // hitung due date
        if (empty($featureRequest->due_date) && ! empty($featureRequest->priority)) {
            $priority = $featureRequest->priority->value;

            $featureRequest->due_date = SlaCalculator::calculateDueDate(
                priority: $priority,
                from: $featureRequest->date_submitted ?? now()
            );
        }

        // update sla
        if ($featureRequest->due_date) {
            $featureRequest->sla_time_remaining = SlaCalculator::calculateTimeRemaining(
                dueDate: Carbon::parse($featureRequest->due_date)
            );
            $featureRequest->sla_time_elapsed = 0;
            $featureRequest->sla_breached = false;
        }
    }
    /**
     * Handle the FeatureRequest "created" event.
     */
    public function created(FeatureRequest $featureRequest): void
    {
        $this->logService->logCreated($featureRequest, [
            'title' => $featureRequest->title,
            'priority' => $featureRequest->priority,
            'status' => $featureRequest->status
        ]);
    }

    public function updating(FeatureRequest $featureRequest): void
    {
        if ($featureRequest->isDirty('status')) {
            $newStatus = $featureRequest->status->value;

            if ($newStatus === 'approved' && is_null($featureRequest->approval_date)) {
                $featureRequest->approval_date = now();
            }

            if ($newStatus === 'assigned' && is_null($featureRequest->assignment_date)) {
                $featureRequest->assignment_date = now();
            }

            if ($newStatus === 'development' && is_null($featureRequest->start_date)) {
                $featureRequest->start_date = now();
            }

            if ($newStatus === 'completed') {
                $featureRequest->completion_date = now();
                $featureRequest->progress = 100;

                // update sla
                if ($featureRequest->due_date) {
                    $featureRequest->sla_breached = SlaCalculator::isSlaBreached(
                        dueDate: Carbon::parse($featureRequest->due_date),
                        completionTime: $featureRequest->completion_date
                    );
                    $featureRequest->sla_time_elapsed = SlaCalculator::calculateTimeElapsed(
                        startTime: $featureRequest->date_submitted,
                        endTime: $featureRequest->completion_date
                    );
                    $featureRequest->sla_time_remaining = 0;
                }
            }
        }
    }

    /**
     * Handle the FeatureRequest "updated" event.
     */
    public function updated(FeatureRequest $featureRequest): void
    {
        // ambil perubahan field
        $changes = [];

        foreach ($featureRequest->getChanges() as $field => $newValue) {

            if (in_array($field, [
                'updated_at',
                'status',
                'assigned_to_id'
            ])) {
                continue;
            }

            $changes[$field] = [
                'old' => $featureRequest->getOriginal($field),
                'new' => $newValue,
            ];
        }

        if (!empty($changes)) {
            $this->logService->logUpdated($featureRequest, $changes);
        }
        
        // sync metrics from converted resource to ticket
        if ($featureRequest->wasChanged('status')) {
            $newStatus = $featureRequest->status->value;

            if ($newStatus === 'completed' && $featureRequest->source_ticket_id) {
                $this->ticketService->syncResolutionFromConvertedResource(
                    sourceTicketId: $featureRequest->source_ticket_id,
                    completionDate: $featureRequest->completion_date
                );
            }
        }
    }

    /**
     * Handle the FeatureRequest "deleted" event.
     */
    public function deleted(FeatureRequest $featureRequest): void
    {
        //
    }

    /**
     * Handle the FeatureRequest "restored" event.
     */
    public function restored(FeatureRequest $featureRequest): void
    {
        //
    }

    /**
     * Handle the FeatureRequest "force deleted" event.
     */
    public function forceDeleted(FeatureRequest $featureRequest): void
    {
        //
    }
}
