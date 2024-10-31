<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToUnfinishedOnboardingStep
{
    /** @var string[] */
    private array $ignoredPath = [
        'logout',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()->onboarding()->inProgress() && ! in_array($request->path(), $this->ignoredPath)) {
            return redirect()->to(
                auth()->user()->onboarding()->nextUnfinishedStep()->attribute('link')
            );
        }

        return $next($request);
    }
}
