<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ValidateSignedPdfUrl
{
    /**
     * Handle an incoming request for signed PDF URLs
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->get('token');
        $signature = $request->get('signature');
        $expires = $request->get('expires');

        if (!$token || !$signature || !$expires) {
            return response()->json(['error' => 'Missing signed URL parameters'], 403);
        }

        // Check if URL has expired
        if (time() > $expires) {
            return response()->json(['error' => 'Signed URL has expired'], 403);
        }

        // Retrieve the signed URL data from cache
        $urlData = Cache::get("signed_pdf_url:{$token}");
        
        if (!$urlData) {
            return response()->json(['error' => 'Invalid or expired signed URL'], 403);
        }

        // Check if URL was already used (for single-use tokens)
        if (isset($urlData['used_at']) && $urlData['used_at']) {
            // Allow reuse within a short window (2 minutes) for browser byte-range requests
            $usedAt = $urlData['used_at'];
            if (time() - $usedAt > 120) { // 2 minutes grace period
                Cache::forget("signed_pdf_url:{$token}");
                return response()->json(['error' => 'Signed URL has already been used'], 403);
            }
        }

        // Verify the signature
        $expectedSignature = $this->generateSignature($token, $expires, $urlData);
        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        // Mark as used if this is the first access
        if (!isset($urlData['used_at']) || !$urlData['used_at']) {
            $urlData['used_at'] = time();
            // Update cache with used timestamp and reduce TTL to 2 minutes
            Cache::put("signed_pdf_url:{$token}", $urlData, 2); // 2 minutes remaining
        }

        // Add the original parameters to the request
        $request->merge($urlData['params']);
        
        // Set the route name for the controller to use
        $request->attributes->add(['pdf_route' => $urlData['route']]);

        // Set user context if available
        if (isset($urlData['user_id'])) {
            $request->attributes->add(['authenticated_user_id' => $urlData['user_id']]);
        }

        return $next($request);
    }

    /**
     * Generate signature for URL validation
     *
     * @param string $token
     * @param int $expires
     * @param array $urlData
     * @return string
     */
    private function generateSignature($token, $expires, $urlData)
    {
        $payload = $token . $expires . serialize($urlData);
        return hash_hmac('sha256', $payload, config('app.key'));
    }
}
