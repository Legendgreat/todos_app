# TODO App

## Setting up

Clone the repository by creating a new folder and running the command `git clone 'https://github.com/Legendgreat/todos_app.git' .`.

Run `composer install` to initialize the project and download required dependencies.

## Setting up your database

Next we want to initialize and migrate your database.

Do this by running `php bin/console doctrine:database:create` followed by `php bin/console doctrine:migrations:migrate`.

## Running the project

Simply type `symfony server:start` to start a local development server which can be reached at `localhost:8000` by default.

## Endpoints

All endpoints can be viewed at `localhost:8000/api/doc`.
