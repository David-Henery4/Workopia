<?php
// loadView("home");
require '../helpers.php';

// Basic Auto-Loader (Custom auto-loader)
// 1st Arg: Callback function
// Callback function, 1st Arg: Class
//
spl_autoload_register(function ($class) {
  $path = basePath("Framework/" . $class . ".php");
  if (file_exists($path)) {
    require $path;
  }
});


// Instantiating the router
$router = new Router();
// Get routes
$routes = require basePath("routes.php");
// Get current URI & HTTP METHOD
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$method = $_SERVER["REQUEST_METHOD"];

// inspect($uri);
// inspect($method);

// Route the request
$router->route($uri, $method);
