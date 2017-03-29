<?php
namespace Webcode\WP\Http\Exceptions;

use Exception;

class NotFoundHttpException extends Exception
{
    public function __construct($message = NULL, \Exception $previous = NULL, $code = 0)
    {
        parent::__construct($message, $previous, $code);
    }
}