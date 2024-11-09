<?php

namespace App\Enums;

use App\Concerns\EnumToArray;
use Carbon\Carbon;
use ValueError;

enum Period: string
{
    use EnumToArray;

    case Today = 'Today';
    case Yesterday = 'Yesterday';
    case LastSevenDays = 'Last 7 Days';
    case LastMonth = 'Last Month';
    case ThisMonth = 'This Month';
    case MonthToDate = 'Month-to-Date';
    case YearToDate = 'Year-to-Date';
    case Custom = 'Custom';

    /**
     * @return string|array<int, mixed>
     */
    public function getDate(): string|array
    {
        return match ($this) {
            self::Today => Carbon::today(),
            self::Yesterday => Carbon::yesterday(),
            self::LastSevenDays => [
                Carbon::now()->subdays(7),
                self::Today->getDate(),
            ],
            self::LastMonth => [
                Carbon::now()->subMonth()->startOf('month'),
                Carbon::now()->subMonth()->endOf('month'),
            ],
            self::ThisMonth => [
                Carbon::now()->startOf('month'),
                Carbon::now()->endOf('month'),
            ],
            self::MonthToDate => [
                Carbon::now()->startOf('month'),
                self::Today->getDate(),
            ],
            self::YearToDate => [
                Carbon::now()->startOf('year'),
                self::Today->getDate(),
            ],
            self::Custom => throw new ValueError('Custom period requires specific start and end dates.')
        };
    }

    /**
     * @return string|array<int, mixed>
     */
    public static function getDateFrom(Period $period): array|string
    {
        return match ($period) {
            self::Today => self::Today->getDate(),
            self::Yesterday => self::Yesterday->getDate(),
            self::LastSevenDays => self::LastSevenDays->getDate(),
            self::LastMonth => self::LastMonth->getDate(),
            self::ThisMonth => self::ThisMonth->getDate(),
            self::MonthToDate => self::MonthToDate->getDate(),
            self::YearToDate => self::YearToDate->getDate(),
            self::Custom => self::Custom->getDate()
        };
    }
}
