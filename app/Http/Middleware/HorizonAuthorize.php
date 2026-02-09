<?php

namespace App\Http\Middleware;

use App\Models\EmployeeNavigation;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class HorizonAuthorize
{
    /**
     * Handle an incoming request for Horizon dashboard.
     * Supports multiple authentication methods for API-only backend.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow all access in local environment
        if (app()->environment('local')) {
            return $next($request);
        }

        if ($this->attemptBasicAuth($request)) {
            return $next($request);
        }

        // if ($this->isWhitelistedIp($request)) {
        //     return $next($request);
        // }

        // Deny access - return 401 with Basic Auth challenge
        return response('Unauthorized', 401, [
            'WWW-Authenticate' => 'Basic realm="Horizon Dashboard"',
        ]);
    }

    /**
     * Attempt to authenticate using HTTP Basic Auth
     */
    protected function attemptBasicAuth(Request $request): bool
    {
        $username = $request->getUser();
        $password = $request->getPassword();

        if (! $username || ! $password) {
            return false;
        }

        // Check against super admin credentials from env
        $horizonUsername = env('HORIZON_USERNAME');
        $horizonPassword = env('HORIZON_PASSWORD');

        if ($horizonUsername && $horizonPassword) {
            if ($username === $horizonUsername && $password === $horizonPassword) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the request IP is whitelisted
     */
    protected function isWhitelistedIp(Request $request): bool
    {
        $whitelistedIps = explode(',', env('HORIZON_ALLOWED_IPS', ''));
        $whitelistedIps = array_filter(array_map('trim', $whitelistedIps));

        if (empty($whitelistedIps)) {
            return false;
        }

        $requestIp = $request->ip();

        foreach ($whitelistedIps as $allowedIp) {
            // Support CIDR notation and wildcards
            if ($this->ipMatch($requestIp, $allowedIp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP matches the pattern (supports wildcards)
     */
    protected function ipMatch(string $ip, string $pattern): bool
    {
        if ($ip === $pattern) {
            return true;
        }

        // Simple wildcard support (e.g., 192.168.1.*)
        $pattern = str_replace('.', '\.', $pattern);
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = '/^'.$pattern.'$/';

        return (bool) preg_match($pattern, $ip);
    }
}
