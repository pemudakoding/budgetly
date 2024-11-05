<?php

namespace App\Events;

class LocaleChanged
{
    public function __construct(
        public string $locale
    ) {}
}
