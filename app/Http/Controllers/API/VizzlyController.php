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
    private $projectId;
    
    public function __construct()
    {
        $this->projectId = config('vizzly.project_id');
    }
    
    /**
     * Generate Vizzly identity tokens for the authenticated user
     */
    public function generateTokens(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return $this->sendError('Authentication required', 401);
            }
            
            // Generate tokens using JWT
            $tokens = $this->createVizzlyTokens($user);

            return $this->sendResponse([
                'identity' => $tokens,
                'expires_at' => now()->addHours(2)->toISOString()
            ], 'Vizzly tokens generated successfully');

        } catch (\Exception $e) {
            return $this->sendError('Failed to generate Vizzly tokens: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Create Vizzly identity tokens using Firebase JWT
     */
    private function createVizzlyTokens($user)
    {
        $privateKey = $this->getPrivateKey();
        
        if (!$privateKey) {
            throw new \Exception('Vizzly private key not found');
        }

        // Current time and expiration
        $now = time();
        $ttl = config('vizzly.token_ttl', 2 * 60 * 60); // 2 hours default
        $expires = date('c', $now + $ttl); // ISO 8601 format
        $userReference = $user->uuid;

        // Create dashboard access token
        $dashboardPayload = [
            'projectId' => $this->projectId,
            'userReference' => $userReference,
            'scope' => 'read_write',
            'accessType' => $this->getUserAccessType($user),
            'expires' => $expires
        ];

        // Create data access token with secure filters
        $dataPayload = [
            'projectId' => $this->projectId,
            'dataSetIds' => '*',
            'secureFilters' => $this->buildSecureFilters($user),
            'parameters' => [
                'user_id' => $user->uuid,
                'user_name' => $user->name,
                'user_email' => $user->email
            ],
            'expires' => $expires
        ];

        // Sign the tokens
        $dashboardToken = JWT::encode($dashboardPayload, $privateKey, 'ES256');
        $dataToken = JWT::encode($dataPayload, $privateKey, 'ES256');

        $tokens = [
            'dashboardAccessToken' => $dashboardToken,
            'dataAccessToken' => $dataToken
        ];

        // Add query engine token for admin users
        // if ($this->userHasQueryEngineAccess($user)) {
        //     $queryEnginePayload = [
        //         'organisationId' => $this->projectId,
        //         'userReference' => $userReference,
        //         'allowDatabaseSchemaAccess' => true,
        //         'allowDataPreviewAccess' => true,
        //         'iat' => $now,
        //         'exp' => $now + $ttl,
        //         'iss' => 'vizzly'
        //     ];
            
        //     $tokens['queryEngineAccessToken'] = JWT::encode($queryEnginePayload, $privateKey, 'ES256');
        // }

        return $tokens;
    }

    /**
     * Get private key for JWT signing
     */
    private function getPrivateKey(): ?string
    {
        $path = config('vizzly.private_key_path');

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->get($path);
        } 

        return null;
    }


    /**
     * Build secure filters for multi-tenancy
     */
    private function buildSecureFilters($user): array
    {
        $filters = [];
        
        // // Apply company-level filtering to all datasets
        // $filters['*'] = [
        //     [
        //         'field' => 'company_id',
        //         'op' => '=',
        //         'value' => (string) $companyId
        //     ]
        // ];

        // // Add user-level filtering for non-admin users
        // if (!$this->userIsAdmin($user)) {
        //     $userSpecificDatasets = config('vizzly.user_specific_datasets', [
        //         'user_data',
        //         'personal_reports',
        //         'user_specific_data'
        //     ]);

        //     foreach ($userSpecificDatasets as $dataset) {
        //         $filters[$dataset] = [
        //             [
        //                 'field' => 'user_id',
        //                 'op' => '=',
        //                 'value' => (string) $user->id
        //             ]
        //         ];
        //     }
        // }

        // // Add department-level filtering if user has department
        // if (property_exists($user, 'department_id') && $user->department_id) {
        //     $filters['department_data'] = [
        //         [
        //             'field' => 'department_id',
        //             'op' => '=',
        //             'value' => (string) $user->department_id
        //         ]
        //     ];
        // }

        return $filters;
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
        return false;
    }

    /**
     * Get Vizzly remote configuration
     */
    public function getRemoteConfig()
    {
        try {
            // Load the JSON config file from resources
            $configPath = resource_path(config('vizzly.config_file_path'));
            
            if (!file_exists($configPath)) {
                return $this->sendError('Vizzly config file not found', 404);
            }
            
            $configContent = file_get_contents($configPath);
            $config = json_decode($configContent, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return $this->sendError('Invalid JSON config file', 500);
            }
            
            // Ensure sqlViews is an object, not an array
            if (isset($config['sqlViews'])) {
                if (is_array($config['sqlViews'])) {
                    $config['sqlViews'] = (object) $config['sqlViews'];
                }
            } else {
                $config['sqlViews'] = (object) [];
            }
            
            // Get database credentials from environment
            $dbHost = '10.0.0.13';
            $dbDatabase = 'gears_erp_berkeley';
            $dbUsername = 'zakeeul';
            $dbPassword = 'ZkfnbeT&(Gfuygwb87';
            $dbPort = '3306';
            $connectionId = '5030b4dc-1d09-4661-a59c-e4da901a17xx';

            // Add dynamic connection to the config
            $config['connections'][$connectionId] = (object)[
                "client" => "mysql",
                "name" => "OSOS QA",
                "unencryptedCredentials" => (object)[
                    "host" => $dbHost,
                    "database" => $dbDatabase,
                    "user" => $dbUsername,
                    "password" => $dbPassword,
                    "port" => $dbPort
                ]
            ];

            return response()->json($config);
            
        } catch (\Exception $e) {
            Log::error('Vizzly remote config retrieval failed: ' . $e->getMessage());
            return $this->sendError('Failed to retrieve Vizzly config: ' . $e->getMessage(), 500);
        }
    }
}
