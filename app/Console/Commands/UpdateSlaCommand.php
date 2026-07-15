<?php

namespace App\Console\Commands;

use App\Models\ErrorReport;
use App\Models\FeatureRequest;
use App\Models\Ticket;
use App\Services\NotificationService;
use App\Services\Sla\SlaCalculator;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sla:update')]
#[Description('Update SLA metrics all of the active resources')]
class UpdateSlaCommand extends Command
{
    public function __construct(
        private readonly NotificationService $service
    ) {
        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating SLA metrics...');

        $this->updateTicketSla();
    }

    // Helpers
    private function updateTicketSla(): void
    {
        $tickets = Ticket::whereNotIn('status', ['resolved', 'closed', 'converted'])
            ->whereNotNull('due_date')
            ->whereNotNull('date_reported')
            ->get();

        $breachedCount = 0;

        foreach ($tickets as $ticket) {
            $slaPayload = SlaCalculator::generateSlaPayload(
                startTime: $ticket->date_reported,
                dueDate: $ticket->due_date
            );

            if ($slaPayload['sla_breached'] && ! $ticket->sla_breached) {
                $this->service->notifySlaBreached(
                    userId: $ticket->reporter_id,
                    ticket: $ticket
                );

                if ($ticket->assigned_to_id) {
                    $this->service->notifySlaBreached(
                        userId: $ticket->assigned_to_id,
                        ticket: $ticket
                    );
                }

                $breachedCount++;
            }

            $ticket->withoutEvents(fn() => $ticket->update($slaPayload));
        }

        $this->info("Tickets: {$tickets->count()} updated, {$breachedCount} new SLA breach(es).");
    }

    private function updateErrorReportSla(): void
    {
        $errorReports = ErrorReport::whereNotIn('status', ['completed'])
            ->whereNotNull('due_date')
            ->whereNotNull('date_reported')
            ->get();

        $overdueCount = 0;

        foreach ($errorReports as $errorReport) {
            $slaPayload = SlaCalculator::generateSlaPayload(
                startTime: $errorReport->date_reported,
                dueDate: $errorReport->due_date
            );

            if ($slaPayload['sla_breached'] && ! $errorReport->sla_breached) {
                $slaPayload['status'] = 'overdue';
                $overdueCount++;
            }

            $errorReport->withoutEvents(fn() => $errorReport->update($slaPayload));
        }

        $this->info("Error Reports: {$errorReport->count()} updated, {$overdueCount} marked overdue.");
    }

    private function updateFeatureRequestSla(): void
    {
        $featureRequests = FeatureRequest::whereNotIn('status', ['completed', 'rejected', 'cancelled'])
            ->whereNotNull('due_date')
            ->whereNotNull('date_submitted')
            ->get();

        foreach ($featureRequests as $featureRequest) {
            $slaPayload = SlaCalculator::generateSlaPayload(
                startTime: $featureRequest->date_submitted,
                dueDate: $featureRequest->due_date
            );

            $featureRequest->withoutEvents(fn() => $featureRequest->update($slaPayload));
        }

        $this->info("Feature Requests: {$featureRequest->count()} updated.");
    }
}
