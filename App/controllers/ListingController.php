<?php

namespace App\controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

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
    $listings = $this->db->query("SELECT * FROM listings ORDER BY created_at DESC")->fetchAll();
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
   * @param array $params
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
   * Store new listing in DB
   *
   * @return void
   */
  public function store()
  {
    $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];
    //
    $newListingsData = array_intersect_key($_POST, array_flip($allowedFields));
    //
    $newListingsData["user_id"] = Session::get("user")["id"];

    // We use the sanitize function to make sure only a string is rendered,
    // otherwise someone could pass in HTML code or something similar, and that
    // would get rendered.
    $newListingsData = array_map("sanitize", $newListingsData);
    //
    $requiredFields = ["title", "description", "email", "city", "state", "salary"];
    $errors = [];
    //
    foreach ($requiredFields as $field) {
      if (empty($newListingsData[$field]) || !Validation::string($newListingsData[$field])) {
        $errors[$field] = ucfirst($field) . " is required";
      }
    }
    //
    if (!empty($errors)) {
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
      foreach ($newListingsData as $field => $value) {
        $fields[] = $field;
      }
      $fields = implode(", ", $fields);
      //
      $values = [];
      foreach ($newListingsData as $field => $value) {
        // Convert empty strings to null;
        if ($value === "") {
          $newListingsData[$field] = null;
        }
        $values[] = ":" . $field;
      }
      $values = implode(", ", $values);
      //
      $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";
      $this->db->query($query, $newListingsData);

      Session::setFlashMessage("success_message", "Listing created successfully");

      redirect("/workopia/public/listings");
    }
  }

  /**
   * Delete/Destroy listing
   * @param array $params
   * @return void
   */
  public function destroy($params)
  {
    $id = $params["id"];
    $params = [
      "id" => $id,
    ];
    //
    $listing = $this->db->query("SELECT * FROM listings WHERE id = :id", $params)->fetch();
    
    // Check if listing exists
    if (!$listing) {
      ErrorController::notFound("Listing not found");
      return;
    }
    
    // Authorization
    if (!Authorization::isOwner($listing->user_id)){
      Session::setFlashMessage("error_message","You are not authorized to delete this listing");
      return redirect("/workopia/public/listings/" . $listing->id);
    }

    $this->db->query("DELETE FROM listings WHERE id = :id", $params);

    // Set flash message
    Session::setFlashMessage("success_message", "Listing deleted successfully");

    //
    redirect("/workopia/public/listings");
  }

  /** Show listing Edit form
   *
   * @param array $params
   * @return void
   */
  public function edit($params){
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

    // Authorization
    if (!Authorization::isOwner($listing->user_id)) {
      Session::setFlashMessage("error_message", "You are not authorized to update this listing");
      return redirect("/workopia/public/listings/" . $listing->id);
    }

    loadView("listings/edit", [
      "listing" => $listing,
    ]);
  }

  /**
   * update listing
   *
   * @param array $params
   * @return void
   */
  public function update($params){
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

    // Authorization
    if (!Authorization::isOwner($listing->user_id)) {
      Session::setFlashMessage("error_message", "You are not authorized to update this listing");
      return redirect("/workopia/public/listings/" . $listing->id);
    }

    $allowedFields = ["title", "description", "salary", "tags", "company", "address", "city", "state", "phone", "email", "requirements", "benefits"];

    $updateValues = [];

    $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

    $updateValues = array_map("sanitize", $updateValues);

    $requiredFields = ["title", "description", "email", "city", "state", "salary"];

    $errors = [];

    foreach ($requiredFields as $field) {
      if (empty($updateValues[$field]) || !Validation::string($updateValues[$field])) {
        $errors[$field] = ucfirst($field) . " is required";
      }
    }

    if (!empty($errors)){
      loadView("listings/edit", [
        "listing" => $listing,
        "errors" => $errors,
      ]);
      exit;
    } else {
      // Submit to database
      $updateFields = [];
      foreach (array_keys($updateValues) as $field){
        $updateFields[] = "{$field} = :{$field}";
      }
      $updateFields = implode(", ", $updateFields);
      $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";
      $updateValues["id"] = $id;
      $this->db->query($updateQuery, $updateValues);
      Session::setFlashMessage("success_message", "Listing Updated successfully");
      redirect("/workopia/public/listings/{$id}");
    }
    
  }
};
