<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfTokenForApi
{

    public function handle(Request $request, Closure $next): Response
    {
        $csrfEnabled = env('CSRF_ENABLED', false);
        $normalizedJson = '';
        
        if ($csrfEnabled) {
            if (!in_array($request->method(), ['GET', 'POST', 'PUT', 'DELETE'])) {
                return $next($request);
            }
            
            $routePrefix = $request->route()->uri;
            
            if (in_array($routePrefix, $this->ignoreRoutes())) {
                return $next($request);
            }

            $signature = $request->header('X-CSRF-TOKEN');
            if (!$signature) {
                return $this->sendError();
            }
            
            $parts = explode('|', $signature);

            if (count($parts) !== 2) {
                return $this->sendError();
            }
            
            [$csrfToken, $timestamp] = $parts;
            
            $timeExpiry = env('CSRF_TOKEN_EXPIRY_TIME', 5);
            
            if (!$timestamp || abs(time() - (int)($timestamp)) > $timeExpiry) {
                return $this->sendError();
            }
            
            //body data
            $data = json_decode($request->getContent(), true) ?: '{}';
            $normalizedJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            //params data
            $params = $request->query() ?: '{}';
            $normalizedParams = json_encode($params, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            //operation data
            $operation = strtolower($request->method());

            $requestString = "{$normalizedJson}|{$normalizedParams}|{$operation}";
            $encodedRequest = ($data == "{}") ? base64_encode($data) : base64_encode($normalizedJson);
            
            $dataWithTimestamp = "{$encodedRequest}|{$timestamp}";
            $expectedToken = hash_hmac('sha256', $dataWithTimestamp, env('CSRF_SECRET_KEY'));

            if (!hash_equals($expectedToken, $csrfToken)) {
                return $this->sendError();
            }
        }
        return $next($request);
    }

    private function sendError(): \Illuminate\Http\JsonResponse
    {
        return response()->json(['success' => false, 'message' => 'Invalid CSRF token'], 403);
    }

    private function ignoreRoutes(): array
    {
        return [
            'api/v1/getConfigurationInfo',
        ];
    }
}
