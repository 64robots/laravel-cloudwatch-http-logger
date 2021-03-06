#### Installation


#### Configuration

Add `cloudwatch` to the `channels` array

```php
[
    'channels' =>  [
        'cloudwatch' => [
                'driver' => 'custom',
                'name' => env('CLOUDWATCH_LOG_NAME', ''),
                'region' => env('CLOUDWATCH_LOG_REGION', ''),
                'credentials' => [
                    'key' => env('CLOUDWATCH_LOG_KEY', ''),
                    'secret' => env('CLOUDWATCH_LOG_SECRET', '')
                ],
                'stream_name' => env('CLOUDWATCH_LOG_STREAM_NAME', 'laravel_app'),
                'retention' => env('CLOUDWATCH_LOG_RETENTION_DAYS', 14),
                'group_name' => env('CLOUDWATCH_LOG_GROUP_NAME', 'laravel_app'),
                'version' => env('CLOUDWATCH_LOG_VERSION', 'latest'),
                'formatter' => \Monolog\Formatter\JsonFormatter::class,
                'disabled' => env('DISABLE_CLOUDWATCH_LOG', false),
            ],
    ],
];
```

Add correct values to keys in your .env file. And it should work.

