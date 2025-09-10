<?php

namespace App\Controllers;

class AssetController {
  public function index() {
    $page = '/accounts';
    $title = 'Manage Accounts | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }

  public function create() {
    $page = '/accounts/new';
    $title = 'Create New Asset | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }
}