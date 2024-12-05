<?php
// loadView("home");
require '../helpers.php';
require basePath("Database.php");
require basePath("Router.php");
// Instantiating the router
$router = new Router();
// Get routes
$routes = require basePath("routes.php");
// Get current URI & HTTP METHOD
$uri = $_SERVER["REQUEST_URI"];
$method = $_SERVER["REQUEST_METHOD"];

// inspect($uri);
// inspect($method);

// Route the request
$router->route($uri, $method);



