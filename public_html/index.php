<?php

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);


require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Config/LoadDotenv.php';

/**
 * @routers 
 * */

require __DIR__ . '/../src/Routes/web.php';

$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($requestUri, $method);