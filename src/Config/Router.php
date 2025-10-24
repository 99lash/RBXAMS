<?php

namespace App\Config;

class Router
{
  protected array $routes = [];

  private function addRoute(string $method, string $uri, string $controller)
  {
    // Convert params to regex
    // ex: /users/:id(\d+) -> #^/users/(?P<id>\d+)$#
    $pattern = preg_replace_callback(
      '#:([\w]+)(\([^)]+\))?#',
      function ($matches) {
        $paramName = $matches[1];
        $regex = $matches[2] ?? '[^/]+';
        return "(?P<{$paramName}>{$regex})";
      },
      $uri
    );

    $pattern = "#^" . $pattern . "$#";

    $this->routes[$method][] = [
      'pattern' => $pattern,
      'controller' => $controller,
    ];
  }

  public function get($uri, $controller)
  {
    $this->addRoute('GET', $uri, $controller);
  }
  public function post($uri, $controller)
  {
    $this->addRoute('POST', $uri, $controller);
  }
  public function put($uri, $controller)
  {
    $this->addRoute('PUT', $uri, $controller);
  }
  public function patch($uri, $controller)
  {
    $this->addRoute('PATCH', $uri, $controller);
  }
  public function delete($uri, $controller)
  {
    $this->addRoute('DELETE', $uri, $controller);
  }

  public function resolve($uri, $method)
  {
    $uri = '/' . trim($uri, '/'); // normalize

    if (!isset($this->routes[$method])) {
      return $this->notFound();
    }

    foreach ($this->routes[$method] as $route) {
      if (preg_match($route['pattern'], $uri, $matches)) {
        [$controllerName, $methodName] = explode('@', $route['controller']);
        $controllerClass = "App\\Controllers\\$controllerName";
        $controllerInstance = new $controllerClass();

        $params = array_filter(
          $matches,
          fn($key) => !is_int($key),
          ARRAY_FILTER_USE_KEY
        );

        call_user_func_array([$controllerInstance, $methodName], $params);
        return;
      }
    }

    return $this->notFound();
  }

  private function notFound()
  {
    http_response_code(404);
    $title = 'Page not found | RBXAMS';
    $page = '/404';
    require __DIR__ . '/../Views/index.php';
    // TODO: separate 404 page para sa mga authenticated and non-authenticated users.
  }
}
