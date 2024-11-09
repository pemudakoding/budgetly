<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case Budgeting = 'Budgeting';
    case Settings = 'Settings';

    public function render(): string
    {
        return match ($this) {
            self::Budgeting => __('budgetly::navigation-group.budgeting'),
            default => $this->value,
        };
    }
}
