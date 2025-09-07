<?php

namespace App\Controllers;

use App\Models\User;

class HomeController {
  
  public function index() {
    $page = 'home';
    $title = 'Home | RBXAMS';
    // $user = new User('ash', 21);
    // $name= $user->getName();
    // $age = $user->getAge();
    require __DIR__ . '/../Views/index.php';
  }
}