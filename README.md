# Management System With Laravel

This is a management system with laravel 10 and php-8.1

## Install package for auth

    composer require tymon/jwt-auth
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    php artisan jwt:secret
