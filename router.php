<?php
$routes = require basePath("routes.php");
// inspect($uri);
// inspect($routes);
// inspect(array_key_exists($uri, $routes));
//
if (array_key_exists($uri, $routes)){
// require basePath($routes[$uri]);
require(basePath($routes[$uri]));
} else {
// require(basePath($routes["/workopia/public/404"]));
http_response_code(404);
require basePath($routes["/workopia/public/404"]);
}