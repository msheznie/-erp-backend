<?php

namespace App\Http\Middleware;

use App\Jobs\AuditLog\ThirdPartyApiSummaryLogJob;
use App\Models\ThirdPartyIntegrationKeys;
use App\Models\ThirdPartyApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ThirdPartyApiLogger
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        
        // Capture request data
        $requestData = [
            'headers' => $request->headers->all(),
            'body' => $request->all(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ];

        // Remove sensitive data from logging
        if (isset($requestData['headers']['authorization'])) {
            $requestData['headers']['authorization'] = ['[REDACTED]'];
        }

        // Get third party integration details from request BEFORE calling controller
        $thirdPartyIntegrationKeyId = $request->get('third_party_integration_key_id');
        $companyId = $request->get('company_id');
        
        // If not available in request, try to get from authentication header
        if (!$thirdPartyIntegrationKeyId) {
            $thirdPartyIntegrationKeyId = $this->getThirdPartyIntegrationKeyId($request);
        }

        // Get current user
        $user = Auth::user() ? Auth::user()->name : ($request->get('user_name') ?? 'system');
        
        // Get tenant UUID
        $tenantUuid = $request->get('tenant_uuid') ?? env('TENANT_UUID', 'local');
        
        // Get database connection
        $database = $request->get('db') ?? env('DB_DATABASE');


        $checkApi = DB::table('third_party_apis')->where('path', $request->path())->first();

        // Generate external reference from request payload
        $externalReference = $request->get('external_reference') ?? ThirdPartyApiLog::generateExternalReference();

        $logId = Str::random(32);

        if ($checkApi && $checkApi->webhook_enabled) {
            // Add tracking parameters to request for use in controllers BEFORE calling controller
            $request->request->add(['external_reference' => $externalReference]);
            $request->request->add(['log_id' => $logId]);
            $request->request->add(['tenant_uuid' => $tenantUuid]);
            $request->request->add(['webhook_url' => $checkApi->webhook_endpoint]);
        }

        // NOW call the controller with the modified request
        $response = $next($request);
        
        $endTime = microtime(true);
        $executionTime = round(($endTime - $startTime) * 1000, 2); // in milliseconds

        // Capture response data
        $responseData = [
            'status_code' => $response->getStatusCode(),
            'headers' => $response->headers->all()
        ];

        // Add response body if it's JSON
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $responseData['body'] = $response->getData(true);
        } elseif ($response instanceof Response) {
            $content = $response->getContent();
            if ($this->isJson($content)) {
                $responseData['body'] = json_decode($content, true);
            } else {
                $responseData['body'] = $content;
            }
        }

        // Dispatch the enhanced logging job (with database storage)
        if ($thirdPartyIntegrationKeyId) {
            ThirdPartyApiSummaryLogJob::dispatch(
                $database,
                $thirdPartyIntegrationKeyId,
                $tenantUuid,
                $request->path(),
                $request->method(),
                $requestData,
                $responseData,
                $response->getStatusCode(),
                $user,
                $executionTime,
                $externalReference,
                0,
                0,
                null,
                $logId
            );
        }

        return $response;
    }

    /**
     * Get third party integration key ID from authorization header
     */
    private function getThirdPartyIntegrationKeyId(Request $request)
    {
        $header = $request->header('Authorization');
        if (!$header) {
            return null;
        }

        $params = explode(' ', $header);
        if (count($params) !== 2) {
            return null;
        }

        $key = $params[0];
        $value = $params[1];
        
        try {
            $thirdPartyKey = ThirdPartyIntegrationKeys::whereHas('thirdPartySystem', function ($query) use ($key) {
                $query->where('description', $key);
            })->where('api_key', $value)->where('status', 'Active')->first();

            if ($thirdPartyKey) {
                return $thirdPartyKey->id;
            }
        } catch (\Exception $e) {
            // Log error but don't break the request
            \Log::error('Error getting third party integration key: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Check if string is valid JSON
     */
    private function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
} 