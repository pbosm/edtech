#!/usr/bin/env bash
set -e
cd /app

# 1) .env
if [ ! -f .env ]; then
  echo "[frontend] .env não encontrado, copiando de .env.example..."
  cp .env.example .env
fi

# 2) deps
if [ ! -d node_modules ]; then
  echo "[frontend] node_modules ausente, instalando..."
  npm install
fi

echo "[frontend] iniciando Nuxt..."
exec npm run dev -- --port 3000 --host
