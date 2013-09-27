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
        if (!in_array($method, self::$validMethods)) {
            throw new \InvalidArgumentException('This method is invalid. You should use one of these: '. implode(', ', self::$validMethods) .'.');
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
        if ($target == null ||
           (!is_object($target) && !is_string($target))
        ) {
            throw new \InvalidArgumentException("The target must be a string or an object. The given value: {$target}");
        }

        $this->target = $target;
    }

    public function getTarget()
    {
        return $this->target;
    }


    public function match($method, $uri)
    {
        if ($this->getMethod() == $method &&
            $this->getUri() == $uri &&
            $this->getTarget() != null
        ) {
            return $this->target;
        }

        return false;
    }


}

