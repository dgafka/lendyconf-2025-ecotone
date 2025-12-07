<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

// Force test environment
$_SERVER['APP_ENV'] = 'test';
$_ENV['APP_ENV'] = 'test';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if (isset($_SERVER['APP_DEBUG'])) {
    umask(0000);
}
