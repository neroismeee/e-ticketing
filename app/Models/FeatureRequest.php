<?php

namespace App\Models;

use App\Enums\AssignedTeam;
use App\Enums\FeatureRequestStatus;
use App\Enums\Priorities;
use App\Enums\RequestType;
use App\Observers\FeatureRequestObserver;
use App\Traits\HasActivityLog;
use App\Traits\HasApproval;
use App\Traits\HasAssignment;
use App\Traits\HasAttachments;
use App\Traits\HasComments;
use App\Traits\HasStatusHistory;
use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

#[Fillable([
    'id',
    'title',
    'description',
    'request_type',
    'priority',
    'status',
    'progress',
    'reporter_id',
    'assigned_to_id',
    'assigned_team',
    'date_submitted',
    'approval_date',
    'assignment_date',
    'start_date',
    'due_date',
    'completion_date',
    'review_date',
    'estimated_effort',
    'actual_effort',
    'sla_time_elapsed',
    'sla_time_remaining',
    'sla_breached',
    'approved_by',
    'rejection_reason',
    'roi_impact',
    'quality_impact',
    'post_implementation_notes',
    'source_ticket_id',
    'is_direct_input',
])]

#[ObservedBy([FeatureRequestObserver::class])]

class FeatureRequest extends Model
{
    use HasComments, HasAttachments, HasStatusHistory, HasActivityLog, HasAssignment, HasApproval, HasTags;
    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'priority' => Priorities::class,
        'status' => FeatureRequestStatus::class,
        'assigned_team' => AssignedTeam::class,
        'request_type' => RequestType::class,
        'progress' => 'integer',
        'approval_date' => 'datetime',
        'assignment_date' => 'datetime',
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'completion_date' => 'datetime',
        'review_date' => 'datetime',
        'estimated_effort' => 'decimal:2',
        'actual_effort' => 'decimal:2',
        'sla_time_elapsed' => 'decimal:2',
        'sla_time_remaining' => 'decimal:2',
        'sla_breached' => 'boolean',
        'is_direct_input' => 'boolean',
    ];

    // Relations
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function sourceTicket()
    {
        return $this->belongsTo(Ticket::class, 'source_ticket_id');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class, 'feature_request_id');
    }

    public function completedMilestone(): HasMany
    {
        return $this->hasMany(Milestone::class, 'feature_request_id')
            ->where('is_completed', true);
    }

    public function pendingMilestone(): HasMany
    {
        return $this->hasMany(Milestone::class, 'feature_request_id')
            ->where('is_completed', false);
    }

    public function timelineEntries(): HasMany
    {
        return $this->hasMany(TimelineEntry::class, 'feature_request_id')->orderBy('phase');
    }

    public function completedTimeline(): HasMany
    {
        return $this->hasMany(TimelineEntry::class, 'feature_request_id')->where('is_completed', true);
    }

    public function pendingTimeline(): HasMany
    {
        return $this->hasMany(TimelineEntry::class, 'feature_request_id')->where('is_completed', false);
    }

    // Helpers
    public function isAssignedToUser(): bool
    {
        return ! is_null($this->assigned_to_id);
    }

    public function isAssignedToTeam(): bool
    {
        return ! is_null($this->assigned_team);
    }

    public function isAssignable(): bool
    {
        $currentStatus = $this->status->value;

        return in_array($currentStatus, FeatureRequestStatus::assignableStatuses());
    }

    public function isTerminal(): bool
    {
        $currentStatus = $this->status->value;

        return in_array($currentStatus, FeatureRequestStatus::terminalStatuses());
    }

    public function isCompleted(): bool
    {
        return $this->status === FeatureRequestStatus::Completed;
    }

    public function isFromTicket(): bool
    {
        return ! is_null($this->source_ticket_id);
    }

    public function calculateOverallProgress(): int
    {
        $milestones = $this->milestones;

        if ($milestones->isEmpty()) {
            return 0;
        }

        return (int) $milestones->avg('progress');
    }

    // Scopes
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByRequestType(Builder $query, string $type): Builder
    {
        return $query->where('request_type', $type);
    }

    public function scopeSlaBreached(Builder $query): Builder
    {
        return $query->where('sla_breached', true);
    }

    public function scopeDirectInput(Builder $query): Builder
    {
        return $query->where('is_direct_input', true);
    }

    public function scopeFromTicket(Builder $query): Builder
    {
        return $query->whereNotNull('source_ticket_id');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereNotNull('due_date')
            ->where('due_date', '<', now())
            ->whereNotIn('status', FeatureRequestStatus::terminalStatuses());
    }
}
