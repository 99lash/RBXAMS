<?php

namespace App\Config;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Define BASE_URL constant
if (!defined('BASE_URL') && isset($_ENV['BASE_URL'])) {
    if (isset($_ENV['ENV_MODE']) && $_ENV['ENV_MODE'] == 'prod') {
        define('BASE_URL', $_ENV['BASE_URL']);
    } else {
        define('BASE_URL', 'http://rbxams.local');
    }
}