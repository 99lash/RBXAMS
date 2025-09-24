<?php

namespace App\Controllers;

use App\Security\AuthManager;

class HomeController
{
  private AuthManager $authManager;
  private array $currentUser;
  public function __construct()
  {
    $this->authManager = new AuthManager();
    $this->currentUser = $this->authManager->requireAuth();
  }

  public function index()
  {
    // var_dump($this->currentUser);
    // var_dump($_SESSION['user']);
    $page = '/';
    $title = 'Dashboard | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }
}