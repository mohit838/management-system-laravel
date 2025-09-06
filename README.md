# Management System With Laravel

This is a management system with laravel 10 and php-8.1

## Show the tree

    tree -L 3 -I "vendor|node_modules|storage|.git"

## Install package for auth

    composer require tymon/jwt-auth
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    php artisan jwt:secret
