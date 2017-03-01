<?php

namespace Webcode\WP\Http;

class Route
{
    private $_name;
    private $_method;
    private $_path;
    private $_middleware;
    private $_action;

    public function __construct()
    {
        $this->_name = '';
        $this->_method = '';
        $this->_path = '';
        $this->_action = '';
        $this->_middleware = [];
    }

    public function name($name = NULL)
    {
        if (is_null($name)) {
            return $this->_name;
        }
        $this->_name = $name;
    }

    public function method($method = NULL)
    {
        if (is_null($method)) {
            return $this->_method;
        }
        $this->_method = $method;
    }

    public function path($path = NULL)
    {
        if (is_null($path)) {
            return $this->_path;
        }
        $this->_path = $path;
    }

    public function action($action = NULL)
    {
        if (is_null($action)) {
            return $this->_action;
        }
        $this->_action = $action;
    }

    public function middleware(array $middleware = NULL)
    {
        if (is_null($middleware)) {
            return $this->_middleware;
        }
        $this->_middleware = array_merge($this->_middleware, $middleware);
    }

    public function call_action(Request $request, Response $response)
    {
        $this->_init_middleware();
        $this->_call_middleware_before($request, $response);
        if (is_callable($this->_action)) {
            $action = $this->_action;
            $action($request, $response);
        } else {
            $controller = NULL;
            $action = NULL;
            if (is_array($this->_action)) {
                $controller = $this->_action[0];
                $action = $this->_action[1];
            } else if (strpos($this->_action, '@') !== false) {
                $this->_action = explode('@', $this->_action);
                $controller = $this->_action[0];
                $action = $this->_action[1];
            }
            $controller_instance = new $controller();
            $controller_instance->$action($request, $response);
        }
        $this->_call_middleware_after($request, $response);
    }

    private function _init_middleware()
    {
        foreach ($this->_middleware as $middleware) {
            if (is_callable($middleware)) {
                $this->_middleware[$middleware] = new $middleware();
            }
        }
    }

    private function _call_middleware_before(Request $request, Response $response)
    {
        foreach ($this->_middleware as $middleware) {
            if (is_callable($middleware->before)) {
                $middleware->before($request, $response);
            }
        }
    }

    private function _call_middleware_after(Request $request, Response $response)
    {
        foreach ($this->_middleware as $middleware) {
            if (is_callable($middleware->after)) {
                $middleware->after($request, $response);
            }
        }
    }

}