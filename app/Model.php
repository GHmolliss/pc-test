<?php

namespace App;

use App\DB;

abstract class Model
{

    public $db;

    public function __construct()
    {
        $DB = new DB;
        $this->db = $DB->getPDO();
    }
}
