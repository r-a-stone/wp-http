<?php
namespace Webcode\WP\Http;

class View
{
    public $data = [];
    private $_locations = [];

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

    public function add_location($location)
    {
        $this->_locations[] = $location;
    }

    public static function make($template, array $data = [])
    {
        $view = self::get_instance();
        $view->data = $data;
        $path = $view->find_template($template);

        if ($path) {
            if (function_exists('add_filter')) {
                add_filter('template_include', function ($template) use ($path, $data) {
                    $template = $path;

                    return $template;
                });
            }
        }
    }

    public static function data($key)
    {
        $view = View::get_instance();

        return $view->data[$key];
    }

    public function find_template($path)
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        foreach ($this->_locations as $location) {
            if (file_exists($location . DIRECTORY_SEPARATOR . $path . '.php')) {
                return $location . DIRECTORY_SEPARATOR . $path . '.php';
            }
        }

        return false;
    }

}