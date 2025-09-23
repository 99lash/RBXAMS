<?php

namespace App\Config;

use mysqli;

class Database
{
  private string $host = 'localhost';
  private string $db = 'rbxams_db';
  private string $user = 'root';
  private string $pass = '';

  private mysqli $connection;

  public function __construct()
  {
    $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->db);
    if ($this->connection->connect_error) {
      die("Connection failed: " . $this->connection->connect_error);
    }
  }

  public function getConnection(): mysqli
  {
    return $this->connection;
  }
}