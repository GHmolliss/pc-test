<?php

namespace App;

final class View
{
    public $data = [];

    public function assign(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->data[$key] = $value;
        }
    }

    public function display(string $filename): void
    {
        $this->view("{$filename}.php");
    }

    private function view($filepath): void
    {
        try {
            require_once ROOT_APP . "/{$filepath}";
        } catch (\Exception $e) {
            throw new \Exception("Unknown file '{$filepath}'");
        }
    }
}
