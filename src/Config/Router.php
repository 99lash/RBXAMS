<?php

namespace App\Config;

class Router
{
  protected $routes = [];

  public function get($uri, $controller)
  {
    $this->routes['GET'][$uri] = $controller;
  }

  public function post($uri, $controller)
  {
    $this->routes['POST'][$uri] = $controller;
  }
  public function put($uri, $controller)
  {
    $this->routes['PUT'][$uri] = $controller;
  }
  public function patch($uri, $controller)
  {
    $this->routes['PATCH'][$uri] = $controller;
  }

  public function delete($uri, $controller)
  {
    $this->routes['DELETE'][$uri] = $controller;
  }

  public function resolve($uri, $method)
  {
    $uri = '/' . $uri;
    // var_dump($this->routes);
    // var_dump($uri, $method);
    // var_dump(array_key_exists($uri, $this->routes[$method]));
    if (array_key_exists($uri, $this->routes[$method])) {
      $controller = $this->routes[$method][$uri];
      [$controllerName, $methodName] = explode('@', $controller);
      // print_r($controllerName);
      // print_r($methodName);
      $controllerClass = "App\\Controllers\\$controllerName";
      $controllerInstance = new $controllerClass();
      $controllerInstance->$methodName();
      return;
    }
    http_response_code(404);
    $title = 'Page not found | RBXAMS';
    $page = '404';
    require __DIR__ . '/../Views/index.php';
  }
}