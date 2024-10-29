<?php

namespace App\Livewire\Auth;

use App\Enums\Role;
use App\Models\User;

class Register extends \Filament\Pages\Auth\Register
{
    public function afterRegister(): void
    {
        /** @var User $user */
        $user = $this->form->getModelInstance();

        $user->assignRole(Role::User);
    }
}
