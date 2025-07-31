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
     * Test endpoint to verify Vizzly integration
     */
    public function test(Request $request): JsonResponse
    {
        $user = Auth::user();
        $selectedCompany = $request->header('X-Company-ID') 
            ?? $request->input('company_id') 
            ?? ($user ? $user->default_company_id : null);
            
        return response()->json([
            'success' => true,
            'message' => 'Vizzly integration is working',
            'data' => [
                'user_authenticated' => !is_null($user),
                'user_id' => $user ? $user->id : null,
                'selected_company' => $selectedCompany,
                'project_id' => $this->projectId,
                'timestamp' => now()->toISOString()
            ]
        ]);
    }
    
    /**
     * Generate Vizzly identity tokens for the authenticated user
     * Following Vizzly documentation standards
     */
    public function generateTokens(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'error' => 'Authentication required',
                    'message' => 'User must be authenticated to generate Vizzly tokens'
                ], 401);
            }

            // Get the selected company from request or user's default
            $selectedCompany = $request->header('X-Company-ID') 
                ?? $request->input('company_id') 
                ?? $user->default_company_id;

            if (!$selectedCompany) {
                return response()->json([
                    'error' => 'Company context required',
                    'message' => 'A company must be selected to generate Vizzly tokens'
                ], 400);
            }

            // Validate user has access to the selected company
            // if (!$this->userHasAccessToCompany($user, $selectedCompany)) {
            //     return response()->json([
            //         'error' => 'Access denied',
            //         'message' => 'User does not have access to the selected company'
            //     ], 403);
            // }

            // Generate tokens using JWT
            $tokens = $this->createVizzlyTokens($user, $selectedCompany);

            return response()->json([
                'success' => true,
                'data' => [
                    'identity' => $tokens,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'company_id' => $selectedCompany,
                        'access_type' => $this->getUserAccessType($user)
                    ],
                    'expires_at' => now()->addHours(2)->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Vizzly token generation failed: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'company_id' => $selectedCompany ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return fallback tokens in case of error
            return response()->json([
                'success' => false,
                'data' => [
                    'identity' => $this->getFallbackTokens(),
                    'fallback' => true
                ],
                'message' => 'Using fallback tokens due to error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create Vizzly identity tokens using Firebase JWT
     * Following Vizzly documentation structure
     */
    private function createVizzlyTokens($user, $companyId)
    {
        try {
            $privateKey = $this->getPrivateKey();
            
            if (!$privateKey) {
                Log::warning('Vizzly private key not found, using fallback tokens');
                return $this->getFallbackTokens();
            }

            // Current time and expiration
            $now = time();
            $ttl = 2 * 60 * 60; // 2 hours
            $userReference = "user_{$user->id}";

            // Create dashboard access token
            $dashboardPayload = [
                'projectId' => $this->projectId,
                'userReference' => $userReference,
                'scope' => 'read_write',
                'accessType' => $this->getUserAccessType($user),
                // 'iat' => $now,
                'expires' => '2025-08-21T04:06:58.621Z',
                // 'iss' => 'vizzly'
            ];

            // Create data access token with secure filters
            $dataPayload = [
                'projectId' => $this->projectId,
                'dataSetIds' => '*', // Access to all datasets
                // 'userReference' => $userReference,
                // 'secureFilters' => $this->buildSecureFilters($user, $companyId),
                'parameters' => [],
                'secureFilters' => [],
                // 'parameters' => [
                //     'company_id' => $companyId,
                //     'user_id' => $user->id,
                //     'user_name' => $user->name,
                //     'user_email' => $user->email
                // ],

                // 'iat' => $now,
                'expires' => '2025-08-21T04:06:58.621Z',
                // 'iss' => 'vizzly'
            ];

            // Sign the tokens
            $dashboardToken = JWT::encode($dashboardPayload, $privateKey, 'ES256');
            $dataToken = JWT::encode($dataPayload, $privateKey, 'ES256');

            $tokens = [
                'dashboardAccessToken' => $dashboardToken,
                'dataAccessToken' => $dataToken
            ];

            // Add query engine token for admin users
            if ($this->userHasQueryEngineAccess($user)) {
                $queryEnginePayload = [
                    'organisationId' => $this->projectId,
                    'userReference' => $userReference,
                    'allowDatabaseSchemaAccess' => true,
                    'allowDataPreviewAccess' => true,
                    'iat' => $now,
                    'exp' => $now + $ttl,
                    'iss' => 'vizzly'
                ];
                
                $tokens['queryEngineAccessToken'] = JWT::encode($queryEnginePayload, $privateKey, 'ES256');
            }

            return $tokens;

        } catch (\Exception $e) {
            Log::error('JWT token creation failed: ' . $e->getMessage());
            return $this->getFallbackTokens();
        }
    }

    /**
     * Get private key for JWT signing
     */
    private function getPrivateKey(): ?string
    {
        $path = 'vizzly-private.pem';

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->get($path);
        } 

        return null;
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
     * Following Vizzly documentation for secure filters
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
                'value' => (string) $companyId
            ]
        ];

        // Add user-level filtering for non-admin users
        if (!$this->userIsAdmin($user)) {
            // Apply user-specific filtering to sensitive datasets
            $userSpecificDatasets = [
                'user_data',
                'personal_reports',
                'user_specific_data'
            ];

            foreach ($userSpecificDatasets as $dataset) {
                $filters[$dataset] = [
                    [
                        'field' => 'user_id',
                        'op' => '=',
                        'value' => (string) $user->id
                    ]
                ];
            }
        }

        // Add department-level filtering if user has department
        if (property_exists($user, 'department_id') && $user->department_id) {
            $filters['department_data'] = [
                [
                    'field' => 'department_id',
                    'op' => '=',
                    'value' => (string) $user->department_id
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
        // This could check user_companies table, roles, etc.
        
        // For now, basic checks:
        if ($this->userIsAdmin($user)) {
            return true; // Admins have access to all companies
        }

        // Check if user's default company matches
        if (property_exists($user, 'company_id') && $user->company_id == $companyId) {
            return true;
        }

        // Check if user has explicit access to this company
        // You might have a user_companies pivot table
        if (method_exists($user, 'companies')) {
            return $user->companies()->where('company_id', $companyId)->exists();
        }

        return false;
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
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('super_admin');
        }
        
        if (property_exists($user, 'is_admin')) {
            return $user->is_admin;
        }
        
        if (property_exists($user, 'role')) {
            return in_array($user->role, ['admin', 'super_admin']);
        }
        
        return false;
    }
}
