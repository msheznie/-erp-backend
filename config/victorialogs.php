<?php

return [

    /*
    |--------------------------------------------------------------------------
    | VictoriaLogs Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for VictoriaLogs
    | integration including API endpoints, authentication, and query limits.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | VictoriaLogs Base API URL
    |--------------------------------------------------------------------------
    |
    | Base URL for VictoriaLogs server. Endpoint paths are appended internally.
    | Example: https://your-victoria-logs-server.com
    |
    */
    'api_url' => env('VICTORIA_LOG_URL', 'https://php-auditlogs-rnd.gears-int.com'),

    /*
    |--------------------------------------------------------------------------
    | VictoriaLogs API Endpoints
    |--------------------------------------------------------------------------
    |
    | Endpoint paths appended to the base URL for push and query operations.
    | These are standard VictoriaLogs endpoints and typically don't need changes.
    |
    */
    'endpoints' => [
        'push' => '/insert/loki/api/v1/push',
        'query' => '/select/logsql/query',
    ],

    /*
    |--------------------------------------------------------------------------
    | VictoriaLogs Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'username' => env('VICTORIA_LOG_USERNAME', 'vauth'),
        'password' => env('VICTORIA_LOG_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Configuration
    |--------------------------------------------------------------------------
    */
    'environment' => env('VICTORIALOGS_ENV', env('APP_ENV', 'local')),

    /*
    |--------------------------------------------------------------------------
    | Query Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of records returned per query.
    | This limit applies to all VictoriaLogs queries.
    |
    */
    'limit' => env('VICTORIA_LOGS_LIMIT', 10000),

    /*
    |--------------------------------------------------------------------------
    | Request Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds for VictoriaLogs API calls
    |
    */
    'timeout' => env('VICTORIA_LOGS_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Failed Log Storage
    |--------------------------------------------------------------------------
    |
    | Whether to store failed audit logs in database for retry.
    | Set to false to disable storing failed logs (they will only be logged).
    |
    */
    'store_failed_logs' => env('VICTORIA_LOGS_STORE_FAILED_LOGS', false),

    'store_logs' => env('STORE_LOGS', false),

    /*
    |--------------------------------------------------------------------------
    | Channel Names
    |--------------------------------------------------------------------------
    |
    | Channel names used for different types of logs
    |
    */
    'channels' => [
        'audit' => 'audit',
        'auth' => 'auth',
        'navigation' => 'navigation',
        'third_party_api' => 'third_party_api',
    ],

];
