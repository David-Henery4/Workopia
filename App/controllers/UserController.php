<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;

class UserController
{
  protected $db;
  //
  public function __construct()
  {
    $config = require basePath("config/db.php");
    $this->db = new Database($config);
  }

  /**
   * Show the login page
   * 
   * @return void
   */
  public function login()
  {
    loadView("users/login");
  }

  /**
   * Show the register page
   * 
   * @return void
   */
  public function create()
  {
    loadView("users/create");
  }

  /**
   * Store user in DB
   * @return void
   */
  public function store()
  {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $city = $_POST["city"];
    $state = $_POST["state"];
    $password = $_POST["password"];
    $passwordConfirmation = $_POST["password_confirmation"];
    //
    $errors = [];

    // Validation

    // Email Check
    if (!Validation::email($email)) {
      $errors["email"] = "Please enter a valid email address";
    }

    // Name Length
    if (!Validation::string($name, 2, 50)) {
      $errors["name"] = "Name must be between 2 and 50 characters";
    }

    // Password length
    if (!Validation::string($password, 6, 50)) {
      $errors["password"] = "Password must be at least 6 characters";
    }

    // Password match
    if (!Validation::match($password, $passwordConfirmation)) {
      $errors["password_confirmation"] = "Passwords do not match";
    }

    if (!empty($errors)) {
      loadView("users/create", [
        "errors" => $errors,
        "user" => [
          "name" => $name,
          "email" => $email,
          "city" => $city,
          "state" => $state,
        ]
      ]);
      exit;
    }

    // Check if email exists
    $params = [
      "email" => $email
    ];

    $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();

    if ($user) {
      $errors["email"] = "That email already exists";
      loadView("users/create", [
        "errors" => $errors
      ]);
      exit;
    }

    // Create user Account
    $params = [
      "name" => $name,
      "email" => $email,
      "city" => $city,
      "state" => $state,
      "password" => password_hash($password, PASSWORD_DEFAULT),
    ];

    $this->db->query("INSERT INTO users (name, email, city, state, password) VALUES (:name, :email, :city, :state, :password)", $params);

    redirect("/workopia/public/");
  }
}
