<?php

namespace App\Controllers;

use App\Models\User;

class UserController {
  
  public function index() {
    $page = 'user';
    $title = 'User | RBXAMS';
    $user = new User('ash', 21);
    $name= $user->getName();
    $age = $user->getAge();
    require __DIR__ . '/../Views/index.php';
  }
}