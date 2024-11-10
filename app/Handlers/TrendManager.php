<?php

namespace App\Handlers;

use App\Enums\Period;
use Carbon\Carbon;
use Flowframe\Trend\Trend;

class TrendManager
{
    private const PERIOD_INTERVALS = [
        Period::Today->value => 'perHour',
        Period::Yesterday->value => 'perDay',
        Period::LastSevenDays->value => 'perDay',
        Period::LastMonth->value => 'perWeek',
        Period::ThisMonth->value => 'perWeek',
        Period::MonthToDate->value => 'perWeek',
        Period::YearToDate->value => 'perMonth',
    ];

    public function setTrendInterval(string $filter, Trend $trend, Carbon $startDate, Carbon $endDate): void
    {
        if ($filter === Period::Custom->value) {
            $this->handleCustomPeriod($trend, $startDate, $endDate);

            return;
        }

        $method = self::PERIOD_INTERVALS[$filter] ?? 'perHour';

        $trend->$method();
    }

    private function handleCustomPeriod(Trend $trend, ?Carbon $startDate, ?Carbon $endDate): void
    {
        if (! $startDate || ! $endDate) {
            $trend->perHour();

            return;
        }

        $diffDays = (int) $startDate->diffInDays($endDate);

        $interval = match (true) {
            $diffDays > 90 => 'perMonth',
            $diffDays >= 30 => 'perWeek',
            $diffDays > 1 => 'perDay',
            default => 'perHour'
        };

        $trend->$interval();
    }
}
