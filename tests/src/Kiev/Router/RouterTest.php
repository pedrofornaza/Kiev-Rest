<?php

namespace Kiev\Router;

class RouterTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParams()
    {
        $instance = new Router();

        $this->assertInstanceOf('Kiev\Router\Router', $instance);
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
            'uri'    => $uri,
            'target' => $target,
        );

        $instance->addRoute($route);

        $routes = $instance->getRoutes();
        $this->assertContainsOnlyInstancesOf('Kiev\Router\Route', $routes);

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
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => $uri,
            'params' => array(),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithOneResourceOnUri()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/resource';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => ltrim($uri, '/'),
            'params' => array(),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithEndSlash()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/resource/';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => trim($uri, '/'),
            'params' => array(),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithOneResourceAndParamOnUri()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/resource/123';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => 'resource/*',
            'params' => array('resource' => '123'),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithTwoResourcesAndOneParamOnUri()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/first/123/second';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => 'first/*/second',
            'params' => array('first' => '123'),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithTwoResourcesAndTwoParamsOnUri()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/first/123/second/456';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => 'first/*/second/*',
            'params' => array('first' => '123', 'second' => '456'),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testMatchWithoutResource()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';
        $route = new Route($method, $uri, $target);
        $instance->addRoute($route);

        $request = array(
            'method' => $method,
            'uri'    => $uri,
        );

        $matchedTarget = $instance->match($request);
        $this->assertEquals($target, $matchedTarget);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testRunWithoutRoutes()
    {
        $instance = new Router();

        $request = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/',
        );

        $instance->run($request);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testRunWithOneRoute()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';
        $route = new Route($method, $uri, $target);
        $instance->addRoute($route);

        $request = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/otherroute',
        );

        $instance->run($request);
    }
}

