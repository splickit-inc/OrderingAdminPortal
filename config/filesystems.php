<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => env('STORAGE', 's3'),

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

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],
        'public_folder' => [
            'driver' => 'local',
            'root' => public_path(),
            'visibility' => 'public',
        ],
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'your_key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'your_secret'),
            'region' => env('AWS_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET', 'com.yourbiz.products'),
        ],

        'preview' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'your_key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'your_secret'),
            'region' => env('AWS_REGION', 'us-east-1'),
            'bucket' => 'com.yourbiz.products.preview',
        ],

        'MerchantProcessingDocuments' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'your_key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'your_secret'),
            'region' => env('AWS_REGION_PROCESSING_DOCUMENTS', 'us-east-1'),
            'bucket' => env('AWS_BUCKET_PROCESSING_DOCUMENTS', 'yourbiz.merchant.processing.documents'),
        ],
    ],

];
