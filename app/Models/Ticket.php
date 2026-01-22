<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'tickets';
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
        'due_date',
        'resolved_date',
        'closed_date',
        'sla_breached',
        'response_time',
        'resolution_time',
        'estimated_effort',
        'actual_effort',
        'parent_ticket_id',
        'converted_to_type',
        'converted_to_id',
        'converted_at',
        'converted_by',
        'conversion_reason',
        'created_at',
        'updated_at',
    ];

    public const CATEGORIES = [
        'software_bug',
        'feature_request',
        'network_issue',
        'hardware_failure',
        'system_error',
        'performance_issue',
    ];

    public const PRIORITIES = [
        'low',
        'medium',
        'high',
        'critical',
    ];

    public const STATUSES = [
        'draft',
        'pending_approval',
        'assigned',
        'in_progress',
        'waiting_for_user',
        'resolved',
        'closed',
        'converted',

    ];

    public const ASSIGNED_TEAMS = [
        'programmer',
        'network',
        'hardware',
    ];
    
    public const CONVERTED_TO_TYPES = [
        'error_report',
        'feature_request',
    ];

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $year = Carbon::now()->year;

    //         $last = self::whereYear('created_at', $year)
    //             ->orderBy('id', 'desc')
    //             ->first();
                
    //         $number = 1;
    //         if ($last) {
    //             $number = (int) substr($last->id, -4) + 1;
    //         }

    //         $model->id = 'TKT-' . $year . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    //     });
    // }

    

}
