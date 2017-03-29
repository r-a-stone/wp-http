<?php
namespace Webcode\WP\Http;

use Exception;

class RouteCollection
{
    public $routes;
    public $group_stack;

    public function __construct()
    {
        foreach (Route::$http_methods as $method) {
            $this->routes[$method] = [];
        }

    }

    public function add_route(Route $route)
    {
        $this->routes[$route->method] = array_merge($this->routes[$route->method], [$route->path => &$route]);
    }

    public function add_group(RouteGroup $group)
    {
        $this->group_stack[] = $group;
    }

    public function clear_group_stack()
    {
        $this->group_stack = [];
    }

    public function match($method, $uri)
    {
        $route = $this::_match_route_simple($method, $uri);
        if (!$route) {
            $route = $this::_match_route_complex($method, $uri);
        }
        if (!$route) {
            throw new Exception('No match found');
        }

        return $route;
    }

    private static function _match_route_complex($method, $uri)
    {
        $collection = self::get_instance();
        foreach ($collection->routes[$method] as $path => $route) {
            $path_params = [];
            $path_parts = explode('/', $path);
            $path_pattern = '/^\/';
            foreach ($path_parts as $index => $path_part) {
                if (!empty($path_part)) {
                    if (strpos($path_part, '{') !== false) {
                        $path_params[] = [
                            'index' => $index,
                            'name'  => str_replace(['{', '?', '}'], '', $path_part)
                        ];
                        if (strpos($path_part, '?') !== false) {
                            $path_pattern .= '(.*?(?=\/?))\/?';
                        } else {
                            $path_pattern .= '(.*(?=\/?))\/?';
                        }
                    } else {
                        $path_pattern .= '((?i)' . $path_part . '(?-i))\/?';
                    }
                }
            }
            $path_pattern .= '$/';
            if (preg_match($path_pattern, $uri, $matches)) {
                $params = [];
                foreach ($path_params as $param) {
                    $params[$param['name']] = str_replace('/', '', $matches[$param['index']]);
                }
                $route->params = $params;

                return $route;
            }
        }

        return false;
    }

    private static function _match_route_simple($method, $uri)
    {
        $collection = self::get_instance();
        foreach ($collection->routes[$method] as $path => $route) {
            if ($path == $uri) {
                return $route;
                break 1;
            }
        }

        return false;
    }

    public static function get_instance()
    {
        static $instance = NULL;
        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }
}