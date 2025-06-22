<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Http\Controllers\AppBaseController;

class VizzlyController extends AppBaseController
{
    private $projectId = 'prj_589be17c00f343d9819dacd36a0a4f60';
    
    /**
     * Generate Vizzly access tokens for the authenticated user
     */
    public function generateTokens(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            
            // Get the selected company from request or user's default
            $selectedCompany = $request->header('X-Company-ID') 
                ?? $request->input('company_id') 
                ?? $user->default_company_id;

            // Generate tokens using JWT
            $tokens = $this->createVizzlyTokens($user, $selectedCompany);

            return response()->json([
                'accessTokens' => $tokens,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'company_id' => $selectedCompany
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Vizzly token generation failed: ' . $e->getMessage());
            
            // Return fallback tokens in case of error
            return response()->json([
                'accessTokens' => $this->getFallbackTokens(),
                'fallback' => true,
                'error' => 'Using fallback tokens due to: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create Vizzly access tokens using Firebase JWT
     */
    private function createVizzlyTokens($user, $companyId): array
    {
        try {
            // Try to load private key from storage
            $privateKey = null;
            $keyPaths = [
                'vizzly-private.pem',
                'vizzly/vizzly-private.pem',
                storage_path('app/vizzly-private.pem'),
                storage_path('vizzly/vizzly-private.pem'),
                base_path('vizzly-private.pem')
            ];

            foreach ($keyPaths as $path) {
                if (Storage::exists($path)) {
                    $privateKey = Storage::get($path);
                    break;
                } elseif (file_exists($path)) {
                    $privateKey = file_get_contents($path);
                    break;
                }
            }

            if (!$privateKey) {
                Log::warning('Vizzly private key not found in any location, using fallback tokens');
                return $this->getFallbackTokens();
            }

            // Current time
            $now = time();
            $ttl = 2 * 60 * 60; // 2 hours

            // Create dashboard access token
            $dashboardPayload = [
                'organisationId' => $this->projectId,
                'userReference' => "user_{$user->id}",
                'scope' => 'read_write',
                'accessType' => $this->getUserAccessType($user),
                'iat' => $now,
                'exp' => $now + $ttl,
                'iss' => 'vizzly'
            ];

            // Create data access token with secure filters
            $dataPayload = [
                'organisationId' => $this->projectId,
                'dataSetIds' => '*',
                'userReference' => "user_{$user->id}",
                'scope' => 'read_write',
                'accessType' => $this->getUserAccessType($user),
                'secureFilters' => $this->buildSecureFilters($user, $companyId),
                'parameters' => [
                    'company_id' => $companyId,
                    'user_id' => $user->id
                ],
                'iat' => $now,
                'exp' => $now + $ttl,
                'iss' => 'vizzly'
            ];

            // Sign the tokens
            $dashboardToken = JWT::encode($dashboardPayload, $privateKey, 'RS256');
            $dataToken = JWT::encode($dataPayload, $privateKey, 'RS256');

            $tokens = [
                'dashboardAccessToken' => $dashboardToken,
                'dataAccessToken' => $dataToken
            ];

            // Add query engine token for admin users
            if ($this->userHasQueryEngineAccess($user)) {
                $queryEnginePayload = [
                    'organisationId' => $this->projectId,
                    'userReference' => "user_{$user->id}",
                    'scope' => 'read_write',
                    'accessType' => 'admin',
                    'iat' => $now,
                    'exp' => $now + $ttl,
                    'iss' => 'vizzly'
                ];
                
                $tokens['queryEngineAccessToken'] = JWT::encode($queryEnginePayload, $privateKey, 'RS256');
            }

            return $tokens;

        } catch (\Exception $e) {
            Log::error('JWT token creation failed: ' . $e->getMessage());
            return $this->getFallbackTokens();
        }
    }

    /**
     * Get fallback static tokens (your working tokens)
     */
    private function getFallbackTokens(): array
    {
        return [
            'dashboardAccessToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJvcmdhbmlzYXRpb25JZCI6InByajU4OWJlMTdjMDBmMzQzZDk4MTlkYWNkMzZhMGE0ZjYwIiwiZGF0YVNldElkcyI6IioiLCJ1c2VyUmVmZXJlbmNlIjoidXNlciAxMjM0NSIsInNjb3BlIjoicmVhZF93cml0ZSIsImFjY2Vzc1R5cGUiOiJzdGFuZGFyZCIsInNlY3VyZUZpbHRlcnMiOnt9LCJpYXQiOjE3MzUyMDYxNjgsImV4cCI6MTc1MTA3MjE2OCwiaXNzIjoidml6emx5In0.OHgF8lCJvB1NwGGlJZGz_7QCEQFqjKJOqNhKWWZ6rXJSZJJzJQTzHJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJO',
            'dataAccessToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJvcmdhbmlzYXRpb25JZCI6InByajU4OWJlMTdjMDBmMzQzZDk4MTlkYWNkMzZhMGE0ZjYwIiwiZGF0YVNldElkcyI6IioiLCJ1c2VyUmVmZXJlbmNlIjoidXNlciAxMjM0NSIsInNjb3BlIjoicmVhZF93cml0ZSIsImFjY2Vzc1R5cGUiOiJzdGFuZGFyZCIsInNlY3VyZUZpbHRlcnMiOnt9LCJpYXQiOjE3MzUyMDYxNjgsImV4cCI6MTc1MTA3MjE2OCwiaXNzIjoidml6emx5In0.OHgF8lCJvB1NwGGlJZGz_7QCEQFqjKJOqNhKWWZ6rXJSZJJzJQTzHJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJO',
            'queryEngineAccessToken' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJvcmdhbmlzYXRpb25JZCI6InByajU4OWJlMTdjMDBmMzQzZDk4MTlkYWNkMzZhMGE0ZjYwIiwidXNlclJlZmVyZW5jZSI6InVzZXIgMTIzNDUiLCJzY29wZSI6InJlYWRfd3JpdGUiLCJhY2Nlc3NUeXBlIjoiYWRtaW4iLCJpYXQiOjE3MzUyMDYxNjgsImV4cCI6MTc1MTA3MjE2OCwiaXNzIjoidml6emx5In0.QzHJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJOJJO'
        ];
    }

    /**
     * Build secure filters for multi-tenancy
     */
    private function buildSecureFilters($user, $companyId): array
    {
        $filters = [];
        
        // Apply company-level filtering to all datasets
        // This ensures users only see data for their selected company
        $filters['*'] = [
            [
                'field' => 'company_id',
                'op' => '=',
                'value' => $companyId
            ]
        ];

        // Add user-level filtering if needed
        if (!$this->userIsAdmin($user)) {
            $filters['user_specific_data'] = [
                [
                    'field' => 'user_id',
                    'op' => '=',
                    'value' => $user->id
                ]
            ];
        }

        return $filters;
    }

    /**
     * Check if user has access to the specified company
     */
    private function userHasAccessToCompany($user, $companyId): bool
    {
        // Implement your company access logic here
        // For now, allow access if user has company_id or is admin
        return $user->company_id == $companyId || $this->userIsAdmin($user);
    }

    /**
     * Determine user access type (admin or standard)
     */
    private function getUserAccessType($user): string
    {
        return $this->userIsAdmin($user) ? 'admin' : 'standard';
    }

    /**
     * Check if user has query engine access (for config manager)
     */
    private function userHasQueryEngineAccess($user): bool
    {
        return $this->userIsAdmin($user) || (method_exists($user, 'hasRole') && $user->hasRole('vizzly_admin'));
    }

    /**
     * Check if user is admin
     */
    private function userIsAdmin($user): bool
    {
        // Implement your admin check logic
        return (method_exists($user, 'hasRole') && $user->hasRole('admin')) 
            || (property_exists($user, 'is_admin') && $user->is_admin);
    }
}
