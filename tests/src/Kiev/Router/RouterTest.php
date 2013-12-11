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

    public function testAddRouteWithArrayRouteWithoutTargetKey()
    {
        $this->setExpectedException('InvalidArgumentException');

        $instance = new Router();

        $route = array(
            'method' => 'GET',
            'uri'    => '/',
        );

        $instance->addRoute($route);
    }

    public function testAddRouteWithArrayRouteWithoutUriKey()
    {
        $this->setExpectedException('InvalidArgumentException');

        $instance = new Router();

        $route = array(
            'method' => 'GET',
            'target' => 'stdClass',
        );

        $instance->addRoute($route);
    }

    public function testAddRouteWithArrayRouteWithoutMethodKey()
    {
        $this->setExpectedException('InvalidArgumentException');

        $instance = new Router();

        $route = array(
            'uri'    => '/',
            'target' => 'stdClass',
        );

        $instance->addRoute($route);
    }

    public function testAddRouteWithInvalidRoute()
    {
        $this->setExpectedException('InvalidArgumentException');

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

    public function testParseRequestWithoutUri()
    {
        $this->setExpectedException('InvalidArgumentException');

        $instance = new Router();

        $request = array(
            'REQUEST_METHOD' => 'GET'
        );

        $instance->parseRequest($request);
    }

    public function testParseRequestWithoutMethod()
    {
        $this->setExpectedException('InvalidArgumentException');

        $instance = new Router();

        $request = array(
            'REQUEST_URI' => '/',
        );

        $instance->parseRequest($request);
    }

    public function testParseRequestWithQueryString()
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

    public function testParseRequestWithoutResourceAndQueryString()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/?query=string';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => '/',
            'params' => array(),
        );

        $parsedRequest = $instance->parseRequest($request);
        $this->assertEquals($expectedReturn, $parsedRequest);
    }

    public function testParseRequestWithResourceAndQueryString()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/resource/?query=string';

        $request = array(
            'REQUEST_METHOD' => $method,
            'REQUEST_URI'    => $uri,
        );

        $expectedReturn = array(
            'method' => $method,
            'uri' => 'resource',
            'params' => array(),
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

    public function testRunWithoutRoutes()
    {
        $this->setExpectedException('RuntimeException');

        $instance = new Router();

        $request = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/',
        );

        $instance->run($request);
    }

    public function testRunWithOneRoute()
    {
        $this->setExpectedException('RuntimeException');

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

    public function testRunWithValidRequestAndRoute()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';
        $route = new Route($method, $uri, $target);
        $instance->addRoute($route);

        $request = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/',
        );

        $result = $instance->run($request);

        $this->assertInstanceOf('Kiev\Router\Result', $result);
        $this->assertEquals($target, $result->getTarget());
        $this->assertEquals($method, $result->getMethod());
        $this->assertEquals(array(), $result->getParams());
    }

    public function testRunWithValidRequestWithParamsAndRoute()
    {
        $instance = new Router();

        $method = 'GET';
        $uri = '/resource/*';
        $target = 'stdClass';
        $route = new Route($method, $uri, $target);
        $instance->addRoute($route);

        $request = array(
            'REQUEST_METHOD' => 'GET',
            'REQUEST_URI'    => '/resource/1',
        );

        $result = $instance->run($request);

        $params = array('resource' => 1);

        $this->assertInstanceOf('Kiev\Router\Result', $result);
        $this->assertEquals($target, $result->getTarget());
        $this->assertEquals($method, $result->getMethod());
        $this->assertEquals($params, $result->getParams());
    }
}