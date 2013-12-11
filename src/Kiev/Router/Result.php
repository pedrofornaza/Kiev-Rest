<?php

namespace Kiev\Router;

class Result
{
	protected $target;
	protected $method;
	protected $params = array();

	public function __construct($target, $method, $params = array())
	{
		$this->target = $target;
		$this->method = $method;
		$this->params = $params;
	}

	public function getTarget()
	{
		return $this->target;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function getParams()
	{
		return $this->params;
	}
}