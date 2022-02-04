# TODOS

### Requirements

  - [PHP 7.4](http://php.net/downloads.php)
  - [Composer](https://getcomposer.org/)


## Installation

```sh
$ composer install
$ touch database/database.sqlite
$ php artisan migrate
$ mv .env.example .env
$ php artisan key:generate
$ php artisan serve
```

To test using
```sh
$ php artisan test
```

## Available API

| Function | Endpoint |
| ------ | ------ |
| Login | POST: {baseurl}/api/login |
| Register | POST: {baseurl}/api/register |
| Index Todos | GET: {baseurl}/api/todos |
| Filter Todos | GET: {baseurl}/api/todos/?status=completed |
| Store Todos | POST: {baseurl}/api/todos |
| Update Todos | PATCH: {baseurl}/api/todos/{id} |
| Update Status PATCH | POST: {baseurl}/api/todos/updatestatus/{id} |
| Delete Todos | DELETE: {baseurl}/api/todos/{id} |
