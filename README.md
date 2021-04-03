# Snowtrick

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/0474e6502be6414081ad57df8633d5fb)](https://www.codacy.com/gh/kdefarge/snowtrick/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=kdefarge/snowtrick&amp;utm_campaign=Badge_Grade)
[![SymfonyInsight](https://insight.symfony.com/projects/c53b2a8b-160d-408c-802e-8bada2cedf44/mini.svg)](https://insight.symfony.com/projects/c53b2a8b-160d-408c-802e-8bada2cedf44)

Snowtrick is a Symfony web application

## Installation

*   install Snowtrick with composer

```bash
git clone git@github.com:kdefarge/snowtrick.git
cd snowtrick
composer install
```

## Setup

*   update .env file

```bash
# Config database
# MariaDB (dont forget version X.X.X with your version)
DATABASE_URL="mysql://USER:PASSWRD@SERVER:PORT/DB_NAME?serverVersion=mariadb-X.X.X"
# Config mailer
# Mail is send when create account and reset password
MAILER_DSN=smtp://USER:PASSWORD@SERVER_SMTP:PORT
# Config Recpatcha
# Recaptcha is used for register and reset password
RECAPTCHA_PUBLIC="Your public key"
RECAPTCHA_KEY="Your secret key"
```

*   install database

```bash
# Doctrine can create the DB_NAME database for you
php bin/console doctrine:database:create
# executes all migration files
php bin/console doctrine:migrations:migrate
```

*   Run dev fixture

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

## Testing Error Pages during Development

Source : [How to Customize Error Pages](https://symfony.com/doc/current/controller/error_pages.html)

```bash
# templates/bundles/TwigBundle/Exception/
http://localhost/index.php/_error/{statusCode}
http://localhost/index.php/_error/{statusCode}.{format}
```
