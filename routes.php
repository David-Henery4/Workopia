<?php

// return [
//   // "/" => "controllers/home.php",
//   "/workopia/public/" => "controllers/home.php",
//   "/workopia/public/listings" => "controllers/listings/index.php",
//   "/workopia/public/listings/create" => "controllers/listings/create.php",
// ];

$router->get("/workopia/public/", "controllers/home.php");
$router->get("/workopia/public/listings", "controllers/listings/index.php");
$router->get("/workopia/public/listings/create", "controllers/listings/create.php");
//
$router->get("/workopia/public/listing", "controllers/listings/show.php");

