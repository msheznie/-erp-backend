<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DRIVER', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],
        'local_public' => [
            'driver' => 'local',
            'root' => public_path(''),
            'url' => env('APP_URL').'',
            'visibility' => 'public',
        ],
        'public' => [
            'driver' => 'local',
            'root' => 'C:\inetpub\wwwroot\GEARSERP\GEARSWEBPORTAL\Portal\uploads', //storage_path('app/public'),
            'url' => 'C:\inetpub\wwwroot\GEARSERP\GEARSWEBPORTAL\Portal\uploads' , //env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => true,
            'file_expiry_time' => '+60 minutes'
        ],

        's3SRM' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID_SRM'),
            'secret' => env('AWS_SECRET_ACCESS_KEY_SRM'),
            'region' => env('AWS_DEFAULT_REGION_SRM'),
            'bucket' => env('AWS_BUCKET_SRM'),
            'url' => env('AWS_URL_SRM'),
        ],

        

        'ftp' => [
            'driver'   => 'ftp',
            'host'     => 'gears.gulfenergy-int.com',
            'username' => 'gearsadmin@gulfenergy-int.com',
            'password' => 'Gea-1234',
            'port'     => 21,

            // Optional FTP Settings...
            // 'port'     => 21,
            // 'root'     => '',
            // 'passive'  => true,
            // 'ssl'      => true,
            // 'timeout'  => 30,
        ],
        'resource' => [
            'driver' => 'local',
            'root' => resource_path('views'),
            'url' => env('APP_URL').'',
            'visibility' => 'public',
        ],
        'sftp' => [
            'driver'   => 'sftp',
            'host'     => '',
            'username' => '',
            'password' => '',
            'port'     => 22
        ],
    ],

];
