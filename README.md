Kiev-Router
==============

Description
-----------

A PHP thin library to handle requests.

Basic Usage
-----------

```php
<?php

use Kiev\Router\Router;
use Kiev\Router\Route;

$router = new Router();

/** Appending routes to router */
$index = new Route('GET', '/', 'App\Index');
$router->addRoute($index);

$users = new Route('GET', '/users', 'App\User\Collection');
$router->addRoute($users);

$user = new Route('GET', '/users/*', 'App\User');
$router->addRoute($user);

$messages = new Route('GET', '/users/*/messages', 'App\User\Message\Collection');
$router->addRoute($messages);

$message = new Route('GET', '/users/*/messages/*', 'App\User\Message');
$router->addRoute($message);

/** Router returns the same thing that your Target class */
echo $router->run($_SERVER);
```

### Status: on development