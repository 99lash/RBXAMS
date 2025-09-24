<?php

namespace App\Controllers;

use App\Security\AuthManager;
use App\Services\AuthService;
use App\Services\UserService;
use App\Utils\Flash;

class AuthController
{
  private UserService $userService;
  private AuthService $authService;
  private AuthManager $authManager;
  public function __construct()
  {
    $this->userService = new UserService();
    $this->authService = new AuthService();
    $this->authManager = new AuthManager();
  }
  public function loginGet()
  {
    $this->authManager->requireGuest();
    $page = '/login';
    $title = 'Sign in | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }

  public function loginPost()
  {
    $this->authManager->requireGuest();
    $nameOrEmail = $_POST['nameOrEmail'] ?? '';
    $password = $_POST['password'] ?? '';
    $response = $this->authService->login($nameOrEmail, $password);
    header('Content-Type: application/json');
    if (!$response) {
      http_response_code(401);
      header('location: /login');
      // echo json_encode([
      //   "success" => false,
      //   "detail" => "Invalid credentials"
      // ]);
      Flash::set('error', 'Invalid credentials');
      return;
    }
    // echo json_encode([
    //   "success" => true,
    //   "detail" => "Login success"
    // ]);
    Flash::set('success', 'Welcome back');
    header('location: /');
  }

  public function logoutPost()
  {
    $this->authService->logout();
    header('location: /login');
  }
}