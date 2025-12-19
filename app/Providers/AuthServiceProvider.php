<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

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
