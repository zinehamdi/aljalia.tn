<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCountryIsSelected
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->country_id) {
            // Ignore if the current route is the onboarding route or livewire internal routes
            if (!$request->is('onboarding*') && !$request->is('livewire*')) {
                return redirect()->route('onboarding.country');
            }
        }

        return $next($request);
    }
}
