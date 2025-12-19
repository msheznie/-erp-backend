<?php

namespace App\Listeners;

use App\Events\UnverifiedEmailEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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
        $url = request()->getHttpHost();
        $url_array = explode('.', $url);
        $subDomain = $url_array[0];

        $tenant = Cache::get('tenant_'.$subDomain)['uuid'];

        $token = request()->header('Authorization');
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }


        if(($tenant === $event->tenant) && ($token === $event->token) && (Auth::user()->employee->empCompanySystemID === $event->companyID))
        {
//            request()->attributes->set('unverified_email_data', $event->data);
        }

        return $event;
    }
}
