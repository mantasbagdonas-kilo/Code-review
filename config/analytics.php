<?php

return [
    // Accounts to pull metrics for on each run.
    'accounts' => ['acc_1', 'acc_2', 'acc_3'],

    'api' => [
        'url' => env('ANALYTICS_API_URL', 'https://api.metrics.example.com'),
        'key' => env('ANALYTICS_API_KEY'),
    ],
];
