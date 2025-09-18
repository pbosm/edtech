# EdTech Infra Starter (Laravel + Nuxt + Docker)

## Passos
1) **Gerar Laravel** (uma vez):
```bash
chmod +x scripts/init_backend.sh
./scripts/init_backend.sh
```
2) **Subir os serviços**:
```bash
docker compose up --build -d
docker compose exec -T backend php artisan key:generate
```
3) **Acessar**:
- Frontend: http://localhost:8080
- Backend: http://localhost:8080/api (se aparecer 404 JSON do Laravel, está rodando)

> Só infra nesta etapa. A API e páginas virão depois.
