git pull

mkdir -p ./laravel/node_modules/
chmod 777 ./laravel/node_modules/

# Build
docker compose build
docker compose up -d --remove-orphans
docker compose exec -w /var/www/html php-fpm composer install
docker compose run --rm -w /app/laravel node sh -c "npm ci && npm run build"

# Tmp
mkdir -p ./.tmp
chmod 777 ./.tmp

# Permissions
docker compose exec php-fpm chmod -R 777 /var/www/html/bootstrap/cache
docker compose exec php-fpm chmod -R 777 /var/www/html/storage/logs
docker compose exec php-fpm chmod -R 777 /var/www/html/storage/framework
docker compose exec php-fpm chmod -R 777 /var/www/html/storage/app/public

# Clean
docker compose exec -w /var/www/html php-fpm php artisan cache:clear
docker compose exec -w /var/www/html php-fpm php artisan route:clear
docker compose exec -w /var/www/html php-fpm php artisan config:clear
docker compose exec -w /var/www/html php-fpm php artisan view:clear
