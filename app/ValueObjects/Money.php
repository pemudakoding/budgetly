<?php

namespace App\ValueObjects;

use Exception;
use Illuminate\Support\Number;

class Money
{
    public int|float $value;

    /**
     * @throws Exception
     */
    public function __construct(int|float|string $amount)
    {
        // will check the string
        // 123,123.345,-1234,-123.456
        if (is_string($amount) && ! preg_match('/^-?[0-9.,]+$/', $amount)) {
            throw new Exception('Invalid string, string contains alphabet');
        }
        if (is_string($amount)) {
            $this->value = preg_match('/\.\d+/', $amount)
                ? (float) $amount
                : (int) $amount;

            return;
        }

        $this->value = $amount;
    }

    /**
     * @throws Exception
     */
    public static function make(int|float|string $amount): Money
    {
        return new self($amount);
    }

    /**
     * @throws Exception
     */
    public static function makeFromFilamentMask(int|float|string $amount, string $separator = '.', string $decimalSeparator = ','): Money
    {
        if (is_string($amount)) {
            /**
             * Replace million thousand and hundred to be flat number
             * and replace decimal separator to dot
             */
            $amount = str_replace($separator, '', $amount);
            $amount = str_replace($decimalSeparator, '.', $amount);
        }

        return new self($amount);
    }

    public static function format(int|float $amount): string
    {
        /** @var string $amount */
        $amount = Number::currency($amount, 'IDR', 'ID');

        return $amount;
    }
}
