<?php

namespace App\Controllers;

use App\Security\AuthManager;

class SummaryController
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
    $page = '/summary';
    $title = 'Daily Summary | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }
}