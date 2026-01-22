<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ErrorReport extends Model
{
    protected $table = 'error_reports';
    protected $fillable = [
        'id',
        'title',
        'description',
        'category',
        'priority',
        'status',
        'reporter_id',
        'assigned_to_id',
        'assigned_team',
        'date_reported',
        'start_date',
        'due_date',
        'completion_date',
        'estimated_effort',
        'actual_effort',
        'sla_time_elapsed',
        'sla_time_remaining',
        'sla_breached',
        'source_ticket_id',
        'is_direct_input',
    ];
    
    public const CATEGORIES = [
        'hardware',
        'network',
        'software',
    ];

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
        'critical',
    ];

    public const STATUSES = [
        'pending_approval',
        'in_progress',
        'completed',
        'overdue',
    ];
    
    public const TEAMS = [
        'programmer',
        'network',
        'hardware',
    ];
}
