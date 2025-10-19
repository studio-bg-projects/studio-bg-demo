# IT ERP

## Setup dev environment

Put same DB credentials in [.env](.env.example) and [.env](laravel/.env)

## How to start dev

1. `docker compose build`
2. `docker compose up`
3. `chmod 777 ./.tmp`

## Setup PHP dependencies and complete the framework setup

Complete [.env](laravel/.env) (by using same DB credentials from [.env](.env.example))

### Install composer packages

```bash
docker compose exec -w /var/www/html php-fpm composer install
```

### Laravel setup

```bash
docker compose exec -w /var/www/html php-fpm php artisan key:generate
docker compose exec -w /var/www/html php-fpm php artisan migrate
```

### Add DB records

```bash
docker compose exec -w /var/www/html php-fpm php artisan db:seed
```

### Fix permissions

```bash
sudo docker compose exec php-fpm chmod -R 777 /tmp-php
sudo docker compose exec php-fpm chmod -R 777 /var/www/html/bootstrap/cache
sudo docker compose exec php-fpm chmod -R 777 /var/www/html/storage/logs
sudo docker compose exec php-fpm chmod -R 777 /var/www/html/storage/framework/views
sudo docker compose exec php-fpm chmod -R 777 /var/www/html/storage/app/public
```

### PHP artisan (commands)

To see all `artisan` commands run `docker compose exec -w /var/www/html php-fpm php artisan`

## Setup npm, webpack, watchers & more

Install NodeJS from https://nodejs.org/en/

- To install all required packages run `npm install`
- To compile the CSS & JS immediately with watchers start the **development mode** with `npm run dev`
- To compile the CSS & JS for production run `npm run build`

### Build the resources (when there is no local node installation)

```bash
sudo docker compose run --rm -w /app/laravel node sh -c "npm install && npm run build"
```

## Setup npm, webpack, watchers & more

Install NodeJS from https://nodejs.org/en/

- To install all required packages run `npm install`
- To see all available commands run `npm run`
- To compile the CSS & JS immediately with watchers start the **development mode** with `npm run dev`
- To compile the CSS & JS from production run `npm run prod`
