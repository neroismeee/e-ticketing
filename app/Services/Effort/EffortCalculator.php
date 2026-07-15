<?php

namespace App\Services\Effort;

use Illuminate\Support\Carbon;

class EffortCalculator
{
    public static function calculateResponseTime(
        Carbon $reportedAt,
        Carbon $assignedAt
    ): float {
        return round($reportedAt->diffInMinutes($assignedAt) / 60, 2);
    }

    public static function calculateResolutionTime(
        Carbon $reportedAt,
        Carbon $resolvedAt
    ): float {
        return round($reportedAt->diffInMinutes($resolvedAt) / 60, 2);
    }

    public static function calculateEffortVariance(
        ?float $estimated,
        ?float $actual
    ): ?float {
        if (is_null($estimated) || is_null($actual)) {
            return null;
        }

        return round($estimated - $actual, 2);
    }

    public static function calculateEffortEfficiency(
        ?float $estimated,
        ?float $actual
    ): ?float {
        if (is_null($estimated) || is_null($actual) || $actual = 0) {
            return null;
        }

        return round($estimated / $actual * 100, 2);
    }
}