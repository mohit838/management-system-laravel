<?php

return [
    'paths' => [
        // resources/views  ← remove or comment out if it doesn't exist
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views')) ?: null
    ),
];
