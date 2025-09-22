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
  echo "[backend] vendor/ ausente, executando composer install..."
  composer install --no-interaction --prefer-dist
  chmod -R 777 storage bootstrap/cache || true
fi

# 3) APP_KEY
if ! grep -q '^APP_KEY=' .env || [ -z "$(grep '^APP_KEY=' .env | cut -d= -f2)" ]; then
  echo "[backend] APP_KEY ausente, gerando..."
  php artisan key:generate --force || true
fi

# 3.1) wait for MySQL
echo "[backend] aguardando MySQL em ${DB_HOST}:${DB_PORT}..."
for i in {1..30}; do
  php -r '
    $h=getenv("DB_HOST")?: "mysql";
    $p=getenv("DB_PORT")?: "3306";
    $u=getenv("DB_USERNAME")?: "edtechuser";
    $pw=getenv("DB_PASSWORD")?: "edteachuser123";
    try { new PDO("mysql:host=$h;port=$p",$u,$pw,[PDO::ATTR_TIMEOUT=>2]); exit(0); }
    catch (Exception $e) { exit(1); }
  ' && break || { echo "  - tentativa $i/30..."; sleep 2; }
done

# 4) migrations
echo "[backend] executando migrations..."
php artisan migrate --force

# 5) seed if database is empty | table considered: courses
echo "[backend] verificando se precisa rodar os seeds..."
COURSES_COUNT=$(php -r '
  $h=getenv("DB_HOST")?: "mysql";
  $p=getenv("DB_PORT")?: "3306";
  $db=getenv("DB_DATABASE")?: "edtech";
  $u=getenv("DB_USERNAME")?: "edtechuser";
  $pw=getenv("DB_PASSWORD")?: "edteachuser123";
  try {
    $pdo=new PDO("mysql:host=$h;port=$p;dbname=$db;charset=utf8mb4",$u,$pw,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $count=$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
    echo (int)$count;
  } catch (Throwable $e) {
    echo 0;
  }
')
if [ "${COURSES_COUNT}" = "0" ]; then
  echo "[backend] banco vazia (courses=0): rodando db:seed..."
  php artisan db:seed --force
else
  echo "[backend] banco já populada (courses=${COURSES_COUNT}): pulando db:seed."
fi

# 6) Upstart the server
echo "[backend] iniciando Laravel..."
exec php artisan serve --host=0.0.0.0 --port=8000
