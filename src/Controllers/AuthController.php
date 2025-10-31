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
      Flash::set('error', 'Invalid credentials');
      return;
    }
    Flash::set('success', 'Welcome back');
    header('location: /');
  }

  public function logoutPost()
  {
    $this->authService->logout();
    header('location: /login');
  }

  public function forgotPasswordGet()
  {
    $this->authManager->requireGuest();
    $page = '/forgot-password';
    $title = 'Forgot Password | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }

  public function forgotPasswordPost()
  {
    $this->authManager->requireGuest();
    $email = $_POST['email'] ?? '';

    if (empty($email)) {
      Flash::set('error', 'Email address is required.');
      header('location: /forgot-password');
      return;
    }

    $response = $this->authService->sendPasswordResetLink($email);

    if ($response['success']) {
      Flash::set('success', $response['message']);
    } else {
      Flash::set('error', $response['message']);
    }
    header('location: /forgot-password');
  }

  public function resetPasswordGet()
  {
    $this->authManager->requireGuest();
    $token = $_GET['token'] ?? '';

    if (empty($token) || !$this->authService->validateResetToken($token)) {
      Flash::set('error', 'Invalid or expired password reset token.');
      header('location: /login');
      return;
    }

    $page = '/reset-password';
    $title = 'Reset Password | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }

  public function resetPasswordPost()
  {
    $this->authManager->requireGuest();
    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (empty($token) || empty($password) || empty($passwordConfirmation)) {
      Flash::set('error', 'All fields are required.');
      header('location: /reset-password?token=' . $token);
      return;
    }

    if ($password !== $passwordConfirmation) {
      Flash::set('error', 'Passwords do not match.');
      header('location: /reset-password?token=' . $token);
      return;
    }

    $response = $this->authService->resetPassword($token, $password);

    if ($response['success']) {
      Flash::set('success', $response['message']);
      header('location: /login');
    } else {
      Flash::set('error', $response['message']);
      header('location: /reset-password?token=' . $token);
    }
  }
}