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

        // Method 1: Check for Basic Auth credentials
        if ($this->attemptBasicAuth($request)) {
            return $next($request);
        }

        // Method 2: Check for Bearer token in Authorization header
        if ($this->attemptTokenAuth($request)) {
            return $next($request);
        }

        // Method 3: Check for token in query parameter (for direct links)
        if ($this->attemptQueryTokenAuth($request)) {
            return $next($request);
        }

        // Method 4: Check IP whitelist
        if ($this->isWhitelistedIp($request)) {
            return $next($request);
        }

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

        // Try to authenticate against database users
        $user = User::where('email', $username)->first();

        if ($user && \Hash::check($password, $user->password)) {
            return $this->isAuthorizedUser($user);
        }

        return false;
    }

    /**
     * Attempt to authenticate using Bearer token from Authorization header
     */
    protected function attemptTokenAuth(Request $request): bool
    {
        $token = $request->bearerToken();

        if (! $token) {
            return false;
        }

        return $this->validateToken($token);
    }

    /**
     * Attempt to authenticate using token from query parameter
     */
    protected function attemptQueryTokenAuth(Request $request): bool
    {
        $token = $request->query('token');

        if (! $token) {
            return false;
        }

        return $this->validateToken($token);
    }

    /**
     * Validate an API token and check user authorization
     */
    protected function validateToken(string $token): bool
    {
        // Find the token in passport tokens
        $accessToken = Token::where('id', $token)
            ->where('revoked', 0)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $accessToken) {
            return false;
        }

        // Get the user associated with the token
        $user = User::find($accessToken->user_id);

        if (! $user) {
            return false;
        }

        return $this->isAuthorizedUser($user);
    }

    /**
     * Check if user is authorized to access Horizon
     */
    protected function isAuthorizedUser(User $user): bool
    {
        // Check if user email is in super admin list
        $superAdminEmails = explode(',', env('HORIZON_SUPER_ADMINS', ''));
        $superAdminEmails = array_filter(array_map('trim', $superAdminEmails));

        if (in_array($user->email, $superAdminEmails, true)) {
            return true;
        }

        // Check if user has super admin user type
        if ($user->user_type && $user->user_type->isProductSuperAdmin) {
            return true;
        }

        // Check if user has specific permission via user groups
        $employeeSystemID = $user->employee_id;
        if ($employeeSystemID) {
            $hasHorizonAccess = EmployeeNavigation::where('employeeSystemID', $employeeSystemID)
                ->whereHas('usergroup', function ($query) {
                    $query->where('userGroupName', 'System Administrator')
                        ->orWhere('userGroupName', 'Super Admin')
                        ->orWhere('slug', 'system-admin');
                })
                ->exists();

            if ($hasHorizonAccess) {
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
