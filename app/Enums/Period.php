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
            self::Today => [
                Carbon::now()->startOfDay(),
                Carbon::now(),
            ],
            self::Yesterday => [
                Carbon::now()->subDay()->startOfDay(),
                Carbon::now()->subDay()->endOfDay(),
            ],
            self::LastSevenDays => [
                Carbon::now()->subDays(7),
                Carbon::today(),
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
                Carbon::today(),
            ],
            self::YearToDate => [
                Carbon::now()->startOf('year'),
                Carbon::today(),
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

    public function render(): string
    {
        return __('budgetly::period.'.str($this->value)->lower()->kebab());
    }

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $data = [];

        foreach (Period::cases() as $case) {
            $data[$value = (string) $case->value] = __('budgetly::period.'.str($value)->lower()->kebab());
        }

        return $data;
    }
}
