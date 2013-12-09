<?php

namespace Kiev\Router;

class Router
{
    protected $routes = array();

    public function addRoute($route)
    {
        if (is_array($route)) {
            $route = $this->addRouteByArray($route);
        } elseif (!$route instanceof Route) {
            throw new \InvalidArgumentException('The route need to be a valid array or an object.');
        }

        $this->routes[] = $route;
    }

    public function addRouteByArray($route)
    {
        $keys = array('method', 'uri', 'target');

        foreach($keys as $key) {
            if (!array_key_exists($key, $route)) {
                throw new \InvalidArgumentException("The array must have an element {$key} to be a valid route.");
            }
        }

        return new Route($route['method'], $route['uri'], $route['target']);
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    public function run($request)
    {
        $args = $this->parseRequest($request);
        $target = $this->match($args);
        if (!$target) {
            throw new \RuntimeException('No target found to the given request.');
        }

        return $this->dispatch($target, $args);
    }

    public function parseRequest($request)
    {
        if (!isset($request['REQUEST_METHOD']) ||
            !isset($request['REQUEST_URI'])
        ) {
            throw new \InvalidArgumentException('The request could not be handled because the request is not valid.');
        }

        $method = strtoupper($request['REQUEST_METHOD']);
        $params = array();

        $uri = parse_url($request['REQUEST_URI']);
        $uri = $uri['path'];

        if ($uri !== '/') {
            $uriParts = $this->explodeUri($uri);
            $params = $this->extractParamsFromUri($uriParts);
            $uri = $this->rebuildUri($uriParts);
            $uri = trim($uri, '/');
        }

        return array(
            'method' => $method,
            'uri' => $uri,
            'params' => $params,
        );
    }

    public function explodeUri($uri)
    {
        $uri = trim($uri, '/') .'/';

        $pattern = '#[a-zA-Z0-9]{1,}/[a-zA-Z0-9]{0,}#';

        $matches = array();
        preg_match_all($pattern, $uri, $matches);

        return current($matches);
    }

    public function rebuildUri($uriParts)
    {
        foreach ($uriParts as &$part) {
            list($resource, $param) = explode('/', $part);
            $part = $resource;
            if ($param != '') {
                $part .= '/*';
            }
        }

        return implode('/', $uriParts);
    }

    public function extractParamsFromUri($uriParts)
    {
        $params = array();
        foreach($uriParts as $part) {
            list($key, $value) = explode('/', $part);
            if ($value != '') {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    public function match($args)
    {
        foreach ($this->routes as $route) {
            if ($route->match($args['method'], $args['uri'])) {
                return $route->getTarget();
            }
        }

        return false;
    }

    public function dispatch($target, $args)
    {
        if (is_string($target)) {
            $target = new $target;
        }

        return call_user_func_array(array($target, $args['method']), $args['params']);
    }
}

