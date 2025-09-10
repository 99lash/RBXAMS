<?php

namespace App\Controllers;

class AuthController {

  public function login() {
    //TODO: need to implement a middleware to check if user is already login para maprevent yung pag access ng page na 'to.
    $page = '/login';
    $title = 'Sign in | RBXAMS';
    require __DIR__ . '/../Views/index.php'; 
  }

  public function register() {
    //TODO: need to implement a middleware to check if user is already login para maprevent yung pag access ng page na 'to.
    $page = '/register';
    $title = 'Sign up | RBXAMS ';
    require __DIR__ . '/../Views/index.php';
  }
}