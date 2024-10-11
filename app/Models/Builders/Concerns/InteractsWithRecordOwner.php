<?php

namespace App\Models\Builders\Concerns;

use App\Models\User;

trait InteractsWithRecordOwner
{
    public function whereOwnedBy(User|int $user, string $column = 'user_id'): static
    {
        $this->where(
            $column,
            $user instanceof User
                ? $user->id
                : $user
        );

        return $this;
    }
}
