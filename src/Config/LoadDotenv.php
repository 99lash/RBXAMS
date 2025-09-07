<?php

namespace App\Config;

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// echo $_ENV['MEOW'];
// echo $_ENV['API_KEY'];