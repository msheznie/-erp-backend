<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExtractHeadersFromBody
{
    public function handle(Request $request, Closure $next)
    {
        // Decode JSON body
        $data = json_decode($request->getContent(), true);

        // Check if 'headers' exist in the body
        if (isset($data['headers'])) {
            foreach ($data['headers'] as $key => $value) {
                // Set headers from request body
                $request->headers->set($key, $value);
            }

            // Remove 'headers' key from the request body to prevent processing issues
            unset($data['headers']);

            // Replace the request content with the modified data
            $request->replace($data);
        }

        return $next($request);
    }
}
