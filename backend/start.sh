#!/usr/bin/env bash
set -e
cd /var/www/html

# 1) .env
if [ ! -f .env ]; then
  echo "[backend] .env não encontrado, copiando de .env.example..."
  cp .env.example .env
fi

# 2) vendor
if [ ! -f vendor/autoload.php ]; then
  echo "[backend] vendor/ ausente, rodando composer install..."
  composer install --no-interaction --prefer-dist
  chmod -R 777 storage bootstrap/cache || true
fi

# 3) APP_KEY
if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
  echo "[backend] APP_KEY ausente, gerando..."
  php artisan key:generate --force || true
fi

# 4) Migrate + Seed
php artisan migrate --seed --force

echo "[backend] iniciando Laravel..."
exec php artisan serve --host=0.0.0.0 --port=8000
