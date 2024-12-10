<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize {

  /**
   * Check if user authenticated
   *
   * @return boolean
   */
  public function isAuthenticated(){
    return Session::has('user');
  }

  /**
   * Handle the users request
   *
   * @param string $role
   * @return boolean
   */
  public function handle($role){
    if ($role === "guest" && $this->isAuthenticated()){
      return redirect("/workopia/public/");
    } elseif($role === "auth" && !$this->isAuthenticated()) {
      return redirect("/workopia/public/auth/login");
    }
  }

}
