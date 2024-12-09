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
$router->get("/workopia/public/listings/create", "ListingController@create");
// $router->get("/workopia/public/listing", "ListingController@show");

// Update Routes
$router->get("/workopia/public/listings/edit/{id}", "ListingController@edit");
$router->put("/workopia/public/listings/{id}", "ListingController@update");

// Dynamic Routes
$router->get("/workopia/public/listings/{id}", "ListingController@show");

// POST Routes
$router->post("/workopia/public/listings", "ListingController@store");

// DELETE Routes
$router->delete("/workopia/public/listings/{id}", "ListingController@destroy");

// Auth & Sign-in routes
$router->get("/workopia/public/auth/register", "UserController@create");
$router->get("/workopia/public/auth/login", "UserController@login");

