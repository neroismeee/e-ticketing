<?php

namespace App\Services\Sla;

use App\Enums\Priorities;
use Illuminate\Support\Carbon;

class SlaCalculator
{
    public static function calculateDueDate(string $priority, ?Carbon $from = null): Carbon 
    {
        $from = $from ?? now();
        $hours = self::getSlaHours($priority);

        return $from->copy()->addHours($hours);
        
    }

    public static function calculateTimeElapsed(Carbon $startTime, ?Carbon $endTime = null): float
    {
        $until = $endTime ?? now();

        return round($startTime->diffInMinutes($until) / 60, 2);
    }

    public static function calculateTimeRemaining(Carbon $dueDate): float
    {
        $diffInMinutes = now()->diffInMinutes($dueDate, false);

        return round($diffInMinutes / 60, 2);
    }

    public static function isSlaBreached(Carbon $dueDate, ?Carbon $completionTime = null): bool
    {
        $checkAt = $completionTime ?? now();

        return $checkAt->isAfter($dueDate);
    }

    public static function calculateResponseTime(Carbon $reportedAt, Carbon $assignedAt): float 
    {
        return round($reportedAt->diffInMinutes($assignedAt) / 60, 2);
    }

    public static function calculateResolutionTime(Carbon $reportedAt, Carbon $resolvedAt): float
    {
        return round($reportedAt->diffInMinutes($resolvedAt) / 60, 2);
    }

    public static function getSlaHours(string $priority): int
    {
        $priorityEnum = Priorities::tryFrom($priority);

        if ($priorityEnum) {
            return $priorityEnum->slaHours();
        }

        return match ($priority) {
            'critical' => 4,
            'high' => 8,
            'medium' => 24,
            'low' => 72,
            default => 48
        };
    }

    public static function generateSlaPayload(
        Carbon $startTime,
        ?Carbon $dueDate = null,
        ?Carbon $completionTime = null
    ): array {
        $timeElapsed = self::calculateTimeElapsed($startTime, $completionTime);
        $timeRemaining = $dueDate ? self::calculateTimeRemaining($dueDate) : null;
        $isBreached = $dueDate ? self::isSlaBreached($dueDate, $completionTime): null;

        return [
            'sla_time_elapsed' => $timeElapsed,
            'sla_time_remaining' => $timeRemaining,
            'sla_breached' => $isBreached
        ];
    }
}