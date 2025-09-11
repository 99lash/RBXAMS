<?php

$db_host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$db_username = $_ENV['DB_USERNAME'];
$db_password = $_ENV['DB_PASSWORD'];

try {
  $con = new mysqli($db_host, $db_username, $db_password);

  if ($con->connect_errno) {
    throw new Exception("Connection Error: " . $con->connect_errno);
  }
  $database_creation_query = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
  if (!$con->query($database_creation_query)) {
    throw new Exception('Database Creation Failed: ' . $con->error);
  }
  // echo "Database `$db_name` created (or already exists)";
  $con->select_db($db_name);

  $filepath = __DIR__ . '/../../table_schemas.sql';
  $table_schemas_file = fopen($filepath, 'r') or die('Unable to open file');
  $table_schemas_query = fread($table_schemas_file, filesize(($filepath)));
  // echo $table_schemas_query;
  $queries = explode(';', $table_schemas_query);

  foreach ($queries as $query) {
    $query = trim($query);
    if ($query) {
      if (!$con->query($query)) {
        echo "Error in query: " . $con->error . PHP_EOL;
        throw new Exception('Table Creation Failed: ' . $con->error);
      }
    }
  }
  fclose($table_schemas_file);
  // echo "Table created successfully.";

} catch (Exception $e) {
  echo $e->getMessage();
}

