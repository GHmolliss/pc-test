<?php

define('APP_URL', 'http://osp-procode.ru');

define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_NAME', 'procode');

define('ROOT', realpath(__DIR__ . '/../'));
define('ROOT_APP', ROOT . '/App');

spl_autoload_register(function ($className) {
    include ROOT . "/{$className}.php";
});
