<?php

namespace App\Controllers;

// use App\Models\User;

class HomeController {
  public function __construct() {
    $this->page = 'home';
    $this->title = 'Dashboard | RBXAMS';
  }

  public function index() {
    $page = '/';
    $age = 'Dashboard | RBXAMS';
    // $user = new User('ash', 21);
    // $name= $user->getName();
    // $age = $user->getAge();
    require __DIR__ . '/../Views/index.php';
  }
}