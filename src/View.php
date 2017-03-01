<?php
namespace Webcode\Http;

class View
{

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
        foreach ($data as $var => $val) {
            $var = 'data_' . $var;
            $$var = $val;
            global $$var;
        }
        $path = $view->find_template($template);
        if ($path) {
            add_filter('template_include', function ($template) use ($path) {
                $template = $path;

                return $template;
            });
        }
    }

    public function find_template($path)
    {
        $path = str_replace('.', DIRECTORY_SEPARATOR, $path);
        foreach ($this->_locations as $location) {
            if (file_exists($location . $path . '.php')) {
                return $location . $path . '.php';
            }
        }

        return false;
    }
}