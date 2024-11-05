<?php

namespace App\Handlers;

use App\Enums\LanguageSwitchPlacement;
use App\Events\LocaleChanged;
use Closure;
use Exception;
use Filament\FilamentManager;
use Filament\Panel;
use Filament\Support\Components\Component;
use Filament\Support\Facades\FilamentView;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Crypt;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Str;

class LanguageSwitch extends Component
{
    protected ?string $displayLocale = null;

    /** @var array<int, string>|Closure|null */
    protected array|Closure|null $outsidePanelRoutes = null;

    /** @var array<int, string>|Closure */
    protected array|Closure $excludes = [];

    /** @var array<int, string>|Closure */
    protected array|Closure $flags = [];

    protected bool|Closure $isCircular = false;

    protected bool $isFlagsOnly = false;

    /** @var array<int, string>|Closure */
    protected array|Closure $labels = [];

    /** @var array<int, string>|Closure */
    protected array|Closure $locales = [];

    protected bool $nativeLabel = false;

    protected ?LanguageSwitchPlacement $outsidePanelPlacement = null;

    protected bool|Closure $visibleInsidePanels = false;

    protected bool|Closure $visibleOutsidePanels = false;

    protected Closure|string $renderHook = 'panels::global-search.after';

    protected Closure|string|null $userPreferredLocale = null;

    public static function make(): static
    {
        $static = app(static::class);

        $static->visible();

        $static->displayLocale();

        $static->outsidePanelRoutes();

        $static->configure();

        return $static;
    }

    /**
     * @throws Exception
     */
    public static function boot(): void
    {
        $static = static::make();

        if ($static->isVisibleInsidePanels()) {
            FilamentView::registerRenderHook(
                name: $static->getRenderHook(),
                hook: fn (): string => Blade::render('<livewire:filament-language-switch key=\'fls-in-panels\' />')
            );
        }

        if ($static->isVisibleOutsidePanels()) {
            FilamentView::registerRenderHook(
                name: 'panels::body.start',
                hook: fn (): string => Blade::render('<livewire:filament-language-switch key=\'fls-outside-panels\' />')
            );
        }
    }

    public function circular(bool $condition = true): static
    {
        $this->isCircular = $condition;

        return $this;
    }

    public function displayLocale(?string $locale = null): static
    {
        $this->displayLocale = $locale ?? app()->getLocale();

        return $this;
    }

    public function nativeLabel(bool $condition = true): static
    {
        $this->nativeLabel = $condition;

        return $this;
    }

    /**
     * @param  list<string>|Closure|null  $routes
     * @return $this
     */
    public function outsidePanelRoutes(array|Closure|null $routes = null): static
    {
        $this->outsidePanelRoutes = $routes ?? [
            'auth.login',
            'auth.profile',
            'auth.register',
        ];

        return $this;
    }

    /**
     * @param  list<string>|Closure  $excludes
     * @return $this
     */
    public function excludes(array|Closure $excludes): static
    {
        $this->excludes = $excludes;

        return $this;
    }

    /**
     * @param  list<string>|Closure  $flags
     * @return $this
     */
    public function flags(array|Closure $flags): static
    {
        $this->flags = $flags;

        return $this;
    }

    public function flagsOnly(bool $condition = true): static
    {
        $this->isFlagsOnly = $condition;

        return $this;
    }

    /**
     * @param  list<string>|Closure  $labels
     * @return $this
     */
    public function labels(array|Closure $labels): static
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param  list<string>|Closure  $locales
     * @return $this
     */
    public function locales(array|Closure $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function outsidePanelPlacement(LanguageSwitchPlacement $placement): static
    {
        $this->outsidePanelPlacement = $placement;

        return $this;
    }

    public function renderHook(string $hook): static
    {
        $this->renderHook = $hook;

        return $this;
    }

    public function userPreferredLocale(Closure|string|null $locale): static
    {
        $this->userPreferredLocale = $locale;

        return $this;
    }

    public function visible(bool|Closure $insidePanels = true, bool|Closure $outsidePanels = false): static
    {
        $this->visibleInsidePanels = $insidePanels;

        $this->visibleOutsidePanels = $outsidePanels;

        return $this;
    }

    public function getDisplayLocale(): ?string
    {
        return $this->evaluate($this->displayLocale);
    }

    /**
     * @return list<string>
     */
    public function getExcludes(): array
    {
        return (array) $this->evaluate($this->excludes);
    }

    /**
     * @return list<string>
     *
     * @throws Exception
     */
    public function getFlags(): array
    {
        $flagUrls = (array) $this->evaluate($this->flags);

        foreach ($flagUrls as $url) {
            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception('Invalid flag url');
            }
        }

        return $flagUrls;
    }

    public function isCircular(): bool
    {
        return (bool) $this->evaluate($this->isCircular);
    }

    /**
     * @throws Exception
     */
    public function isFlagsOnly(): bool
    {
        return $this->evaluate($this->isFlagsOnly) && filled($this->getFlags());
    }

    /**
     * @throws Exception
     */
    public function isVisibleInsidePanels(): bool
    {
        /** @var list<string> $locales */
        $locales = $this->locales;

        return $this->evaluate($this->visibleInsidePanels)
            && count($locales) > 1
            && $this->isCurrentPanelIncluded();
    }

    /**
     * @throws Exception
     */
    public function isVisibleOutsidePanels(): bool
    {
        /** @var list<string> $outsidePanelRoutes */
        $outsidePanelRoutes = $this->evaluate($this->outsidePanelRoutes);

        return $this->evaluate($this->visibleOutsidePanels)
            && str(request()->route()->getName())->contains($outsidePanelRoutes)
            && $this->isCurrentPanelIncluded();
    }

    /**
     * @return list<string>
     */
    public function getLabels(): array
    {
        return (array) $this->evaluate($this->labels);
    }

    /**
     * @return list<string>
     */
    public function getLocales(): array
    {
        return (array) $this->evaluate($this->locales);
    }

    public function getNativeLabel(): bool
    {
        return (bool) $this->evaluate($this->nativeLabel);
    }

    public function getOutsidePanelPlacement(): LanguageSwitchPlacement
    {
        return $this->outsidePanelPlacement ?? LanguageSwitchPlacement::TopRight;
    }

    public function getRenderHook(): string
    {
        return (string) $this->evaluate($this->renderHook);
    }

    public function getUserPreferredLocale(): ?string
    {
        return $this->evaluate($this->userPreferredLocale) ?? null;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getPreferredLocale(): string
    {
        /** @var string|null $locale */
        $locale = request()->cookie('switch_locale');
        $localeFromCookie = is_null($locale)
            ? null
            : Str::of(Crypt::decryptString($locale))->after('|');

        $locale = session()->get('locale') ??
            request()->get('locale') ??
            $localeFromCookie?->toString() ??
            $this->getUserPreferredLocale() ??
            config('app.locale', 'id') ??
            request()->getPreferredLanguage();

        return in_array($locale, $this->getLocales(), true) ? $locale : config('app.locale');
    }

    /**
     * @return array<string, Panel>
     */
    public function getPanels(): array
    {
        /** @var FilamentManager $filament */
        $filament = filament();

        return collect($filament->getPanels())
            ->reject(fn (Panel $panel) => in_array($panel->getId(), $this->getExcludes()))
            ->toArray();
    }

    public function getCurrentPanel(): Panel
    {
        /** @var FilamentManager $filament */
        $filament = filament();

        return $filament->getCurrentPanel();
    }

    public function getFlag(string $locale): string
    {
        /** @var list<string> $flags */
        $flags = $this->evaluate($this->flags);

        return $flags[$locale] ?? str($locale)->upper()->toString();
    }

    /**
     * @throws Exception
     */
    public function getLabel(string $locale): string
    {
        /** @var list<string> $labels */
        $labels = $this->evaluate($this->labels);

        if (! is_array($labels)) {
            throw new Exception('Labels must fill in as array');
        }

        if (array_key_exists($locale, $labels) && ! $this->getNativeLabel()) {
            return $labels[$locale];
        }

        return str(
            (string) locale_get_display_name(
                locale: $locale,
                displayLocale: $this->getNativeLabel() ? $locale : $this->getDisplayLocale()
            )
        )
            ->title()
            ->toString();
    }

    /**
     * @throws Exception
     */
    public function isCurrentPanelIncluded(): bool
    {
        return array_key_exists($this->getCurrentPanel()->getId(), $this->getPanels());
    }

    public function getCharAvatar(string $locale): string
    {
        return str($locale)->length() > 2
            ? str($locale)->substr(0, 2)->upper()->toString()
            : str($locale)->upper()->toString();
    }

    public static function trigger(string $locale): Application|Redirector|RedirectResponse
    {
        session()->put('locale', $locale);

        cookie(raw: true)->queue(cookie()->forever('switch_locale', $locale));

        event(new LocaleChanged($locale));

        return redirect(request()->header('Referer'));
    }
}
