<?php

namespace App\Http\Middleware;
use App\Events\UnverifiedEmailEvent;
use App\Models\Tenant;
use Illuminate\Support\Facades\Event;
use Closure;
use Illuminate\Support\Facades\Cache;

class NotVerifiedEmailMiddleware
{
    private $tenantData;
    private $event;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if ($request->isMethod('put')) {
            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];

            $this->subDomain = $subDomain;
            if ($subDomain != 'localhost:8000') {
                if (!$subDomain) {
                    return "Subdomain not found";
                }

                $tenant = Tenant::where('sub_domain', 'like', $subDomain)->first();

                if ($tenant) {
                    $tenantData = [
                        'db' => $tenant->database,
                        'uuid' => $tenant->uuid
                    ];
                    $this->tenantData = $tenantData;
                    Cache::put('tenant_' . $subDomain, $tenantData, 60 * 60); // Cache for 1 hour
                } else {
                    return "Tenant not found";
                }
            } else {
                $tenantData = [
                    'db' => "",
                    'uuid' => ""
                ];
                $this->tenantData = $tenantData;
                Cache::put('tenant_' . $subDomain, $tenantData, 60 * 60); // Cache for 1 hour
            }
        }


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
