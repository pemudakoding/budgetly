<?php

namespace App\Livewire;

use App\Handlers\LanguageSwitch;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class FilamentLanguageSwitch extends Component
{
    #[On('language-switched')]
    public function changeLocale(string $locale): void
    {
        LanguageSwitch::trigger(locale: $locale);
    }

    public function render(): View
    {
        return view('livewire.filament-language-switch');
    }
}
