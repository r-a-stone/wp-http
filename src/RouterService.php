<?php

namespace Webcode\WP\Http;

class RouterService
{
    protected static $http_methods = [
        'GET',
        'PUT',
        'PATCH',
        'POST',
        'DELETE'
    ];

    public function __construct()
    {
        add_action('do_parse_request', [RouterService::class, 'dispatch'], 30, 2);
    }

    public static function get_instance()
    {
        static $instance = NULL;
        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    public static function create_route($method, $path, $action)
    {
        $collection = RouteCollection::get_instance();
        $route = new Route();
        $route->method($method)
            ->path(trim($path, '/'))
            ->action($action);
        $collection->add_route($route);

        return $route;
    }

    public static function dispatch($do_parse)
    {
        $method = strtoupper((isset($_REQUEST['_method'])) ? $_REQUEST['_method'] : $_SERVER['REQUEST_METHOD']);
        $path = self::_get_current_path();
        $request = Request::createFromGlobals();
        $response = new Response();
        $response->prepare($request);
        $collection = RouteCollection::get_instance();
        $collection->match($request, $response, $method, $path);

        return $do_parse;
    }

    private static function _get_current_path()
    {
        $current_path = trim(esc_url_raw(add_query_arg([])), '/');
        $home_path = trim(parse_url(home_url(), PHP_URL_PATH), '/');
        if ($home_path && strpos($current_path, $home_path) === 0) {
            $current_path = trim(substr($current_path, strlen($home_path)), '/');
        }
        $current_path = trim(preg_replace('/\?.*/', NULL, $current_path), '/');

        return $current_path;
    }

    public static function __callStatic($method, $args)
    {
        if (property_exists(RouterService::class, $method)) {
            if (is_callable(RouterService::$method)) {
                return call_user_func_array(RouterService::$method, $args);
            }
        } else {
            if (in_array(strtoupper($method), RouterService::$http_methods)) {
                RouterService::get_instance();

                return RouterService::create_route(strtoupper($method), $args[0], $args[1]);
            }
        }
    }
}