<?php
namespace Webcode\WP\Http;

class Route
{
    public $name;
    public $method;
    public $path;
    public $middleware_stack;
    public $namespace;
    public $closure;
    public $params;

    private $_request;
    private $_response;


    public static $http_methods = [
        'HEAD',
        'GET',
        'PUT',
        'PATCH',
        'POST',
        'DELETE'
    ];

    public function __construct($method, $path, $closure)
    {
        $this->method = $method;
        $this->name = NULL;
        $this->path = '/' . implode('/', $path);
        $this->middleware_stack = [];
        $this->namespace = '\\';
        $this->closure = $closure;
        $this->params = [];
    }

    public function middleware(array $middleware)
    {
        $this->middleware_stack = array_merge($this->middleware_stack, $middleware);

        return $this;
    }

    public function set_namespace($namespace)
    {
        $this->namespace = $namespace;

        return $this;
    }

    public function name($name)
    {
        $this->name .= $name;

        return $this;
    }

    public function run()
    {
        $this->_request = Request::createFromGlobals();
        $this->_response = new Response();
        $this->_response->prepare($this->_request);
        $this->_set_url_params();
        $this->_run_middleware();
        if (is_callable($this->closure)) {
            return $this->_call_closure();
        } else {
            return $this->_call_controller_method();
        }
    }

    private function _set_url_params()
    {
        foreach ($this->params as $var => $val) {
            $this->_request->query->set($var, $val);
        }
    }

    private function _run_middleware()
    {
        foreach ($this->middleware_stack as $middleware) {
            $middleware($this->_request, $this->_response);
        }
    }

    private function _call_closure()
    {
        $closure = $this->closure;

        return $closure($this->_request, $this->_response);
    }

    private function _call_controller_method()
    {
        $parts = explode('@', $this->closure);
        $namespace = $this->namespace;
        $controller = $namespace . $parts[0];
        $method = $parts[1];
        $controller_instance = new $controller($this->_request, $this->_response);

        return $controller_instance->$method();
    }
}