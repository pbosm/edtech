#!/usr/bin/env bash
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
  echo "[backend] vendor/ não encontrado. Rodando composer install..."
  composer install --no-interaction --prefer-dist
  chmod -R 777 storage bootstrap/cache || true
fi

# opcional: gera APP_KEY se estiver faltando
if [ -f .env ] && ! grep -q '^APP_KEY=' .env; then
  echo "[backend] APP_KEY ausente. Gerando..."
  php artisan key:generate --force || true
fi

echo "[backend] iniciando servidor Laravel..."
exec php artisan serve --host=0.0.0.0 --port=8000
