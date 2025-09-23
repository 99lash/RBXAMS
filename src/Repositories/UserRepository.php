<?php

namespace App\Repositories;

use App\Models\UserModel;
use App\Config\Database;
use DateTimeImmutable;

class UserRepository
{
  private \mysqli $mysqli;

  public function __construct()
  {
    $db = new Database();
    $this->mysqli = $db->getConnection();
  }

  public function findALl()
  {
    $users = [];
    $result = $this->mysqli->query("SELECT * FROM users;");
    if ($result) {
      $allUsersData = $result->fetch_all(MYSQLI_ASSOC);
      $result->free();
    }
    foreach ($allUsersData as $userData) {
      $user = new UserModel(
        $userData['id'],
        $userData['user_role_id'],
        $userData['name'],
        $userData['email'],
        $userData['password'],
        $userData['profile_pic_url'] || null
      );
      $users[] = $user->jsonSerialize();
    }
    return $users;
  }

  public function findById($id): ?UserModel
  {
    $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
      // var_dump($row);
      $user = new UserModel(
        $row['id'],
        $row['user_role_id'],
        $row['name'],
        $row['email'],
        $row['password'],
        $row['profile_pic_url'] || null
      );
      $createdAt = new DateTimeImmutable($row['created_at']);
      $updatedAt = new DateTimeImmutable($row['updated_at']);
      $user->setCreatedAt($createdAt)->setUpdatedAt($updatedAt);
      return $user;
    }
    return null;
  }

  public function findByNameOrEmail($nameOrEmail): ?UserModel
  {
    $stmt = '';
    if (filter_var($nameOrEmail, FILTER_VALIDATE_EMAIL)) {
      $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE email = ?");
    } else {
      $stmt = $this->mysqli->prepare("SELECT * FROM users WHERE name = ?");
    }

    $stmt->bind_param("s", $nameOrEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
      return new UserModel(
        $row['id'],
        $row['user_role_id'],
        $row['name'],
        $row['email'],
        $row['password'],
        $row['profile_pic_url'] || null
      );
    }
    return null;
  }
  public function create($id, $userRoleId, $name, $email, $hashed_password, $profilePicUrl) {
    $query = "
      INSERT INTO
        users 
        (id, user_role_id, name, email, password, profile_pic_url)
      VALUES 
        (?, ?, ?, ?, ?, ?);
    ";
    $stmt = $this->mysqli->prepare($query);
    $stmt->bind_param("sissss", $id, $userRoleId, $name, $email, $hashed_password, $profilePicUrl);
    return $stmt->execute();
  }

  /**
   * Update user fields (PATCH-like behavior)
   */
  public function updatePartial(UserModel $user): bool
  {
    $fields = [];
    $params = [];
    $types = "";

    // get_object_vars can see private props only inside the same class,
    // but here we call from Repository, so use jsonSerialize
    $data = $user->jsonSerialize();

    foreach ($data as $column => $value) {
      if ($column === "id") {
        continue; // never update primary key
      }
      if ($value !== null) {
        $fields[] = "{$column} = ?";
        $params[] = $value;
        $types .= is_int($value) ? "i" : "s";
      }
    }

    if (empty($fields)) {
      return false; // nothing to update
    }
    $sql = "UPDATE users SET " . implode(", ", $fields) . " WHERE id = ?";

    $stmt = $this->mysqli->prepare($sql);

    if (!$stmt) {
      throw new \Exception("Prepare failed: " . $this->mysqli->error);
    }

    
    // add the id at the end
    $params[] = $user->getId();
    $types .= "s";
    
    $stmt->bind_param($types, ...$params);
    // var_dump($stmt);
    return $stmt->execute();
  }

  public function deleteById($id) {
    $stmt = $this->mysqli->prepare('DELETE FROM users WHERE id = ?');
    $stmt->bind_param('s', $id);
    return $stmt->execute();
  }
}