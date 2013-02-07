<?php

namespace Kiev\Rest;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructWithoutParams()
	{
		$instance = new Router();

		$this->assertInstanceOf('Kiev\Rest\Router', $instance);
	}

	public function testAddRouteWithObjectRoute()
	{
		$instance = new Router();
		$route = new Route('GET', '/', 'stdClass');

		$instance->addRoute($route);

		$this->assertContains($route, $instance->getRoutes());
	}

	public function testAddRouteWithArrayRoute()
	{
		$instance = new Router();
		$method = 'GET';
		$uri = '/';
		$target = 'stdClass';

		$route = array(
			'method' => $method,
			'uri'	 => $uri,
			'target' => $target,
		);

		$instance->addRoute($route);

		$routes = $instance->getRoutes();
		$this->assertContainsOnlyInstancesOf('Kiev\Rest\Route', $routes);

		$routeObject = new Route($method, $uri, $target);
		$this->assertEquals($routeObject, current($routes));
	}

	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testAddRouteWithInvalidRoute()
	{
		$instance = new Router();

		$instance->addRoute('invalid_route');
	}

	public function testParseRequest()
	{
		$instance = new Router();

		$method = 'GET';
		$uri = '/';

		$request = array(
			'REQUEST_METHOD' => $method,
			'REQUEST_URI'	 => $uri,
		);

		$expectedReturn = array(
			'method' => $method, 
			'uri' => $uri,
			'params' => array(),
		);

		$parsedRequest = $instance->parseRequest($request);
		$this->assertEquals($parsedRequest, $expectedReturn);
	}

	public function testMatch()
	{
		$instance = new Router();

		$method = 'GET';
		$uri = '/';
		$target = 'stdClass';
		$route = new Route($method, $uri, $target);
		$instance->addRoute($route);

		$request = array(
			'method' => $method,
			'uri'	 => $uri,
		);

		$matchedTarget = $instance->match($request);

		$this->assertEquals($target, $matchedTarget);
	}


}

