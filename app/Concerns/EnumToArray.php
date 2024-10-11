<?php

namespace App\Concerns;

use BackedEnum;

/**
 * @mixin BackedEnum
 */
trait EnumToArray
{
    /**
     * @return array<string, string>
     */
    public static function toArray(): array
    {
        $data = [];

        foreach (static::cases() as $case) {
            $data[$value = (string) $case->value] = $value;
        }

        return $data;
    }
}
