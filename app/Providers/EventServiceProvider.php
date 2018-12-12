<?php

namespace App\Providers;

use App\Events\DocumentCreated;
use App\Events\logHistory;
use App\Listeners\AfterDocumentCreated;
use App\Listeners\AfterLogin;
use App\Listeners\RevokeOldTokens;
use App\Models\AccessTokens;
use App\Models\ItemIssueMaster;
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
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],
        'Laravel\Passport\Events\AccessTokenCreated' => [
            'App\Listeners\RevokeOldTokens',
        ],
        'Laravel\Passport\Events\RefreshTokenCreated' => [
            'App\Listeners\PruneOldTokens',
        ],
        DocumentCreated::class => [
            AfterDocumentCreated::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        ItemIssueMaster::created(function (ItemIssueMaster $document) {
            //event(new DocumentCreated($document));
        });
    }
}
