<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Vizzly Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration options for Vizzly integration
    |
    */

    'project_id' => env('VIZZLY_PROJECT_ID', 'prj_589be17c00f343d9819dacd36a0a4f60'),
    
    'token_ttl' => env('VIZZLY_TOKEN_TTL', 2 * 60 * 60), // 2 hours in seconds
    
    'user_specific_datasets' => [
        'user_data',
        'personal_reports',
        'user_specific_data'
    ],
    
    'connection_id' => env('VIZZLY_CONNECTION_ID', 'd8c50799-b628-41f6-802e-86aed95feb3b'),
    
    'private_key_path' => 'vizzly-private.pem',
    
    'config_file_path' => env('VIZZLY_CONFIG_FILE_PATH', 'vizzly-config/config.json'),
];
