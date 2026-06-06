# day5_storman — VaultOS

A self-storage management dashboard: units, tenants, and payments in one place.

VaultOS is a small operations app for a storage facility. See every unit and whether it's occupied, manage the tenants renting them, record payments, and get the whole picture on a dashboard. Built as a Laravel API behind a Vue SPA, containerised, and wired to deploy on AWS.

## What it does

- **Units** — grid of every unit with occupancy status
- **Tenants** — who's renting what
- **Payments** — record and track payments
- **Dashboard** — occupancy and revenue at a glance

## Stack

- Laravel 11 (PHP 8.2) REST API
- Vue 3 SPA
- SQL Server (MSSQL)
- Docker + Docker Compose
- GitHub Actions → AWS ECR / ECS Fargate

## Running it (Docker)

```
cp .env.example .env          # set APP_KEY, or run php artisan key:generate in the container
docker-compose up --build
docker-compose exec app php artisan migrate --seed
```

Open http://localhost:8080.

## Deploy

Pushes to `master` build the image, push it to ECR, and roll out a rolling update to ECS Fargate. One-time AWS setup (ECR repo, SSM secrets, ECS cluster + ALB) lives in `ecs-task-definition.json` and `.github/workflows/deploy.yml`. Requires GitHub secrets `AWS_ACCESS_KEY_ID` and `AWS_SECRET_ACCESS_KEY`.

---

Day 5 of building a small thing every day.
