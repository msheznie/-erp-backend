<?php

namespace App\Providers;

use App\Events\logHistory;
use App\Listeners\AfterLogin;
use App\Listeners\RevokeOldTokens;
use App\Models\AccessTokens;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
       /* 'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],*/
        logHistory::class => [
            AfterLogin::class,
            RevokeOldTokens::class
        ]
      /* 'App\Events\logHistory' =>[
            'App\Listeners\AfterLogin',
            'App\Listeners\RevokeOldTokens',
        ],*/
      /*  'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\RevokeOldTokens',
        ],

        'Laravel\Passport\Events\RefreshTokenCreated' => [
            'App\Listeners\PruneOldTokens',
        ],*/
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $acc = AccessTokens::where('user_id',2637)->orderBy('created_at')->first();

       AccessTokens::created(event(new logHistory($acc)));

        AccessTokens::created(function (AccessTokens $accessToken) {
            //event(new logHistory($accessToken));
        });

        /*AccessTokens::created(function (AccessTokens $model){
            //Log::info('Before Event Call');
            //Log::info($model);
           // event(new logHistory($model));
           // Log::info('After Event Call');
        });*/
    }
}
