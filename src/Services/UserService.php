<?php

namespace App\Services;

use App\Models\UserModel;
use App\Repositories\UserRepository;
use App\Utils\IdGeneratorFactory;
use App\Utils\IdType;

class UserService
{
  private UserRepository $repo;

  public function __construct()
  {
    $this->repo = new UserRepository();
  }

  public function getAllUsers(): array
  {
    return $this->repo->findAll();
  }

  public function getById($id): ?UserModel
  {
    return $this->repo->findById($id);
  }

  public function getByNameOrEmail($nameOrEmail): UserModel
  {
    return $this->repo->findByNameOrEmail($nameOrEmail);
  }

  public function createUser($name, $email, $password, $profilePicUrl)
  {
    $userId = (IdGeneratorFactory::createId(IdType::USER))->generate();
    $hashed_password = password_hash($password, PASSWORD_BCRYPT); 
    return $this->repo->create($userId, 3, $name, $email, $hashed_password, $profilePicUrl);
  }

  public function updateUserById($id)
  {
    $patchData = json_decode(file_get_contents("php://input"), true);

    // var_dump($patchData);

    if (!$patchData) {
      throw new \Exception("Invalid PATCH body");
    }

    $user = new UserModel($id);
    // $user->setId($id);

    // Dynamically map PATCH data into model setters if they exist
    foreach ($patchData as $field => $value) {
      $method = "set" . str_replace(' ', '', ucwords(str_replace('_', ' ', $field)));
      if (method_exists($user, $method)) {
        $user->$method($value);
      }
    }
    // var_dump($user);
    return $this->repo->updatePartial($user);
  }

  public function deleteUserById($id): bool|null
  {
    $user = $this->repo->findById($id);

    if (!$user) {
      return null;
    }

    return $this->repo->deleteById($id);
  }
}