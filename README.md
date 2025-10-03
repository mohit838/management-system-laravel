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
    APP_URL=http://localhost

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
