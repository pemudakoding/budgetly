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
     * @return array<int, Carbon>
     */
    public function getDate(): array
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
                Carbon::now()->subMonth()->startOfMonth(),
                Carbon::now()->subMonth()->endOfMonth(),
            ],
            self::ThisMonth => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
            ],
            self::MonthToDate => [
                Carbon::now()->startOfMonth(),
                Carbon::today(),
            ],
            self::YearToDate => [
                Carbon::now()->startOfYear(),
                Carbon::today(),
            ],
            self::Custom => throw new ValueError('Custom period requires specific start and end dates.')
        };
    }

    /**
     * @return array<int, Carbon>
     */
    public static function getDateFrom(?Period $period): array
    {
        return match ($period) {
            self::Yesterday => self::Yesterday->getDate(),
            self::LastSevenDays => self::LastSevenDays->getDate(),
            self::LastMonth => self::LastMonth->getDate(),
            self::ThisMonth => self::ThisMonth->getDate(),
            self::MonthToDate => self::MonthToDate->getDate(),
            self::YearToDate => self::YearToDate->getDate(),
            self::Custom => self::Custom->getDate(),
            default => self::Today->getDate(),
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
