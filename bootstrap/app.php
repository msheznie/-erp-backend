<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
// Keycloak exceptions are now handled in custom guard
// use KeycloakGuard\Exceptions\TokenException;
// use KeycloakGuard\Exceptions\UserNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // API routes with custom prefix and namespace (from RouteServiceProvider)
            Route::prefix('api/v1')
                ->as('api.')
                ->namespace('App\Http\Controllers\API')
                ->group(base_path('routes/api.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Global middleware
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\PreflightResponse::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // Trust proxies configuration
        // Note: We can't use config() here as config service isn't bootstrapped yet
        // Read config file directly or use environment variables
        $trustedProxyConfig = require __DIR__.'/../config/trustedproxy.php';
        $proxies = $trustedProxyConfig['proxies'] ?? null;
        $headers = $trustedProxyConfig['headers'] ?? (
            \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
            \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
        );
        
        if ($proxies === '*') {
            $middleware->trustProxies(at: '*', headers: $headers);
        } elseif (is_array($proxies) && !empty($proxies)) {
            $middleware->trustProxies(at: $proxies, headers: $headers);
        } else {
            // Default: trust all proxies if not specified
            $middleware->trustProxies(at: '*', headers: $headers);
        }

        // Web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // API middleware group
        $middleware->api(prepend: [
            'throttle:10,1',
            'auth:api',
            'bindings',
        ]);

        // Alias middleware
        $middleware->alias([
            'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'corsFree' => \App\Http\Middleware\CorsFree::class,
            'securityHeader' => \App\Http\Middleware\SecurityHeader::class,
            'tenant' => \App\Http\Middleware\TenantEnforce::class,
            'authorization' => \App\Http\Middleware\UserAuthorization::class,
            'tenantById' => \App\Http\Middleware\TenantByKey::class,
            'locale' => \App\Http\Middleware\DetectLocale::class,
            'max_memory_limit' => \App\Http\Middleware\MaxMemoryLimit::class,
            'max_execution_limit' => \App\Http\Middleware\MaxExecutionLimit::class,
            'access_token' => \App\Http\Middleware\AccessToken::class,
            'thirdPartyApis' => \App\Http\Middleware\PosApi::class,
            'thirdPartyApiLogger' => \App\Http\Middleware\ThirdPartyApiLogger::class,
            'print_lang' => \App\Http\Middleware\DetectPrintLang::class,
            'signed_pdf_url' => \App\Http\Middleware\ValidateSignedPdfUrl::class,
            'hrms_employee' => \App\Http\Middleware\DetectHRMSEmployee::class,
            'mobileAccess' => \App\Http\Middleware\MobileAccessVerify::class,
            'auth.api.keycloak' => \App\Http\Middleware\EitherAuthAPIorKeyClock::class,
            'mobileServer' => \App\Http\Middleware\MobileServer::class,
            'checkNotVerifiedEmail' => \App\Http\Middleware\NotVerifiedEmailMiddleware::class,
            'csrf.api' => \App\Http\Middleware\VerifyCsrfTokenForApi::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Exception types that should not be reported
        $exceptions->dontReport([
            \League\OAuth2\Server\Exception\OAuthServerException::class,
        ]);

        // Inputs that should never be flashed for validation exceptions
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
        ]);

        // Custom exception handling
        $exceptions->render(function (Throwable $e, Request $request) {
            // Keycloak authentication errors are now handled in the guard
            // No need for specific exception handling

            // Authentication exception
            if ($e instanceof AuthenticationException) {
                return response()->json([
                    'errors' => [
                        'status' => 401,
                        'message' => 'Unauthenticated',
                    ]
                ], 401);
            }

            // Other exceptions (non-HTTP, non-Auth, non-Validation)
            if (!($e instanceof HttpException || $e instanceof AuthenticationException || $e instanceof ValidationException)) {
                if (!config('app.debug')) {
                    return response()->json(['message' => 'Something went wrong. Please contact system administrator'], 500);
                }
            }
        });

        // Report exceptions to Sentry
        $exceptions->report(function (Throwable $e) {
            if ($e instanceof \League\OAuth2\Server\Exception\OAuthServerException && $e->getCode() == 9) {
                return false; // Don't report this exception
            }
            
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Scheduled commands from Console/Kernel.php
        $schedule->command('command:newPR')->daily()->withoutOverlapping();
        $schedule->command('command:queuework')->everyMinute()->withoutOverlapping();
        $schedule->command('invoiceDueReminder')->daily()->withoutOverlapping();
        $schedule->command('notification_service')->daily()->withoutOverlapping();
        $schedule->command('leave_accrual_schedule')->daily()->withoutOverlapping();
        $schedule->command('financialPeriodActivation')->daily()->withoutOverlapping();
        $schedule->command('itemWACAmountPost')->daily()->withoutOverlapping();
        $schedule->command('command:recurringVoucher')->daily()->withoutOverlapping();
        $schedule->command('command:reversePoAccrual')->daily()->withoutOverlapping();
        $schedule->command('command:delegationActive')->daily()->withoutOverlapping();
        $schedule->command('command:codeConfigEdit')->hourly()->withoutOverlapping();
        $schedule->command('command:checkb2bstatus')->everyFifteenMinutes()->withoutOverlapping();
        $schedule->command('auth:detect-expired-tokens')->hourly()->withoutOverlapping();

        $schedule->command('pull-attendance')
            ->timezone('Asia/Muscat')
            ->dailyAt('00:30')
            ->withoutOverlapping();

        $schedule->command('pull-cross-day-attendance')
            ->timezone('Asia/Muscat')
            ->dailyAt('12:30')
            ->withoutOverlapping();

        $schedule->command('command:forgotToPunchIn')
            ->timezone('Asia/Muscat')
            ->hourly()
            ->between('8:00', '13:00')
            ->withoutOverlapping();

        $schedule->command('command:forgotToPunchOut')
            ->timezone('Asia/Muscat')
            ->dailyAt('07:00')
            ->withoutOverlapping();

        $schedule->command('command:attendanceDailySummary')
            ->timezone('Asia/Muscat')
            ->dailyAt('07:00')
            ->withoutOverlapping();

        $schedule->command('command:attendanceWeeklySummary')
            ->timezone('Asia/Muscat')
            ->weeklyOn(5, '09:00')
            ->withoutOverlapping();

        $schedule->command('command:birthday_wish_schedule')
            ->timezone('Asia/Muscat')
            ->dailyAt('02:00')
            ->withoutOverlapping();

        $schedule->command('command:leaveCarryForwardComputationSchedule')
            ->timezone('Asia/Muscat')
            ->dailyAt('21:00')
            ->withoutOverlapping();

        $schedule->command('command:AbsentNotificationNonCrossDay')
            ->timezone('Asia/Muscat')
            ->hourly()
            ->between('12:00', '23:59')
            ->withoutOverlapping();

        $schedule->command('command:AbsentNotificationCrossDay')
            ->timezone('Asia/Muscat')
            ->hourly()
            ->between('00:00', '12:00')
            ->withoutOverlapping();

        $schedule->command('command:budgetDeadlineNotification')
            ->timezone('Asia/Muscat')
            ->dailyAt('00:00')
            ->withoutOverlapping();

        $schedule->command('command:budgetSubmissionDeadlineReachedNotification')
            ->timezone('Asia/Muscat')
            ->dailyAt('00:00')
            ->withoutOverlapping();
    })
    ->create();
