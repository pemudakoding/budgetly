<?php

namespace App\Models\Builders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends Builder<Account>
 */
class AccountBuilder extends Builder
{
    public function whereOwnedBy(User|int $user): AccountBuilder
    {
        $this->where(
            'user_id',
            $user instanceof User
                ? $user->id
                : $user
        );

        return $this;
    }
}
