# API Platform

## Symfonycast

- [x] API Platform: Serious RESTful APIs
- [x] API Platform Part 2: Security
- [ ] API Platform Part 3: Custom Resources

## Setup

Make sure you have [Composer installed](https://getcomposer.org/download/)
and then run:

```
composer install
```

You may alternatively need to run `php composer.phar install`, depending
on how you installed Composer.

**Start the built-in web server**

You can use Nginx or Apache, but Symfony's local web server
works even better.

To install the Symfony local web server, follow
"Downloading the Symfony client" instructions found
here: https://symfony.com/download - you only need to do this
once on your system.

Then, to start the web server, open a terminal, move into the
project, and run:

```
symfony serve -d
```

(If this is your first time using this command, you may see an
error that you need to run `symfony server:ca:install` first).

Now check out the site at `https://localhost:8000`

**Database Setup (with Docker)**

The easiest way to set up the database is to use the `docker-compose.yaml`
file that's included in this project. First, make sure Docker is downloaded
and running on your machine. Then, from inside the project, run:

```
docker-compose up -d
```

**Database Setup (without Docker)**

If you do not want to use Docker, you can also just install and run
MySQL manually. When you're done, open the `.env` file and make any
adjustments you need - specifically `DATABASE_URL`. Or, better,
you can create a `.env.local` file and *override* any configuration
you need there (instead of changing `.env` directly).

**Database Schema**

To actually *create* the database and get some tables, run:

```
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
symfony console doctrine:fixtures:load
```

This uses the `symfony` binary, but `symfony console` is identical
to `php bin/console`, except that this allows the `DATABASE_URL`
environment variable to be injected if you're using Docker.

If you get an error that the database exists, that should
be ok. But if you have problems, completely drop the
database (`doctrine:database:drop --force`) and try again.

**Database Schema for Tests**

To execute the tests, you may (if you're using the Docker integration)
need to also initialize the database inside the test database container:

```
symfony console doctrine:database:create --env=test
symfony console doctrine:migrations:migrate --env=test
```

**Optional: Compiling Webpack Encore Assets**

Make sure to install Node and also [yarn](https://yarnpkg.com).
Then run:

```
yarn install
yarn encore dev --watch
```
