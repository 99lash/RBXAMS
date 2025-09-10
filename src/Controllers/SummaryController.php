<?php

namespace App\Controllers;

class SummaryController {
  public function index() {
    $page = '/summary';
    $title = 'Daily Summary | RBXAMS';
    require __DIR__ . '/../Views/index.php';
  }
}