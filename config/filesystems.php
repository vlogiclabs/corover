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
    // https://861653539274.signin.aws.amazon.com/console
    //access key : AKIAIAOZYID6CKW2QGQA 
    //secret key : 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
//composer require league/flysystem-aws-s3-v3
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],
//https://stackoverflow.com/questions/34574347/file-upload-to-aws-s3-laravel-5-1
        's3' => [
            'driver' => 's3',
            'key' => 'AKIAITUVYTQBN2JRYEHQ',
            'secret' => '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',
            'region' =>'us-east-1',
            'bucket' => 'vllmohali',
        'ACL' => 'public-read'
            // 'version' => 'latest',
            // 'endpoints'=>'a4b.us-east-1.amazonaws.com',
             // "Action": [
               // "s3:GetObject"
              //],
              //"Resource": [
               // "https://s3.amazonaws.com/vllmohali"
            //]
             //'scheme' => , 
        ],




   /* 's3_client_config' => 
    [ 'key' => env('S3_KEY'),
     'secret' => env('S3_SECRET'), 
     'region' => env('S3_REGION'),
      'scheme' => env('S3_SCHEME'), 
      'version' => 'latest',

     ],*/
    ],

];
