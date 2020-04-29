<?php

return [

    /**
     * 阿里云 AccessKeyId
     */
    'accessKeyId' => env('SLS_ACCESS_KEY_ID', 'AccessKeyID'),

    /**
     * 阿里云 AccessKeySecret
     */
    'accessKeySecret' => env('SLS_ACCESS_KEY_SECRET', 'AccessKeySecret'),

    /**
     * endpoint
     */
    'endpoint' => env('SLS_ENDPOINT', ''),

    /**
     *  project
     */
    'project' => env('SLS_PROJECT', ''),

    /**
     * logStore
     */
    'logStore' => env('SLS_LOG_STORE', ''),

    /**
     * topic，非必须
     */
    'topic' => env('SLS_TOPIC', ''),

    /**
     * source，非必须
     */
    'source' => env('SLS_SOURCE', ''),

    /**
     * APP_ENV
     */
    'env' => config('app.env', ''),
];
