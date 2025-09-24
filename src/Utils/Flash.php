<?php

namespace App\Utils;

class Flash
{
  public static function set(string $key, mixed $value)
  {
    $_SESSION['flash'][$key] = $value;
  }

  public static function get(string $key)
  {
    if (!isset($_SESSION['flash'][$key])) {
      return null;
    }
    $value = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);
    return $value;
  }

  public static function has(string $key)
  {
    return isset($_SESSION['flash'][$key]);
  }
}