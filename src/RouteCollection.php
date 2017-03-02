<?php

namespace Webcode\WP\Http;

class RouteCollection
{
    private $_routes;
    private $_name_list;

    public function __construct()
    {
        $this->_routes = [];
        $this->_name_list = [];
    }

    public static function get_instance()
    {
        static $instance = NULL;
        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    public function add_route(Route $route)
    {
        $this->_routes[] = &$route;
        $this->regenerate_name_list();

        return $route;
    }

    public function regenerate_name_list()
    {
        $this->_name_list = [];
        foreach ($this->_routes as $_route) {
            if (!empty($_route->name())) {
                $this->_name_list[$_route->method() . '.' . $_route->name()] = $_route;
            }
        }
    }

    public function get_by_name($name)
    {
        foreach ($this->_name_list as $_name => $_route) {
            if ($_name === $name) {
                return $_route;
            }
        }

        return false;
    }

    public function match(Request $request, Response $response, $method, $path)
    {
        foreach ($this->_routes as $route) {
            if ($path === $route->path() && $method === $route->method()) {
                $route->call_action($request, $response);
            }
        }
    }
}