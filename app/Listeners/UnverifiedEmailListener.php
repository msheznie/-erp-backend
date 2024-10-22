<?php

namespace App\Listeners;

use App\Events\UnverifiedEmailEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Response;

class UnverifiedEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Response $response)
    {
        return $response;
    }

    /**
     * Handle the event.
     *
     * @param  UnverifiedEmailEvent  $event
     * @return void
     */
    public function handle(UnverifiedEmailEvent $event)
    {
       return $event;
    }
}
