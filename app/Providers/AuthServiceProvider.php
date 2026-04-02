<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Auth;
use App\Auth\Guards\KeycloakGuard;
use App\Services\BudgetPermissionService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Budget planning permission gate (used by multiple budget planning endpoints)
        Gate::define('BudgetPlanningUserPermissionGate', function (
            $user,
            $companyId,
            $departmentBudgetPlanningDetailID,
            $delegateUser = null,
            $entryID = null,
            $checkInputEditSave = false
        ) {
            $delegateUser = $delegateUser ?? ($user?->employee_id);

            $result = app(BudgetPermissionService::class)->getBudgetPlanningUserPermissions([
                'companyId' => $companyId,
                'departmentBudgetPlanningDetailID' => $departmentBudgetPlanningDetailID,
                'delegateUser' => $delegateUser,
            ]);

            if (empty($result) || empty($result['success']) || $result['success'] !== true) {
                throw new AuthorizationException('User permissison not exists');
            }

            $delegateUserPermission = $result['data']['delegateUser'] ?? null;

            if (
                isset($delegateUserPermission) &&
                ($delegateUserPermission['status'] ?? false) === true &&
                ($delegateUserPermission['isActive'] ?? true) === false
            ) {
                throw new AuthorizationException('Delegate is not active');
            }

            // Extra checks for "save" endpoints (input/edit/save permissions)
            if ($checkInputEditSave === true) {
                if (isset($delegateUserPermission) && ($delegateUserPermission['status'] ?? false) === true) {
                    $access = $delegateUserPermission['access'] ?? [];

                    // BudgetPermissionService can return `access` as a Laravel Collection.
                    // Normalize to a plain array so array_key_exists / indexing won't error.
                    if ($access instanceof \Illuminate\Support\Collection) {
                        $access = $access->toArray();
                    } elseif (is_object($access) && method_exists($access, 'toArray')) {
                        $access = $access->toArray();
                    }

                    if (!empty($access) && array_key_exists('input', $access) && $access['input'] === false) {
                        throw new AuthorizationException("User doesn't have permission to input data");
                    }

                    if (
                        !empty($access) &&
                        empty($access['edit_input'] ?? null) &&
                        !empty($entryID)
                    ) {
                        throw new AuthorizationException("User doesn't have permission to edit data");
                    }
                }

                $financeApprovalStatus = $result['data']['financeApprovalUser']['status'] ?? false;
                $financeStatus = $result['data']['financeUser']['status'] ?? false;

                if ($financeApprovalStatus === true || $financeStatus === true) {
                    throw new AuthorizationException("User doesn't have permission to save data");
                }
            }

            return true;
        });

        // Register custom Keycloak guard
        Auth::extend('keycloak', function ($app, $name, array $config) {
            return new KeycloakGuard(
                Auth::createUserProvider($config['provider']),
                $app['request'],
                $config
            );
        });

        if ($this->app->resolved(\League\OAuth2\Server\AuthorizationServer::class)) {
            $server = $this->app->make(\League\OAuth2\Server\AuthorizationServer::class);
            $server->enableGrantType(new \Laravel\Passport\Bridge\PersonalAccessGrant(), new \DateInterval('PT12H'));
        } else {
            $this->app->afterResolving(\League\OAuth2\Server\AuthorizationServer::class, function ($server) {
                $server->enableGrantType(new \Laravel\Passport\Bridge\PersonalAccessGrant(), new \DateInterval('PT12H'));
            });
        }
    }
}
