# Snowtrick [![SymfonyInsight](https://insight.symfony.com/projects/c53b2a8b-160d-408c-802e-8bada2cedf44/mini.svg)](https://insight.symfony.com/projects/c53b2a8b-160d-408c-802e-8bada2cedf44)

Snowtrick is a Symfony web application

## Installation

- install Snowtrick with composer

```bash
git clone git@github.com:kdefarge/snowtrick.git
cd snowtrick
composer install
```

## Setup

- update .env file

```bash
# MariaDB (dont forget version X.X.X with your version)
DATABASE_URL="mysql://USER:PASSWRD@SERVER:PORT/DB_NAME?serverVersion=mariadb-X.X.X"
# Mail is send when create account and reset password
MAILER_DSN=smtp://USER:PASSWORD@SERVER_SMTP:PORT
```

- install database

```bash
# Doctrine can create the DB_NAME database for you
php bin/console doctrine:database:create
# executes all migration files
php bin/console doctrine:migrations:migrate
```

- Run dev fixture

```bash
# load all the 'dev' fixtures
php bin/console hautelook:fixtures:load --env=dev
```

## Running Snowtrick Application

```bash
cd snowtrick
symfony server:start
```

Open your browser and navigate to http://localhost:8000/. If everything is working, you’ll see a welcome page. Later, when you are finished working, stop the server by pressing Ctrl+C from your terminal.
