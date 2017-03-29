<?php
namespace Webcode\WP\Http;
class RouteGroup
{
    public $closure;
    public $args;

    public function __construct($args, $closure)
    {
        $this->args = $args;
        $this->closure = &$closure;
    }
}