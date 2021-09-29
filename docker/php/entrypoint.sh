#!/bin/bash

log() {
  echo -e "${NAMI_DEBUG:+${CYAN}${MODULE} ${MAGENTA}$(date "+%T.%2N ")}${RESET}${@}" >&2
}

init_project() {
  log "Init project"
  composer install --no-dev
  php artisan key:generate
  php artisan vendor:publish --provider="JeroenNoten\LaravelAdminLte\ServiceProvider" --tag=assets
  chown -R www-data:www-data /var/www/html
  chmod -R g+w /var/www/html
}

setup_db() {
  log "Configuring the database"
  php artisan cache:clear
  php artisan migrate --force
  php artisan db:seed --class=DatabaseSeeder
  php artisan cache:clear
  php artisan view:clear
}

load_data() {
  log "Loading data from Gitlab"
  php artisan import:all
}


if [ ! -f  /var/www/html/.env ]; then
  log "Waiting for Postgres..."
  /root/wait-for-it.sh db:5432 --timeout=180 -- echo "PostgreSQL started"
  init_project
  setup_db
  log "Start first time importing... Waiting...(20-30 minutes)"
  load_data
fi

log "Start php-fpm"
php-fpm -F
