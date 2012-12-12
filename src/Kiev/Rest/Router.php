<?php

namespace Kiev\Rest;

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

	public function match($request) {
		if (!isset($request['REQUEST_METHOD']) ||
			!isset($request['REQUEST_URI'])
		) {
			throw new \InvalidArgumentException('The request could not be handled because the request is not valid.');
		}

		$method = strtoupper($request['REQUEST_METHOD']);
		$uri = parse_url($request['REQUEST_URI']);
		$uri = $uri['path'];
		if ($uri !== '/') {
			$uri = rtrim($uri, '/');
		}

		foreach ($this->routes as $route) {
			if ($route->match($method, $uri)) {
				return $route->getTarget();
			}
		}

		return false;
	}


}

