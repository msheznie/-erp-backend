<?php

namespace App\Http\Middleware;
use App\Events\UnverifiedEmailEvent;
use Illuminate\Support\Facades\Event;
use Closure;

class NotVerifiedEmailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        Event::listen(UnverifiedEmailEvent::class, function ($event) {
            request()->attributes->set('unverified_email_data', $event->data);
        });

        $response = $next($request);

        if ($request->attributes->get('unverified_email_data')) {

            $additionalContent = [
                'unverifiedEmailMsg' => $request->attributes->get('unverified_email_data'),
            ];

            $originalContent = json_decode($response->getContent(), true);
            $response->setContent(json_encode(array_merge($originalContent, $additionalContent)));

            return $response;
        }

        return $response;
    }
}
