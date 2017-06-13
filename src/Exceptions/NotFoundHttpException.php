<?php
namespace Webcode\WP\Http\Exceptions;

use Exception;

class NotFoundHttpException extends Exception {
    
	public function __construct( $message = NULL, $code = 0, Exception $previous = NULL ) {
        
		parent::__construct( $message, $code, $previous );
        
	}
    
}
