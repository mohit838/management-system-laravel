# Management System With Laravel

This is a management system with laravel 10 and php-8.1

## Show the tree

    tree -L 3 -I "vendor|node_modules|storage|.git"

## Install package for auth

    composer require tymon/jwt-auth
    php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
    php artisan jwt:secret

## Permission & Role Setup

    composer require spatie/laravel-permission

    php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
    php artisan migrate

## Role Seeder

    php artisan db:seed --class=RoleSeeder

    # Or just run all seeders:

    php artisan db:seed
