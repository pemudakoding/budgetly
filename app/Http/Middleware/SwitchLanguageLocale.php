<?php

namespace App\Http\Middleware;

use App\Handlers\LanguageSwitch;
use Closure;
use Illuminate\Http\Request;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class SwitchLanguageLocale
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(Request $request, Closure $next): mixed
    {
        app()->setLocale(
            locale: LanguageSwitch::make()->getPreferredLocale()
        );

        return $next($request);
    }
}
