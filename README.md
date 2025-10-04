# Inventory Management System With Laravel-12x

## Installing projects

```bash
    composer install
```

## Copy the **_.env_** File

```bash
    cp .env.example .env
```

## Generate the App Key

```bash
    php artisan key:generate
```

## Configure the **_.env_** File

```bash
    APP_NAME=MyApp
    APP_URL=http://localhost:port

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_db_name
    DB_USERNAME=your_db_user
    DB_PASSWORD=your_db_password

```

## Run Database Migrations And Run (Optional)

```bash
    php artisan migrate
    php artisan db:seed
```

## Install Frontend Dependencies (Optional, if using Vite or Laravel Mix)

```bash
    npm install
    npm run dev
```

-   This compiles CSS/JS if the project uses them.

## Start the Local Development Server

```bash
    php artisan serve
```

## Install & configure JWT

```bash
    composer require php-open-source-saver/jwt-auth
    php artisan vendor:publish --provider="PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider"
    php artisan jwt:secret
```

-   A modern fork of the old tymon/jwt-auth (the original is unmaintained).

    -   It integrates directly with Laravel’s auth system.

-   It gives us helpers like:
    -   auth()->attempt($credentials) → create a token on login
    -   auth()->user() → get the logged-in user from token
    -   auth()->logout() → invalidate a token
    -   auth()->refresh() → refresh token before expiry

## php artisan vendor:publish

-   Why do we publish?
    -   Publishing copies the package’s config file into your app’s config/jwt.php.

## php artisan jwt:secret

-   Why do we need a secret?
    -   JWT = header + payload + signature.
    -   The signature is created with a secret key so tokens can’t be forged.

## Install `php artisan install:api`

```bash
    php artisan install:api
```

## Change Auth Guard

-   Go `config/auth.php` file:

```php
    'defaults' => [
            'guard' => env('AUTH_GUARD', 'api'), <-- web to api
            ....
        ],

        # Then

    'guards' => [
        ....
        # Add new 'api'
        'api' => [
            'driver' => env('AUTH_GUARD_DRIVER', 'jwt'),
            'provider' => 'users',
        ],
    ],
```

## Change In User Model For `JwtSubject`

-   Implement `JwtSubject` in UserModel

```php
    class User extends Authenticatable implements JWTSubject{

        .....

        // For jwt auth implementations
        public function getJWTIdentifier()
        {
            return $this->getKey();
        }

        public function getJWTCustomClaims()
        {
            return [];
        }
    }
```

## After jwt setups

-   Create `Api/ApiController` add (reg, login) then go `route/api.php` (define api route)

## `predis` (pure PHP library) -> easier, just composer (Less prefer)

-   If you don’t want to install system packages, Laravel supports the predis/predis library instead.

```bash
    composer require predis/predis
```

-   Then in .env:

```bash
    CACHE_DRIVER=redis
    REDIS_CLIENT=predis
    # REDIS_URL=redis://:mypassword@127.0.0.1:6379
    REDIS_URL=redis://:mypassword@host.docker.internal:6379
```

-   `config/database.php` file changes

```php
    'client' => env('REDIS_CLIENT', 'predis'),
```

## Mostly prefer`phpredis`

-   Install in your local machine

```bash
    sudo apt install php8.3-redis
```

-   Then in .env:

```bash
    CACHE_DRIVER=redis
    REDIS_CLIENT=phpredis
    # REDIS_URL=redis://:mypassword@127.0.0.1:6379
    REDIS_URL=redis://:mypassword@host.docker.internal:6379
```

-   Register aliases for redis if use `phpredis`

```php
    'aliases' => Facade::defaultAliases()->merge([
            'Redis' => Illuminate\Support\Facades\Redis::class,
        ])->toArray(),
```

## Adjust Blade layout

-   Create a minimal layout in `resources/views/layouts/app.blade.php`:

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>{{ config('app.name') }}</title>
    </head>
    <body>
        <div class="container">@yield('content')</div>
    </body>
</html>
```

Then make a simple test view in `resources/views/hello.blade.php`:

```php
    @extends('layouts.app') @section('content')
    <h1>Hello, Laravel API + Blade!</h1>
    @endsection
```

And route it in `routes/web.php`:

```php
    use Illuminate\Support\Facades\Route;

    Route::get('/', function () {
        return view('hello');
    });
```

```bash
    php artisan view:clear
    php artisan cache:clear
```

---

## Run your containers

-   Bring up your Laravel + Nginx:

```bash
    docker compose build --no-cache
    docker compose up -d
```

## Run a Laravel Redis test

-   Inside your app container:

```bash
    docker exec -it laravel-app bash
```

-   Then run:

```bash
    php artisan tinker
```

-   In Tinker:

```bash
    Cache::put('foo', 'bar', 10); // store in Redis for 10 seconds
    Cache::get('foo'); // should return "bar"
```

-   Or directly test Redis:

```bash
    Redis::set('hello', 'world');
    Redis::get('hello');
```

-   Expected output:

```bash
    => "world"
```

## Clear + check Redis

-   Also run:

```bash
    php artisan cache:clear
    php artisan config:clear
```

-   NOTE:: This ensures Laravel is really using Redis (`CACHE_DRIVER=redis`).

## Laravel Setup :: After Docker Build

-   Run migrations: `docker exec laravel12-app php artisan migrate`.
    -   Clear caches if needed: `docker exec laravel12-app php artisan optimize:clear`.

## Production Deployment :: With Docker Build

-   Remove volume mounts from docker-compose.yml.
-   Ensure `composer install --no-dev --optimize-autoloader` and `php artisan optimize` are run in the build stage.
-   Secure your local Redis and database with proper credentials and network restrictions.

---

## JWT-based Laravel API into a clean layered structure:

```bash
    app/
    ├── Http/
    │    ├── Controllers/
    │    │     └── Api/
    │    │     │    └── AuthController.php
    │    │     └── BaseApiController.php
    │    ├── Requests/
    │    │     ├── RegisterRequest.php
    │    │     └── LoginRequest.php
    │    └── Resources/
    │    │     └── UserResource.php
    │    └── Responses/
    │          └── ApiResponse.php
    ├── Interfaces/
    │     └── BaseRepositoryInterface.php
    │     └── UserRepositoryInterface.php
    ├── Repositories/
    │     ├── BaseRepository.php
    │     └── UserRepository.php
    ├── Services/
    │     ├── BaseService.php
    │     └── AuthService.php
```

### Step 1. Form Requests (Validation Layer)

-   `app/Http/Requests/RegisterRequest.php`

```bash
    php artisan make:request RegisterRequest
```

-   `app/Http/Requests/LoginRequest.php`

```bash
    php artisan make:request LoginRequest
```

### Step 2. Resource (Response Transformer)

-   `app/Http/Resources/UserResource.php`

```bash
    php artisan make:resource UserResource
```

### Step 3. Repository (Database Layer)

-   `app/Interfaces/UserRepositoryInterface.php`

```bash
    php artisan make:interface Interface/UserRepositoryInterface
```

-   `app/Repositories/UserRepository.php`

```bash
    php artisan make:class Repository/UserRepository
```

### Step 4. Service (Business Logic Layer)

-   `app/Services/AuthService.php`

```bash
    php artisan make:class Services/AuthService
```

### Step 5. Controller (Request Handling Only)

-   `app/Http/Controllers/Api/AuthController.php`

```bash
     php artisan make:controller Api/AuthController
```

### Bind Interface to Repository

-   `app/Providers/AppServiceProvider.php --> register()`

```php
     $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
```

### Step 6. BaseResponse helper

-   `app/Http/Responses/ApiResponse.php`

```bash
     php artisan make:class Http/Responses/ApiResponse
```

### Global Exception Handler (unified error format)

-   `app/Exceptions/Handler.php`

```bash
    php artisan make:class Exceptions/Handler
```

### Clear Cache [important]

```bash
    php artisan optimize:clear
```

## Unit Testing with PHPUnit

-   `tests/Unit/UserRepositoryTest.php`
-   `tests/Unit/AuthServiceTest.php`

-   <b> Always use a separate `test database`, because `PHPUnit will reset data` (via RefreshDatabase / migrations).</b>

```bash
    php artisan test # If use phpunit.xml
    php artisan migrate --env=testing
    php artisan test --env=testing # If use .env.testing
```

## Clear Cache and Regenerate Autoload

-   Sometimes Laravel/Composer doesn’t see new files until you regenerate the autoloader:

```bash
    composer dump-autoload
    php artisan optimize:clear
```

## Common Laravel Commands Cheat Sheet

Here are the most common `artisan & composer commands` you’ll use:

-   General

```bash
    php artisan serve              # Start dev server
    php artisan tinker             # REPL to interact with app
    php artisan optimize:clear     # Clear all caches
    php artisan config:clear       # Clear config cache
    php artisan cache:clear        # Clear application cache
    php artisan route:list         # Show all routes
    php artisan migrate            # Run migrations
    php artisan migrate:rollback   # Rollback last migration
    php artisan migrate:fresh      # Drop all tables & re-run migrations
    php artisan migrate:refresh    # Rollback & re-run all migrations
    php artisan db:seed            # Seed DB
    php artisan migrate:fresh --seed # Fresh migration + seed
```

-   Make (generate files)

```bash
    php artisan make:controller UserController --resource
    php artisan make:model User -mfs       # Model + factory + seeder
    php artisan make:request RegisterRequest
    php artisan make:resource UserResource
    php artisan make:middleware AuthMiddleware
    php artisan make:test UserTest
    php artisan make:job ProcessOrderJob
    php artisan make:event OrderPlaced
    php artisan make:listener SendEmailListener
    php artisan make:policy UserPolicy --model=User
```

-   Testing

```bash
    php artisan test                   # Run all tests
    php artisan test --filter=UserTest # Run specific test
    php artisan test --coverage        # With coverage
```

-   Queues / Workers

```bash
    php artisan queue:work     # Start worker
    php artisan queue:listen   # Listen for jobs
    php artisan queue:failed   # List failed jobs
    php artisan queue:retry 5  # Retry failed job id 5
```

-   Debugging

```bash
    php artisan route:list --except-vendor   # Only app routes
    php artisan config:cache
    php artisan view:clear
    php artisan event:list
```
php artisan test --coverage-html coverage-report
