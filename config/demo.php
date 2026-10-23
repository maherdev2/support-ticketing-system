<?php

return [
    'quick_login' => env('APP_ENV') === 'local'
        && env('APP_DEBUG', false)
        && env('DEMO_QUICK_LOGIN', false),
];
