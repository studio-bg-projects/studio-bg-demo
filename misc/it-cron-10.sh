cd /mnt/data/workspace/services/insidetrading.bg/erp/

# Once 24h
sudo docker compose exec -w /var/www/html php-fpm php artisan job:cleanup:uploads

# Every hour
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:store-searches

# Every 10 min
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:categories
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:manufacturers
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:products
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:specifications
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:seo
sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:filters

# sudo docker compose exec -w /var/www/html php-fpm php artisan job:load:data-sources
# sudo docker compose exec -w /var/www/html php-fpm php artisan job:data-sources-match
