<?php

namespace App\Enums;

use App\Concerns\EnumToArray;
use ValueError;

enum Month: string
{
    use EnumToArray;

    case January = 'January';
    case February = 'February';
    case March = 'March';
    case April = 'April';
    case May = 'May';
    case June = 'June';
    case July = 'July';
    case August = 'August';
    case September = 'September';
    case October = 'October';
    case November = 'November';
    case December = 'December';

    public function toNumeric(): string
    {
        return match ($this) {
            self::January => '01',
            self::February => '02',
            self::March => '03',
            self::April => '04',
            self::May => '05',
            self::June => '06',
            self::July => '07',
            self::August => '08',
            self::September => '09',
            self::October => '10',
            self::November => '11',
            self::December => '12',
        };
    }

    public static function fromNumeric(string $numeric): self
    {
        return match ($numeric) {
            '01' => self::January,
            '02' => self::February,
            '03' => self::March,
            '04' => self::April,
            '05' => self::May,
            '06' => self::June,
            '07' => self::July,
            '08' => self::August,
            '09' => self::September,
            '10' => self::October,
            '11' => self::November,
            '12' => self::December,
            default => throw new ValueError("Invalid month numeric value: $numeric"),
        };
    }
}
