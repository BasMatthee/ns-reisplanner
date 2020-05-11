# NS Reisplanner

This application shows real time train arrivals and departures using data provided by [De Nederlandse Spoorwegen](https://www.ns.nl/).

## Prerequisites

- [PHP](https://secure.php.net/manual/en/install.php) (7.4 or higher)
- [Composer](https://getcomposer.org/doc/00-intro.md)
- [GNU Make](https://www.gnu.org/software/make/)
- [Docker](https://docs.docker.com/install/)
- [Docker Compose](https://docs.docker.com/compose/install/)

## Getting started

## Dependencies

Run `make` to install all necessary dependencies and compile the assets.

You will also need an API key from the
[NS Travel information API](https://apiportal.ns.nl) and add
it to the `.env` (or create a `.env.local`) file in the root of this project.

### Environment

Use `docker-compose up` to get the application up and running. Then execute the
database migrations:

```bash
docker-compose exec app bin/console doctrine:migrations:migrate
```

### Importing train stations

In order to import all available train stations the following command can be
executed.

```bash
docker-compose exec app bin/console ns-reisplanner:import-stations
```

### Importing disruptions

In order to import and update the actual disruptions the following command can
be executed.

```bash
docker-compose exec app bin/console ns-reisplanner:import-disruptions
```

### View the application

The application can be reached locally by opening
`http://localhost/departures-and-arrivals` [link](http://localhost/departures-and-arrivals)

## Running the tests

Run `make test` to execute all available tests. There are also more fine-grained
targets, such as `make unit-tests` for only running unit tests. See the
[Makefile](Makefile) for all available targets.
