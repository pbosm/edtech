#!/usr/bin/env sh
set -e
cd /app

dos2unix frontend/start.sh 2>/dev/null || true
chmod +x frontend/start.sh

# 1) .env
[ -f .env ] || { echo "[frontend] copiando .env.example -> .env"; cp .env.example .env; }

# 2) deps
[ -d node_modules ] || { echo "[frontend] instalando deps..."; npm install; }

echo "[frontend] iniciando Nuxt..."
exec npm run dev -- --port 3000 --host
