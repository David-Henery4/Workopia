<?php

namespace Framework;

class Validation {
  
  /**
   * Validate String
   * @param string $value
   * @param int $min
   * @param int $max
   * @return bool
   */
  public static function string($value, $min = 1, $max = INF){

    if (is_string($value)){
      $value = trim($value);
      $length = strlen($value);
      return $length >= $min && $length <= $max;
    }

    return false;
  }

  /**
   * Validate Email
   * @param string $value
   * @return mixed
   */
  public static function email($value){
    $value = trim($value);
    //
    return filter_var($value, FILTER_VALIDATE_EMAIL );
  }

  /**
   * Match one Value against another
   *
   * @param string $value1
   * @param string $value2
   * @return boolean
   */
  public static function match ($value1, $value2){
    return trim($value1) === trim($value2);
  }
}