<?php

namespace App;

final class MVC
{
    private $uriParams = null;

    public function __construct()
    {
        $this->setURL();
        $this->route();
    }

    private function route(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET' && $this->uriParams['path'] === '/') {
            (new IndexController)->index();
        } else {
            (new IndexController)->errorPage();
        }
    }

    private function setURL(): void
    {
        $this->uriParams = parse_url($_SERVER['REQUEST_URI']);
    }
}
