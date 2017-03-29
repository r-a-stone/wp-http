<?php
namespace Webcode\WP\Http;
class RouteService
{
    public $default_namespace;
    public $global_middleware;

    public function __construct($default_namespace, $global_middleware)
    {
        $this->default_namespace = $default_namespace;
        $this->global_middleware = $global_middleware;
    }

    public static function run()
    {
        $collection = RouteCollection::get_instance();
        $method = strtoupper((isset($_REQUEST['_method'])) ? $_REQUEST['_method'] : $_SERVER['REQUEST_METHOD']);
        $path = self::_get_current_path();
        $route = $collection->match($method, $path);

        return $route->run();
    }

    private static function _get_current_path()
    {
        $parts = explode('?', $_SERVER['REQUEST_URI']);

        return $parts[0];
    }

    public static function group($args, $closure)
    {

        $collection = RouteCollection::get_instance();
        $group = new RouteGroup($args, $closure);
        $collection->add_group($group);
        $closure = $group->closure;
        $closure();
        $collection->clear_group_stack();
    }

    public static function get($path, $closure)
    {
        return self::_create_route('GET', $path, $closure);
    }

    public static function put($path, $closure)
    {
        return self::_create_route('PUT', $path, $closure);
    }

    public static function patch($path, $closure)
    {
        return self::_create_route('PATCH', $path, $closure);
    }

    public static function post($path, $closure)
    {
        return self::_create_route('POST', $path, $closure);
    }

    public static function delete($path, $closure)
    {
        return self::_create_route('DELETE', $path, $closure);
    }

    public static function dump_routes()
    {
        $collection = RouteCollection::get_instance();
        var_dump($collection->routes);
    }

    private static function _create_route($method, $path, $closure)
    {
        $service = self::get_instance();
        $collection = RouteCollection::get_instance();
        $middleware = [];
        $prefix = '';
        $name = '';
        if (count($collection->group_stack)) {
            foreach ($collection->group_stack as $group) {
                $middleware = (isset($group->args['middleware'])) ? $group->args['middleware'] : [];
                $prefix .= (isset($group->args['prefix'])) ? '/' . $group->args['prefix'] : '';
                $name .= (isset($group->args['name'])) ? $group->args['name'] : '';
            }
        }
        $route = new Route($method, self::_assemble_path($prefix, $path), $closure);
        $route->middleware(array_merge($service->global_middleware, $middleware));
        $route->set_namespace($service->default_namespace);
        $route->name($name);
        $collection->add_route($route);

        return $route;
    }

    private static function _assemble_path($prefix, $path)
    {
        $prefix = explode('/', $prefix);
        foreach ($prefix as $key => $value) {
            if (empty($value)) {
                unset($prefix[$key]);
            }
        }
        $path = explode('/', $path);
        foreach ($path as $key => $value) {
            if (empty($value)) {
                unset($path[$key]);
            }
        }

        return array_merge($prefix, $path);
    }

    public static function get_instance($default_namespace = '\\', $global_middleware = [])
    {
        static $instance = NULL;
        if (is_null($instance)) {
            $instance = new static($default_namespace, $global_middleware);
        }

        return $instance;
    }

}