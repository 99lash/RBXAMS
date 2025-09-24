<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Security\SessionManager;

class AuthService
{
  private UserRepository $repo;

  public function __construct()
  {
    $this->repo = new UserRepository();
  }

  public function login($nameOrEmail, $password): bool
  {
    

    $user = $this->repo->findByNameOrEmail($nameOrEmail);
    if (!$user || !password_verify($password, $user->getPassword())) {
      return false;
    }
    $_SESSION['user'] = [
      'id' => $user->getId(),
      'name' => $user->getName(),
      'email' => $user->getEmail(),
      'role' => $user->getUserRoleId(),
      'profilePicUrl' => $user->getProfilePicUrl()
    ];
    return true;
  }

  public function logout()
  {
    SessionManager::destroy();
  }

  public function currentUser(): array
  {
    return $_SESSION['user'] ?? null;
  }

  public function checkAuth(): bool
  {
    return isset($_SESSION['user']);
  }
}