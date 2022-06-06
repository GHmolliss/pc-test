<?php

namespace App;

final class Request
{
    private $storage;

    public function __construct()
    {
        $this->storage = $_REQUEST;
    }

    public function __get($name)
    {
        if (isset($this->storage[$name])) {
            return $this->storage[$name];
        }
    }
}
