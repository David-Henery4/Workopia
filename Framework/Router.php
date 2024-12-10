<?php

namespace Framework;

use App\controllers\ErrorController;
use Framework\Middleware\Authorize;

class Router
{
  protected $routes = [];
  
  /**
   * Add a new route
   *
   * @param string $method
   * @param string $uri
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function registerRoute($method, $uri, $action, $middleware = []) {
    list($controller, $controllerMethod) = explode("@", $action);
    //
    $this->routes[] = [
      "method" => $method,
      "uri" => $uri,
      "controller" => $controller,
      "controllerMethod" => $controllerMethod,
      "middleware" => $middleware,
    ];
  }
  
  /**
   * Add a GET router
   * @param string $url
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function get($uri, $controller, $middleware = []){
    $this->registerRoute("GET", $uri, $controller, $middleware);
  }
  
  /**
   * Add a POST router
   * @param string $url
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function post($uri, $controller, $middleware = []){
    $this->registerRoute("POST", $uri, $controller, $middleware);
  }
  
  /**
   * Add a PUT router
   * @param string $url
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function put($uri, $controller, $middleware = []){
    $this->registerRoute("PUT", $uri, $controller, $middleware);
  }
  
  /**
   * Add a DELETE router
   * @param string $url
   * @param string $controller
   * @param array $middleware
   * @return void
   */
  public function delete($uri, $controller, $middleware = []){
    $this->registerRoute("DELETE", $uri, $controller, $middleware);
  }
  
  /**
   * Route Request
   * @param string $uri
   * @param string $method
   * @return void
   */
  public function route($uri) {
    $requestMethod = $_SERVER["REQUEST_METHOD"];
    //

    // Check for _method input
    if ($requestMethod === "POST" && isset($_POST["_method"])){
      // Override the request method with the value of _method
      $requestMethod = strtoupper($_POST["_method"]);
    }

    //
    foreach ($this->routes as $route) {

      // Split current URI into segments
      $uriSegments = explode("/", trim($uri, "/"));

      // Split Route URI into segments
      $routeSegments = explode("/", trim($route["uri"], "/"));

      $match = true;

      if (count($uriSegments) === count($routeSegments) && strtoupper($route["method"]) === strtoupper($requestMethod)) {
        $params = [];
        $match = true;
        //
        for ($i = 0; $i < count($uriSegments); $i++) {

          // If the uri's don't match, there is no param.
          if ($routeSegments[$i] !== $uriSegments[$i] && !preg_match('/\{(.+?)\}/', $routeSegments[$i])) {
            $match = false;
            break;
          }

          // Check for param and add to $params array.
          if (preg_match('/\{(.+?)\}/', $routeSegments[$i], $matches)) {
            $params[$matches[1]] = $uriSegments[$i];
            // inspectAndDie($params);
          }
        }

        if ($match) {
          
          foreach($route["middleware"] as $middleware){
            (new Authorize())->handle($middleware);
          }

          $controller = "App\\Controllers\\" . $route["controller"];
          $controllerMethod = $route["controllerMethod"];
          // Insatiate the controller class and call the controller method
          $controllerInstance = new $controller();
          $controllerInstance->$controllerMethod($params);
          return;
        }
      }

      // if ($route["uri"] === $uri && $route["method"] === $method){
      //   $controller = "App\\Controllers\\" . $route["controller"];
      //   $controllerMethod = $route["controllerMethod"];
      //   // Insatiate the controller class and call the controller method
      //   $controllerInstance = new $controller();
      //   $controllerInstance->$controllerMethod();
      //   return;
      // }
    }
    // Reminder: we can use the scoped resolution operator if calling static function on a class.
    ErrorController::notFound();
  }
}
