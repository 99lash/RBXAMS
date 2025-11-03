<?php

namespace App\Security;

class SessionManager
{
  public static function start()
  {
    // 6 months cookie lifetime to prevent logout on browser close
    $cookieLifetime = 15552000; 
    ini_set('session.cookie_lifetime', $cookieLifetime);
    ini_set('session.gc_maxlifetime', $cookieLifetime); // Also update server-side lifetime
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.cookie_samesite', 'Strict');

    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }

    $timeout = $_ENV['TIMEOUT_DURATION'] ?? 15552000; // 6 months
    // echo $timeout;
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > intval($timeout))) {
      self::destroy();
    }
    $_SESSION['LAST_ACTIVITY'] = time();
  }

  public static function regenerate()
  {
    session_regenerate_id(true);
  }

  public static function destroy()
  {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
      );
    }
    session_destroy();
  }
}