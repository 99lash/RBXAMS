<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/LoadDotenv.php';
// require '../src/Views/Partials/Header.php';

/**
 * @routers 
 * */

require __DIR__ . '/../src/Routes/web.php';

$requestUri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

$router->resolve($requestUri, $method);
?>