<?php

namespace App\ValueObjects;

use Exception;

class Money
{
    public int|float $value;

    /**
     * @throws Exception
     */
    public function __construct(private int|float|string $amount)
    {
        // will check the string
        // 123,123.345,-1234,-123.456
        if (is_string($this->amount) && ! preg_match('/^-?[0-9.,]+$/', $this->amount)) {
            throw new Exception('Invalid string, string contains alphabet');
        }
        if (is_string($this->amount)) {
            $this->value = preg_match('/\.\d+/', $this->amount)
                ? (float) $this->amount
                : (int) $this->amount;

            return;
        }

        $this->value = $this->amount;
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
}
