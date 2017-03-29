<?php
namespace Webcode\WP\Http;

class Url
{
    public static function name($route_name)
    {
        $method = 'GET';
        $collection = RouteCollection::get_instance();
        if(is_array($route_name)) {
            $method = $route_name[0];
            $route_name = $route_name[1];
        }
        $routes = $collection->routes[$method];
        foreach ($routes as $path => $route) {
            if ($route->name === $route_name) {
                return $path;
            }
        }
    }
}