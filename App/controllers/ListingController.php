<?php

namespace App\controllers;

use Framework\Database;
use Framework\Validation;

// use Framework\Validation;

class ListingController
{
  protected $db;
  //
  public function __construct()
  {
    $config = require basePath("config/db.php");
    $this->db = new Database($config);
  }

  /**
   * Show all listings
   *
   * @return void
   */
  public function index()
  {
    $listings = $this->db->query("SELECT * FROM listings")->fetchAll();
    //
    loadView("listings/index", [
      "listings" => $listings,
    ]);
  }

  /**
   * Show the create listings form
   *
   * @return void
   */
  public function create()
  {
    loadView("listings/create");
  }

  /**
   * Show single listing
   *
   * @return void
   */
  public function show($params)
  {
    $id = $params["id"] ?? "";
    //
    $params = [
      "id" => $id
    ];
    //
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();

    // Check if listings exists
    if (!$listing) {
      ErrorController::notFound("Listing not found");
      return;
    }

    //
    loadView("listings/show", [
      "listing" => $listing,
    ]);
  }

  /**
   * Undocumented function
   *
   * @return void
   */
  public function store() {
    $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];
    //
    $newListingsData = array_intersect_key($_POST, array_flip($allowedFields));
    //
    $newListingsData["user_id"] = 3;

    // We use the sanitize function to make sure only a string is rendered,
    // otherwise someone could pass in HTML code or something similar, and that
    // would get rendered.
    $newListingsData = array_map("sanitize", $newListingsData);
    //
    $requiredFields = ["title", "description", "email", "city", "state", "salary"];
    $errors = [];
    //
    foreach($requiredFields as $field){
      if (empty($newListingsData[$field]) || !Validation::string($newListingsData[$field])){
        $errors[$field] = ucfirst($field) . " is required";
      }
    }
    //
    if (!empty($errors)){
      //reload view with errors
      loadView("/listings/create", [
        "errors" => $errors,
        "listing" => $newListingsData
      ]);
    } else {
      // submit data
      echo "success";
      //
      $fields = [];
      foreach($newListingsData as $field => $value){
        $fields[] = $field;
      }
      $fields = implode(", ", $fields);
      //
      $values = [];
      foreach ($newListingsData as $field => $value) {
        // Convert empty strings to null;
        if ($value === ""){
          $newListingsData[$field] = null;
        }
        $values[] = ":" . $field;
      }
      $values = implode(", ", $values);
      //
      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
      $this->db->query($query, $newListingsData);
      redirect("/workopia/public/listings");
    }
  }
};
