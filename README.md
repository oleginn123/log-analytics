# Log analytics
Import logs into database and introduce `/api/count` endpoint for fetching log entries count based on given criteria.

## Setup
### Requirements
1) Docker
2) docker-compose

### Steps
1) Navigate into `.docker` directory
2) Run in the terminal `cp .env.example .env`
3) In the `.env` file you've just the copied you have to change:
   - `APP_CODE_PATH_HOST` - path to the root folder of the application
   - `DATA_PATH_HOST` - where it is convenient, volumes of some services will write data there, for example `mysql`
4) From the root directory run in the terminal:
   - `make start`
   - `make app-install`
6) Go to http://localhost - should work

## Logs import
Import log entries is performing by cron schedule (in the `scheduler` container).
Import process can be run manually:
```shell
make ssh
bin/console log-analytics:import-logs --filePath=<path/to/log/file/in/container>
```

For sample file the CLI command would be:
```shell
bin/console log-analytics:import-logs --filePath=/var/www/html/import/logs.log
```

## Count API
Count API endpoint available by url: `http://localhost/api/count`
