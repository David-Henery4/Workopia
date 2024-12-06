<?php

namespace Framework;

use App\controllers\ErrorController;

class Router {
  protected $routes = [];
  //
  /**
   * Add a new route
   *
   * @param string $method
   * @param string $uri
   * @param string $controller
   * @return void
   */
  public function registerRoute($method,$uri, $action){
    list($controller, $controllerMethod) = explode("@", $action);
    //
    $this->routes[] = [
      "method" => $method,
      "uri" => $uri,
      "controller" => $controller,
      "controllerMethod" => $controllerMethod,
    ];
  }
  //
  /**
   * Add a GET router
   * @param string $url
   * @param string $controller
   * @return void
   */
  public function get($uri, $controller){
    $this->registerRoute("GET", $uri, $controller);
  }
  //
  /**
   * Add a POST router
   * @param string $url
   * @param string $controller
   * @return void
   */
  public function post($uri, $controller){
    $this->registerRoute("POST", $uri, $controller);
  }
  //
  /**
   * Add a PUT router
   * @param string $url
   * @param string $controller
   * @return void
   */
  public function put($uri, $controller){
    $this->registerRoute("PUT", $uri, $controller);
  }
  //
  /**
   * Add a DELETE router
   * @param string $url
   * @param string $controller
   * @return void
   */
  public function delete($uri, $controller){
    $this->registerRoute("DELETE", $uri, $controller);
  }
  //
  /**
   * Route Request
   * @param string $uri
   * @param string $method
   * @return void
   */
  public function route($uri, $method){
    foreach($this->routes as $route){
      if ($route["uri"] === $uri && $route["method"] === $method){
        $controller = "App\\Controllers\\" . $route["controller"];
        $controllerMethod = $route["controllerMethod"];
        // Insatiate the controller class and call the controller method
        $controllerInstance = new $controller();
        $controllerInstance->$controllerMethod();
        return;
      }
    }
    // Reminder: we can use the scoped resolution operator if calling static function on a class.
    ErrorController::notFound();
  }
}