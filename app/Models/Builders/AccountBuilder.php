<?php

namespace App\Models\Builders;

use App\Models\Account;
use App\Models\Builders\Concerns\InteractsWithRecordOwner;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Account>
 */
class AccountBuilder extends Builder
{
    use InteractsWithRecordOwner;
}
