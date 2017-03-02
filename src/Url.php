<?php
namespace Webcode\WP\Http;

class Url
{
    public function __construct()
    {
    }

    public static function get_instance()
    {
        static $instance = NULL;
        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    public static function name($route_name, $method = 'GET')
    {
        $collection = RouteCollection::get_instance();
        $route = $collection->get_by_name($method . '.' . $route_name);
        if ($route) {
            return $route->path();
        }
    }
}