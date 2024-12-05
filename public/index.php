<?php
// loadView("home");
require '../helpers.php';
require basePath("Framework/Database.php");
require basePath("Framework/Router.php");
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



