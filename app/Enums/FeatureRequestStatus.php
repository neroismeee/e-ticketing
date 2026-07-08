<?php

namespace App\Enums;

enum FeatureRequestStatus: string
{
    case Submission = 'submission';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Assigned = 'assigned';
    case Development = 'development';
    case Testing = 'testing';
    case Validation = 'validation';
    case Completed = 'completed';
    case PostImplementationReview = 'post_implementation_review';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Submission => 'Submission',
            self::PendingApproval => 'Pending Approval',
            self::Approved => 'Approved',
            self::Assigned => 'Assigned',
            self::Development => 'Development',
            self::Testing => 'Testing',
            self::Validation => 'Validation',
            self::Completed => 'Completed',
            self::PostImplementationReview => 'Post Implementation Review',
            self::Rejected => 'Rejected',
            self::Cancelled => 'Cancelled',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function assignableStatuses(): array
    {
        return [
            self::Submission->value,
            self::PendingApproval->value,
            self::Approved->value,
            self::Assigned->value,
            self::Development->value,
            self::Testing->value,
            self::Validation->value,
        ];
    }

    public static function terminalStatuses(): array
    {
        return [
            self::Completed,
            self::Rejected,
            self::Cancelled,
        ];
    }

}
