Kiev-Router [![Build Status](https://travis-ci.org/pedrofornaza/Kiev-Router.png)](https://travis-ci.org/pedrofornaza/Kiev-Router)
==============

Description
-----------

A PHP thin library to handle requests.

Installation
------------

You can install via composer:

```json
{
	"require": {
        "kiev/router": "dev-master"
    }
}
```

Basic Usage
-----------

```php
<?php

use Kiev\Router\Router;
use Kiev\Router\Route;

$router = new Router();

/** Appending routes to router */
$index = new Route('GET', '/', 'my_controller');
$router->addRoute($index);

$users = new Route('GET', '/users', 'my_controller');
$router->addRoute($users);

$user = new Route('GET', '/users/*', 'my_controller');
$router->addRoute($user);

$messages = new Route('GET', '/users/*/messages', 'my_controller');
$router->addRoute($messages);

$message = new Route('GET', '/users/*/messages/*', 'my_controller');
$router->addRoute($message);

/** Router returns a Kiev\Router\Result instance **/
$result = $router->run($_SERVER);
echo 'My target is: ' . $result->getTarget();
echo 'The method used is: ' . $result->getMethod();
echo 'The params are: ' . PHP_EOL . print_r($result->getParams());
```

Routing
-------

Kiev routes URIs based on resources, pairing resources and resource identifiers. The identifier is not required but when you want to use him, you can use a `*` to represent it as a parameter. Here are some exemples:

```
/
/users/
/users/*/
/users/*/messages/
/users/*/messages/*/
```

Warning: When routing, Kiev will ignore query strings.

Routes
------

Routes need three parameters on constructor:

* method: HTTP method
* uri
* target: Class names, closures, custom names or anything you want

You can use both class or arrays to configure the router. If you use an array, internally, the router will create a Route object:

```php
<?php

use Kiev\Router\Router;
use Kiev\Router\Route;

$router = new Router();

/** Appending routes to router */
$index = new Route('GET', '/', 'my_controller');
$router->addRoute($index);

$users = array('method' => 'GET', 'uri' => '/users', 'target' => 'my_controller');
$router->addRoute($users);
```

When creating a Route, it validates the method so, `InvalidArgumentException` will be throw if the method is not a valid HTTP method (GET, POST, PUT, DELETE, HEAD).

Result
------

The result object is just to represent the matched route and have only three properties:

* target: the target you've specified on route;
* method: the request method;
* params: an array of params found on the uri;

They can be reached by accessor methods:

```php
<?php

// ...

$result = $router->run($_SERVER);
echo 'My target is: ' . $result->getTarget();
echo 'The method used is: ' . $result->getMethod();
echo 'The params are: ' . PHP_EOL . print_r($result->getParams());
```

Dispatching
-----------

The dispatch is not the goal of the project so you will have to deal with it by yourself. You can do it with your class names, closures, aliases or anything you want to. Here are some examples:

```php
<?php

// Class Names

$route = new Route('GET', '/', 'MyPack\MyController');
$router->addRoute($route);

$result = $router->run($_SERVER);
$controller = $result->getTarget();
$method = $result->getMethod();
$params = $result->getParams();

$instance = new $controller;
echo $instance->$method($params);

// Closures

$route = new Route('GET', '/', function($params) { print_r($params) });
$router->addRoute($route);

$result = $router->run($_SERVER);
$target = $result->getTarget();
$params = $result->getParams();

echo call_user_func_array($target, $params);
```