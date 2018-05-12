<?php

namespace App\Providers;

use App\Events\logHistory;
use App\Listeners\AfterLogin;
use App\Listeners\RevokeOldTokens;
use App\Models\AccessTokens;
use App\Models\User;
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
        /* 'accessTokens.created' => [
              'App\Events\AccessToken@accessTokenCreated',
          ],*/
        /*   logHistory::class => [
             AfterLogin::class
             //RevokeOldTokens::class
           ],*/
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        /*'App\Events\logHistory' =>[
               'App\Listeners\AfterLogin'
               //'App\Listeners\RevokeOldTokens',
           ],*/
        'Laravel\Passport\Events\AccessTokenCreated' => [
            //'App\Listeners\AfterLogin'
            'App\Listeners\RevokeOldTokens', //should uncomment
        ],

        'Laravel\Passport\Events\RefreshTokenCreated' => [
           // 'App\Listeners\PruneOldTokens',  //should uncomment
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //$acc = AccessTokens::where('user_id', 2637)->orderBy('created_at', 'desc')->first();

        //AccessTokens::created(event(new logHistory($acc)));

        /* AccessTokens::created(function ($accessToken) {
            Log::info('Before Event Call');
            event(new logHistory($accessToken));
        });*/

    }
}
