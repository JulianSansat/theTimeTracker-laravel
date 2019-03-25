# theTimeTracker-laravel [![Build Status](https://travis-ci.com/JulianSansat/theTimeTracker-laravel.svg?branch=master)](https://travis-ci.com/JulianSansat/theTimeTracker-laravel)

# Pre-Requisites:

* HTTP Server (recommended Apache 2.4.x)
* PHP 7.1.3 or greater is REQUIRED
* SQL-based Database Server (recommended latest MariaDB)
* PHP's Composer environment installed

# Installing:

1. Git clone.
2. Enter into project folder.
3. Install your project dependencies with composer install (required for everything).
4. Configure your Environment file by copying (cp .env.example .env), generating your keys (APP_KEY, JWT_SECRET and APP_SETUP_KEY) and typing your database credentials.

5. php artisan key:generate
6. php artisan jwt:generate

7. Run your migrations php artisan migrate:fresh --seed .
8. Run the project by opening php artisan serve or setting up your Apache VHost.

9. Run test with: php vendor/bin/phpunit

### This project is currently executing tests automatically with Travis-ci.


