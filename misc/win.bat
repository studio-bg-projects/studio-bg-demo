cd ..
docker compose exec php-fpm rm -rf /var/www/cache/
docker compose exec php-fpm mkdir /var/www/cache/
docker compose exec php-fpm cp -r /var/www/html/vendor/ /var/www/cache/vendor_/
docker compose exec php-fpm mv /var/www/cache/vendor_/ /var/www/cache/vendor/
docker compose exec php-fpm ln -s /var/www/html/app /var/www/cache/app
