<?php

namespace App\Security;

use App\Services\AuthService;

class AuthManager
{
  private AuthService $authService;

  public function __construct()
  {
    $this->authService = new AuthService();
  }

  public function requireAuth()
  {
    if (!$this->authService->checkAuth()) {
      http_response_code(401);
      header('Location: /login');
      exit;
    }
    return $this->authService->currentUser();
  }

  public function requireGuest()
  {
    if ($this->authService->checkAuth()) {
      http_response_code(203);
      header('Location: /');
      exit;
    }
  }
}
// TODO: custom middleware hahaha fuck around and find out, aalis lang saglit taena.