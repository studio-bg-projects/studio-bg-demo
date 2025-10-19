#!/bin/sh

cd /mnt/data/workspace/services/insidetrading.bg/erp/

LOCKFILE=".it-cron.lock"

# Check is it already executing
if [ -f "$LOCKFILE" ]; then
  echo "Script is already running (lock file exists: $LOCKFILE)"
  exit 1
fi

# Create lock file
touch "$LOCKFILE"

# Remove the lock file on exit
cleanup() {
  echo "Exiting... removing lock file."
  rm -f "$LOCKFILE"
  exit
}

# Remove the lock file on kill
trap cleanup INT TERM

# Endless loop
while true; do
  echo "Starting sync cycle..."

  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:customers
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:customers-addresses
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:orders
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:payments
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:misc
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:sync:feeds-imports
  sudo docker compose exec -w /var/www/html php-fpm php artisan job:send-mail-queue
  echo "Cycle finished. Sleeping for 10 seconds..."
  sleep 10
done

# In case the script gets here (is should not)
cleanup
