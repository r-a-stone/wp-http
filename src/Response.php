<?php

namespace Webcode\WP\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Webcode\Http\View;

class Response extends SymfonyResponse
{
    public function view($template, array $data = []) {
        View::make($template, $data);
    }

    public function send() {
        parent::send();
        die();
    }
}