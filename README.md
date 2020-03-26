# HRMS

## HUMAN RESOURCE MANAGEMENT SYSTEM
### Technologies
- [Lumen >= 7.*](https://lumen.laravel.com/)

### Requirements
- PHP >= 7.2
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Composer package manager

### Instructions
- Clone this repository
- CD to project directory
- Run `composer install`
- Run `cp .env.example .env`
- Change the database connection parameters (`DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD`) in the .env file
- Run `php artisan migrate`
- Run `php -S localhost:PORT_NUMBER -t public`
