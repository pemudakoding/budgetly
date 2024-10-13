<?php

namespace App\Filament\Forms;

use App\ValueObjects\Money;
use Filament\Forms\Components\TextInput;
use Filament\Support\RawJs;

class MoneyInput extends TextInput
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->mask(RawJs::make('$money($input, \',\', \'.\')'));
        $this->prefix('Rp.');
        $this->dehydrateStateUsing(fn (?string $state) => Money::makeFromFilamentMask($state)->value);
    }
}
