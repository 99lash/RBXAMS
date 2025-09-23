<?php

namespace App\Controllers;

use App\Services\UserService;
class UserController
{
  private UserService $service;

  public function __construct()
  {
    $this->service = new UserService();
  }
  public function listAll()
  {
    header('Content-Type: application/json');
    $users = $this->service->getAllUsers();
    echo json_encode($users);
  }

  public function getById($id)
  {
    header('Content-Type: application/json');
    $user = $this->service->getById($id);
    // echo json_encode($id);
    if (empty($user))
      echo json_encode([]);
    else
      echo json_encode($user);
  }


  public function create() {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'];
    $profilePicUrl = $_POST['profilePicUrl'] ?? '';

    header('Content-Type: application/json');
    $response = $this->service->createUser($name, $email, $password, $profilePicUrl);
    echo json_encode([
      "success" => $response
    ]);    
  }


  public function updateById($id)
  {
    // echo $id;
    $response = $this->service->updateUserById($id);
    header('Content-Type: application/json');
    echo json_encode([
      "success" => $response,
    ]);
  }

  public function deleteById($id)
  {
    $response = $this->service->deleteUserById($id);
    header('Content-Type: application/json');

    if ($response == null) {
      echo \json_encode(["detail" => "User not found"]);
      return;
    }
    if (!$response) {
      echo json_encode(["success" => $response]);
      return;
    }
    echo json_encode([
      "success" => $response,
      "detail" => "User successfully deleted"
    ]);
  }
}