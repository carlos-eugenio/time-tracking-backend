# Time Tracking Backend

Backend em Laravel 12 para um sistema simples de lancamento de horas.

## Stack

- PHP 8.2
- Laravel 12
- MySQL 8
- Docker Compose com `app`, `nginx` e `mysql`

## Primeira execucao com Docker

Crie o arquivo de ambiente:

```bash
cp .env.example .env
```

Suba os containers:

```bash
docker compose up -d --build
```

Gere a chave da aplicacao:

```bash
docker compose exec app php artisan key:generate
```

Rode as migrations:

```bash
docker compose exec app php artisan migrate
```

A aplicacao fica disponivel em:

```text
http://localhost:8080
```

O MySQL fica acessivel pela maquina host em:

```text
127.0.0.1:3307
```

Credenciais locais:

```text
Database: time_tracking
User: time_tracking
Password: secret
Root password: root
```

## Comandos uteis

```bash
docker compose ps
docker compose logs -f app
docker compose exec app php artisan test
docker compose exec app php artisan migrate:fresh
```

