<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $sessionIp = $request->session()->get('last_ip');
            $currentIp = $request->ip();

            if ($sessionIp && $sessionIp !== $currentIp) {
                Log::info("Logging out user " . Auth::id() . " due to IP change from $sessionIp to $currentIp");
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'تم تسجيل الخروج لأن عنوان الـ IP تغير لدواعي أمنية.');
            }

            if (!$sessionIp) {
                $request->session()->put('last_ip', $currentIp);
            }
        }

        return $next($request);
    }
}
