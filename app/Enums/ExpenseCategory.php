<?php

namespace App\Enums;

use App\Concerns\EnumToArray;

enum ExpenseCategory: string
{
    use EnumToArray;

    case Needs = 'Needs';
    case Wants = 'Wants';
    case Savings = 'Savings';
    case Liabilities = 'Liabilities';

    public function resolveColor(): string
    {
        return match ($this) {
            self::Savings => 'primary',
            self::Needs => 'warning',
            self::Wants => 'secondary',
            self::Liabilities => 'danger',
        };
    }

    public function resolveIcon(): string
    {
        return match ($this) {
            self::Savings => 'heroicon-m-banknotes',
            self::Needs => 'heroicon-m-shopping-cart',
            self::Wants => 'heroicon-m-gift',
            self::Liabilities => 'heroicon-m-credit-card',
        };
    }

    public function render(): string
    {
        return __('budgetly::expense-category.'.str($this->name)->lower());
    }
}
