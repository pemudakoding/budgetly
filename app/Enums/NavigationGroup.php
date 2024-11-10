<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case Budgeting = 'Budgeting';
    case Settings = 'Settings';

    case Report = 'Reports';

    public function render(): string
    {
        return match ($this) {
            self::Budgeting => __('budgetly::navigation-group.budgeting'),
            self::Report => __('budgetly::navigation-group.reports'),
            default => $this->value,
        };
    }
}
