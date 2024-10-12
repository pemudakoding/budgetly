<?php

namespace App\Models\Builders;

use App\Models\Builders\Concerns\InteractsWithRecordOwner;
use App\Models\Income;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Income>
 */
class IncomeBuilder extends Builder
{
    use InteractsWithRecordOwner;
}
