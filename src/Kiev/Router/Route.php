<?php

namespace Kiev\Router;

class Route
{
    protected $method;
    protected static $validMethods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD');

    protected $uri;
    protected $target;

    public function __construct($method = 'GET', $uri = '/', $target = null)
    {
        $this->setMethod($method);
        $this->setUri($uri);

        if ($target != null) {
            $this->setTarget($target);
        }
    }

    public function setMethod($method)
    {
        $method = strtoupper($method);
        $methods = explode('|', $method);

        foreach ($methods as $methodPart) {
            if (!in_array($methodPart, self::$validMethods)) {
                throw new \InvalidArgumentException('This method is invalid. You should use one of these: '. implode(', ', self::$validMethods) .'.');
            }
        }

        $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }


    public function setUri($uri)
    {
        $uriParts = parse_url($uri);
        $uri = isset($uriParts['path']) ? $uriParts['path'] : '/';

        if ($uri !== '/') {
            $uri = trim($uri, '/');
        }

        $this->uri = $uri;
    }

    public function getUri()
    {
        return $this->uri;
    }


    public function setTarget($target)
    {
        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }


    public function match($method, $uri)
    {
        if (strpos($this->getMethod(), $method) !== false &&
            $this->getUri() == $uri &&
            $this->getTarget() != null
        ) {
            return $this->target;
        }

        return false;
    }


}

