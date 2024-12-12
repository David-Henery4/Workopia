<?php

// return [
//   // "/" => "controllers/home.php",
//   "/workopia/public/" => "controllers/home.php",
//   "/workopia/public/listings" => "controllers/listings/index.php",
//   "/workopia/public/listings/create" => "controllers/listings/create.php",
// ];

// $router->get("/workopia/public/", "controllers/home.php");
// $router->get("/workopia/public/listings", "controllers/listings/index.php");
// $router->get("/workopia/public/listings/create", "controllers/listings/create.php");
// //
// $router->get("/workopia/public/listing", "controllers/listings/show.php");

$router->get("/workopia/public/", "HomeController@index");
$router->get("/workopia/public/listings", "ListingController@index");
$router->get("/workopia/public/listings/create", "ListingController@create", ["auth"]);
// $router->get("/workopia/public/listing", "ListingController@show");

// Search Routes
$router->get("/workopia/public/listings/search", "ListingController@search");


// Update Routes
$router->get("/workopia/public/listings/edit/{id}", "ListingController@edit", ["auth"]);
$router->put("/workopia/public/listings/{id}", "ListingController@update", ["auth"]);

// Dynamic Routes
$router->get("/workopia/public/listings/{id}", "ListingController@show");

// POST Routes
$router->post("/workopia/public/listings", "ListingController@store", ["auth"]);

// DELETE Routes
$router->delete("/workopia/public/listings/{id}", "ListingController@destroy", ["auth"]);

// Auth & Sign-in routes
$router->get("/workopia/public/auth/register", "UserController@create", ["guest"]);
$router->get("/workopia/public/auth/login", "UserController@login", ["guest"]);

$router->post("/workopia/public/auth/register", "UserController@store", ["guest"]);

// Logout
$router->post("/workopia/public/auth/logout", "UserController@logout", ["auth"]);
$router->post("/workopia/public/auth/login", "UserController@authenticate", ["guest"]);