<?php

namespace App\Enums;

enum AllocationArrangementType: string
{
    case AddAmount = 'Add Amount';
    case ReduceAmount = 'Reduce Amount';
    case SetAmount = 'Set Amount';

    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $data = [];

        foreach (AllocationArrangementType::cases() as $case) {
            $data[$value = $case->value] = __('filament-forms::components.select.options.'.str($value)->lower()->snake());
        }

        return $data;
    }
}
