<?php

namespace Kiev\Router;

class ResultTest extends \PHPUnit_Framework_TestCase
{
	public function testConstruct()
    {
        $target = 'test_target';
        $method = 'GET';
        $params = array();

        $instance = new Result($target, $method, $params);

        $this->assertInstanceOf('Kiev\Router\Result', $instance);
    }

    public function testGetTarget()
    {
    	$target = 'test_target';
        $method = 'GET';
        $params = array();

        $instance = new Result($target, $method, $params);

        $this->assertEquals($target, $instance->getTarget());
    }

    public function testGetMethod()
    {
    	$target = 'test_target';
        $method = 'GET';
        $params = array();

        $instance = new Result($target, $method, $params);

        $this->assertEquals($method, $instance->getMethod());
    }

    public function testGetParams()
    {
    	$target = 'test_target';
        $method = 'GET';
        $params = array();

        $instance = new Result($target, $method, $params);

        $this->assertEquals($params, $instance->getParams());
    }
}