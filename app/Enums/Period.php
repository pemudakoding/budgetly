<?php

namespace App\Enums;

use App\Concerns\EnumToArray;

enum Period: string
{
    use EnumToArray;

    case Today = 'Today';
    case Yesterday = 'Yesterday';
    case LastWeek = 'Last Week';
    case LastMonth = 'Last Month';
    case ThisWeek = 'This Week';
    case ThisMonth = 'This Month';
    case LastYear = 'Last Year';
    case ThisYear = 'This Year';
    case Custom = 'Custom';
}
