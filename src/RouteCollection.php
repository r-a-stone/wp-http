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
        $this->_name_list[$route->method() . '.' . $route->name()] = &$route;
        return $route;
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