# Time Tracking Backend

Backend em Laravel 12 para controle de colaboradores, lançamentos de ponto, relatórios filtraveis e exportação de dados.

## Funcionalidades

- Autenticacao via Laravel Sanctum.
- Seed de usuario administrador.
- CRUD de colaboradores.
- Ativação e inativação de colaboradores.
- CRUD de lançamentos de ponto.
- Validação de horários de entrada e saída.
- Relatório de pontos por período.
- Filtro opcional por colaborador.
- Ordenação do relatório por funcionário ou data.
- Exportação do relatório em CSV, XLSX e PDF.
- Testes automatizados dos fluxos principais.
- Collection Postman com endpoints e exemplos de parâmetros.

## Stack

- PHP 8.2+
- Laravel 12
- Laravel Sanctum
- MySQL 8
- Nginx
- Docker Compose
- PHPUnit
- Laravel Excel
- DomPDF

## Requisitos

- Git
- Docker
- Docker Compose v2

Nao e necessario ter PHP, Composer, MySQL ou Nginx instalados na maquina host para rodar pelo Docker.

## Rodando com Docker

Clone o repositorio:

```bash
git clone URL_DO_REPOSITORIO_BACKEND
cd backend
```

Crie o arquivo de ambiente:

```bash
cp .env.example .env
```

Suba os containers:

```bash
docker compose up -d --build
```

Confira se os containers subiram:

```bash
docker compose ps
```

Gere a chave da aplicação:

```bash
docker compose exec app php artisan key:generate
```

Rode as migrations:

```bash
docker compose exec app php artisan migrate
```

Popule o banco com o usuário admin, colaboradores e lançamentos de ponto:

```bash
docker compose exec app php artisan db:seed
```

Importante: para testar o login, rode as seeds. 
O usuário inicial é criado pelo `AdminUserSeeder`.

A API ficará disponível em:

```text
http://localhost:8080/api
```

Se as portas `8080` ou `3307` já estiverem em uso, altere no `docker-compose.yml` ou no `.env` antes de subir os containers.

## Credenciais

Usuario criado pela seed:

```text
Email: admin@email.com
Senha: adminTeste1234
```

Banco MySQL para conexao a partir da maquina host:

```text
Host: 127.0.0.1
Port: 3307
Database: time_tracking
User: time_tracking
Password: secret
Root password: root
```

Dentro dos containers, use:

```text
Host: mysql
Port: 3306
```

## Comandos Uteis

Ver containers:

```bash
docker compose ps
```

Ver logs da aplicação:

```bash
docker compose logs -f app
```

Ver logs do Nginx:

```bash
docker compose logs -f nginx
```

Ver logs do MySQL:

```bash
docker compose logs -f mysql
```

Rodar migrations:

```bash
docker compose exec app php artisan migrate
```

Recriar banco e popular novamente:

```bash
docker compose exec app php artisan migrate:fresh --seed
```

Acessar o container da aplicação:

```bash
docker compose exec app bash
```

Acessar o MySQL:

```bash
docker compose exec mysql mysql -utime_tracking -psecret time_tracking
```

Parar containers:

```bash
docker compose down
```

Parar containers e remover volumes:

```bash
docker compose down -v
```

## Testes

Os testes usam o banco `time_tracking_testing`. Crie o database uma vez:

```bash
docker compose exec mysql mysql -uroot -proot -e "CREATE DATABASE IF NOT EXISTS time_tracking_testing CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Rode a suite:

```bash
docker compose exec app php artisan test
```

Rodar um arquivo especifico:

```bash
docker compose exec app php artisan test tests/Feature/EmployeesTest.php
```

## Collection Postman

A collection esta em:

```text
postman/time-tracking-api.postman_collection.json
```

Ela possui exemplos para:

- Login.
- Usuário autenticado.
- Logout.
- Colaboradores.
- Lançamentos de ponto.
- Relatório.
- Exportações CSV, XLSX e PDF.

## Observacoes

- Rode `db:seed` antes de testar a autenticação.
- O primeiro `docker compose up -d --build` demora por causa das extensões e dependências Composer.
- As dependências PHP são instaladas durante o build da imagem; a pasta `vendor` não precisa estar versionada.
- O Nginx expoe a API em `http://localhost:8080/api` e encaminha as requisicoes PHP para o container `app`.
- O MySQL roda no container `mysql`; de dentro do Docker o host do banco e `mysql`, e da máquina host e `127.0.0.1:3307`.
