# SaaS Laravel Docker

Plataforma SaaS multi-tenant construida con Laravel + Vue 3 + Inertia.js.

## Stack

- **Backend:** Laravel 13 + PHP 8.4
- **Frontend:** Vue 3 + Inertia.js + Tailwind CSS
- **Base de datos:** MySQL 8.0
- **Cache/Queue:** Redis
- **Storage:** Cloudflare R2
- **Proxy:** Nginx + Nginx Proxy Manager
- **Automatización:** n8n

## Verticales

- 🧊 Hielitos 3 Cachorros — negocio de hielitos gourmet
- 🏠 Real Estate — gestión inmobiliaria (próximamente)
- 📸 Fotógrafa — portafolio fotográfico (próximamente)
- 🏫 App Escolar — gestión escolar multi-tenant (próximamente)

## Requisitos locales

- Docker Desktop
- Git

## Instalación local

```bash
git clone git@github.com:Jk99z2/saas-laravel-docker.git
cd saas-laravel-docker

# Copiar variables de entorno
cp app/.env.example app/.env

# Levantar containers
docker compose up -d

# Instalar dependencias
docker exec saas_app composer install
docker exec saas_app php artisan key:generate
docker exec saas_app php artisan migrate
docker exec saas_app npm install
docker exec saas_app npm run build
```

## Acceso local

- App: http://localhost
- n8n: http://localhost:5678

## Despliegue en producción

```bash
git pull
docker exec saas_app php artisan config:clear
docker exec saas_app php artisan migrate --force
docker exec saas_app npm run build
```
\```

## Estructura

\```
saas-laravel-docker/
├── app/          # Proyecto Laravel
└── nginx/        # Configuración Nginx
\```
