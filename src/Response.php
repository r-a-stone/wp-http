<?php

namespace Webcode\WP\Http;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class Response extends SymfonyResponse
{
    private $_title_called = false;

    public function view($template, array $data = [])
    {
        if (function_exists('get_bloginfo')) {
            if (!$this->_title_called) {
                $this->page_title([get_bloginfo('name')]);
            }
        }
        View::make($template, $data);
    }

    public function page_title(array $title_parts = [], $separator = '|')
    {
        $this->_title_called = true;
        $page_title = implode(' ' . $separator . ' ', $title_parts);
        if (function_exists('add_filter')) {
            add_filter('pre_get_document_title', function ($title) use ($page_title) {
                return $page_title;
            }, 10);
        }
        //Supports Yoast SEO
        if (function_exists('add_filter')) {
            add_filter('wpseo_title', function ($title) use ($page_title) {
                return $page_title;
            }, 15);
        }
    }

    public function send()
    {
        parent::send();
        die();
    }
}