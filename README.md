# Fund Manager (Laravel/Backend)

Fund Manager is a Financial Management application, This application is similar to the Todo application but with different functionalities, 
built with Laravel Lumen . 

## Installation

Install all composer dependencies  
```sh
composer install --ignore-platform-reqs
```

Migrate Payfan required tables and seed data to tables
```sh
php artisan migrate 
php artisan db:seed
```

Generate secret key / hash key 
```sh
php artisan key:generate
```

Verify the deployment by navigating to your server address in
your preferred browser.

```sh
127.0.0.1:8000
```

## License
MIT && PAYFAN

**Open Source**
