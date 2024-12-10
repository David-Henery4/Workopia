<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;
use Framework\Session;

class UserController
{
  protected $db;
  //
  public function __construct(){
    $config = require basePath("config/db.php");
    $this->db = new Database($config);
  }

  /**
   * Show the login page
   * 
   * @return void
   */
  public function login(){
    loadView("users/login");
  }

  /**
   * Logout user & kill session
   *
   * @return void
   */
  public function logout(){
    Session::clearAll();

    // Gets path and domain the cookie belongs too.
    // Below deletes the cookie
    $params = session_get_cookie_params();
    setcookie("PHPSESSID", "", time() - 86400, $params["path"], $params["domain"]);

    // Then logout
    redirect("/workopia/public/");

  }

  /**
   * Show the register page
   * 
   * @return void
   */
  public function create() {
    loadView("users/create");
  }

  /**
   * Store user in DB
   * @return void
   */
  public function store() {
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

    // Get new UserId
    $userId = $this->db->conn->lastInsertId();

    // Save new UserId to user session
    // Save userId to the session id
    Session::set("user", [
      "id" => $userId,
      "name" => $name,
      "email" => $email,
      "city" => $city,
      "state" => $state,
    ]);

    redirect("/workopia/public/");
  }

  /**
   * Authenticate user with email & password
   *
   * @return void
   */
  public function authenticate(){
    $email = $_POST["email"];
    $password = $_POST["password"];
    //
    $errors = [];

    // Validation
    if (!Validation::email($email)){
      $errors["email"] = "Please enter a valid email";
    }
    if (!Validation::string($password, 6, 50)){
      $errors["password"] = "Password must be at least 6 characters";
    }

    // Check for errors
    if (!empty($errors)){
      loadView("users/login", [
        "errors" => $errors,
      ]);
      exit;
    }

    // Check for email
    $params = [
      "email" => $email,
    ];
    $user = $this->db->query("SELECT * FROM users WHERE email = :email", $params)->fetch();

    if (!$user){
      $errors["email"] = "Incorrect Credentials";
      loadView("users/login", [
        "errors" => $errors,
      ]);
      exit;
    }

    // Check password if correct ($user will exist because we got past the previous check for the user.)

    // Both email check above & password check below should always have the same error message.

    if (!password_verify($password, $user->password)){
      $errors["email"] = "Incorrect Credentials";
      loadView("users/login", [
        "errors" => $errors,
      ]);
      exit;
    }

    // set user session
    Session::set("user", [
      "id" => $user->id,
      "name" => $user->name,
      "email" => $user->email,
      "city" => $user->city,
      "state" => $user->state,
    ]);

    redirect("/workopia/public/");
  }
}
