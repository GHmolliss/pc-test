<?php

namespace App;

final class Product
{
    public $id = 0;
    public $name = '';
    public $href = '';
    public $src = '';
    public $price = 0;

    public function getPriceFormatted(): string
    {
        return number_format($this->price, 2, ',', ' ');
    }
}
