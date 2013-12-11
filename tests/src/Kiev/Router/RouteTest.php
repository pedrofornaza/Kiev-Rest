<?php

namespace Kiev\Router;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithoutParams()
    {
        $instance = new Route();

        $this->assertInstanceOf('Kiev\Router\Route', $instance);
    }

    public function testConstructWithParams()
    {
        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';
        $instance = new Route($method, $uri, $target);

        $this->assertEquals($method, $instance->getMethod());
        $this->assertEquals($uri, $instance->getUri());
        $this->assertEquals($target, $instance->getTarget());
    }

    public function testSetterAndGetterWithValidMethod()
    {
        $method = 'GET';
        $instance = new Route();

        $instance->setMethod($method);

        $this->assertEquals($method, $instance->getMethod());
    }

    public function testSetterAndGetterWithInvalidMethod()
    {
        $this->setExpectedException('InvalidArgumentException');

        $method = 'BAD_METHOD';
        $instance = new Route();

        $instance->setMethod($method);
    }

    public function testSetterAndGetterWithValidUri()
    {
        $uri = '/';
        $instance = new Route();

        $instance->setUri($uri);

        $this->assertEquals($uri, $instance->getUri());
    }

    public function testSetterAndGetterWithDirtyUri()
    {
        $dirtyUri = '/test?query=string#test';
        $cleanUri = 'test';
        $instance = new Route();

        $instance->setUri($dirtyUri);

        $this->assertEquals($cleanUri, $instance->getUri());
    }

    public function testSetterAndGetterTarget()
    {
        $target = 'stdClass';
        $instance = new Route();

        $instance->setTarget($target);

        $this->assertEquals($target, $instance->getTarget());
    }

    public function testMatchWithRightParams()
    {
        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';

        $instance = new Route($method, $uri, $target);

        $this->assertEquals($target, $instance->match($method, $uri));
    }

    public function testMatchWithWrongParams()
    {
        $method = 'GET';
        $uri = '/';
        $target = 'stdClass';

        $instance = new Route($method, $uri, $target);

        $differentMethod = 'POST';
        $differentUri = '/test';

        $this->assertFalse($instance->match($differentMethod, $differentUri));
    }

    public function testMatchWithTwoMethods()
    {
        $method = 'GET|POST';
        $uri = '/';
        $target = 'stdClass';

        $instance = new Route($method, $uri, $target);

        $differentMethod = 'GET';
        $this->assertEquals($target, $instance->match($differentMethod, $uri));

        $differentMethod = 'POST';
        $this->assertEquals($target, $instance->match($differentMethod, $uri));
    }
}