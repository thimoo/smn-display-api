# SwissMetNet Display API

The SwissMetNet Display API is a web service for retrieving public weather information from a network of Swiss stations. The service stops the information every ten minutes over 24 hours and offers an API to consume them.

## Data source

The data stored into the service come from the url stores in `csv_target_url` in the `config/constants.php` file.

## Server

To start the server run `php artisan serve`. The server will start by default on port 8000.

## Custom commands

To check if new data are available in the csv run `php artisan database:refresh` or `php artisan database:refresh`. This command will download the csv and check if new data can be inserted into the database.



To update the station information the command `php artisan profiles:refresh` can be run. The information is stored in the file `storage/csv/stations_infos.csv`.

## Register the Scheduler

Custom commands are registered with the scheduler. To execute it every minute you can add `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1` in your crontab job.

## Installation

To install the project on a development or a production server run :

```
php composer install
php artisan migrate
php artisan db:seed
```

## Configuration

### Configuring the service

Some constraint values are stored in the `config/constants.php` file. It's possible to change the behavior of the web service with these values.

### Environment file

A .env file is required to define the configuration of the database and the environment (development or production). This file must have these lines at least :

```
APP_ENV=...
APP_KEY=...
APP_DEBUG=...
APP_LOG_LEVEL=...
APP_URL=...

DB_CONNECTION=...
DB_HOST=...
DB_PORT=...
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```
