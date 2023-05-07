# SimpleRouter

SimpleRouter is a lightweight and easy-to-use PHP router for building web applications.

## Features

- Simple and intuitive syntax
- Support for dynamic routes with URL parameters
- Support for HTTP methods (GET, POST, PUT, PATCH, and DELETE)
- Easily customizable
- Lightweight and fast

## Requirements

- PHP >= 8.1

## Installation

You can install SimpleRouter using Composer:

```
composer require devceliojr/simplerouter
```

## Usage

Here's a simple example of how to use SimpleRouter:

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use SimpleRouter\Application\Router;

$router = new Router;

$router->get('/', function () {
    echo 'Hello, world!';
});

$router->dispatch();
```

This will create a new router, define a route for the homepage, and run the router.

You can also define dynamic routes with URL parameters:

```php
$router->get('/users/{id:number}', function ($request) {
    echo "User {$request->body->id}";
});
```

In this example, any URL that matches the pattern `/users/{id:number}` will be handled by the callback function, which will receive the value of `id` as an argument.

The supported parameter types are:

- `number`: accepts only numeric values
- `letter`: accepts only alphabetical characters
- `alpha`: accepts alphanumeric characters
- `any`: accepts any character

You can use HTTP methods other than GET by calling the corresponding function on the router:

```php
$router->post('/users', function () {
    echo "Creating user...";
});
```

In this example, the callback function will be executed when a POST request is made to the `/users` URL.

You can also use HTTP verbs with dynamic routes:

```php
$router->put('/users/{id:number}', function ($request) {
    echo "Updating user {$request->body->id}...";
});
```

In this example, the callback function will be executed when a PUT request is made to a URL that matches the pattern `/users/{id:number}`.

For more complex applications, you can use nested routes and subgroups:

```php
$router->group(null, function () use ($router) {
    $router->get("/", [HomeController::class, "home"]);
});

$router->group("/admin", function () use ($router) {
    $router->get("/", [AdminController::class, "show"]);
    $router->subgroup("/financial", function () use ($router) {
        $router->get("/", [AdminController::class, "financial"]);
    });
});
```

In this example, the `HomeController` class handles the root URL, while the `AdminController` class handles URLs under the `/admin` prefix. The `financial` method of the `AdminController` class is accessed by navigating to `/admin/financial`.

For more information on how to use SimpleRouter, please refer to the [documentation](https://github.com/devceliojr/SimpleRouter/blob/main/README.md).

## Contributing

Contributions are welcome! If you'd like to contribute to SimpleRouter, please open an issue or pull request on [GitHub](https://github.com/devceliojr/SimpleRouter).

## License

SimpleRouter is open-source software licensed under the [MIT license](https://github.com/devceliojr/SimpleRouter/blob/main/LICENSE).