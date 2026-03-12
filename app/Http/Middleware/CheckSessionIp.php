<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionIp
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $sessionIp = $request->session()->get('last_ip');
            $currentIp = $request->ip();

            if ($sessionIp && ! $this->isSameNetwork($sessionIp, $currentIp)) {
                Log::info('Logging out user '.Auth::id()." due to network change from $sessionIp to $currentIp");
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }

            // Update IP on each request (allows minor IP changes within same network)
            $request->session()->put('last_ip', $currentIp);
        }

        return $next($request);
    }

    /**
     * Check if two IPs are on the same /24 network.
     * This allows minor IP changes (DHCP renewal) while blocking
     * access from completely different networks.
     */
    private function isSameNetwork(string $ip1, string $ip2): bool
    {
        $parts1 = explode('.', $ip1);
        $parts2 = explode('.', $ip2);

        if (count($parts1) < 3 || count($parts2) < 3) {
            return false;
        }

        // Compare first 3 octets (same /24 subnet)
        return $parts1[0] === $parts2[0]
            && $parts1[1] === $parts2[1]
            && $parts1[2] === $parts2[2];
    }
}
