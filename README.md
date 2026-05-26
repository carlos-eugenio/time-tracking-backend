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

## MySQL

```text
Host: mysql
Port: 3306
```

Para acessar o banco pela maquina host:

```text
Host: 127.0.0.1
Port: 3307
Database: time_tracking
User: time_tracking
Password: secret
```

Comandos uteis para o MySQL:

```bash
docker compose up -d mysql
docker compose logs -f mysql
docker compose exec mysql mysql -utime_tracking -psecret time_tracking
docker compose exec mysql mysql -uroot -proot
```

## Comandos uteis

```bash
docker compose ps
docker compose logs -f app
docker compose exec app php artisan test
```
