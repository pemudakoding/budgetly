<?php

namespace App\Enums;

use App\Concerns\EnumToArray;

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
}
