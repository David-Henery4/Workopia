<?php
require '../helpers.php';
// loadView("home");

$routes = [
  // "/" => "controllers/home.php",
  "/workopia/public/" => "controllers/home.php",
  "/workopia/public/listings" => "controllers/listings/index.php",
  "/workopia/public/listings/create" => "controllers/listings/create.php",
  "/workopia/public/404" => "controllers/error/404.php",
];

$uri = $_SERVER["REQUEST_URI"];

// inspect($uri);
// inspect($routes);
// inspect(array_key_exists($uri, $routes));
//
if (array_key_exists($uri, $routes)){
  require basePath($routes[$uri]);
  // require(basePath($routes[$uri]));
} else {
  // require(basePath($routes["/workopia/public/404"]));
  require basePath($routes["/workopia/public/404"]);
}