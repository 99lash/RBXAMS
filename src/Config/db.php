<?php

/**
 * @NOTE: load database credentials from '.env' file. 
 */
$dbHost = $_ENV['DB_HOST'];
$dbName = $_ENV['DB_NAME'];
$dbUsername = $_ENV['DB_USERNAME'];
$dbPassword = $_ENV['DB_PASSWORD'];
$envMode = $_ENV['ENV_MODE'] ?? getenv('ENV_MODE') ?: 'dev';

try {
  $con = new mysqli($dbHost, $dbUsername, $dbPassword);
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  if ($envMode !== 'prod') {
    /**
     * @NOTE: execute the creation of database and tables if environment mode is 'dev' or 'test'    
     */
    $database_creation_query = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    if (!$con->query($database_creation_query)) {
      throw new Exception('Database Creation Failed: ' . $con->error);
    }
    // echo "Database `$db_name` created (or already exists)";
    $con->select_db($dbName);

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
  } else {
    /**
     * @NOTE: assuming that database is created in production.
     */
    $con->select_db($dbName);
  }
} catch (Exception $e) {
  if ($envMode == 'prod') {
    error_log('[DB INIT]' . $e->getMessage());
  } else {
    echo $e->getMessage();
  }
}