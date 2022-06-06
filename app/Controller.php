<?php

namespace App;

abstract class Controller
{
    public $request = null;
    public $view = null;

    public function __construct()
    {
        $this->request = new Request;
        $this->view = new View;
    }

    public function errorPage(int $code = 404, string $message = 'Not Found'): void
    {
        header("HTTP/1.1 {$code} {$message}");
    }
}
