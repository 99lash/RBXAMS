<?php

namespace App\Models;

class UserModel extends HeroModel implements \JsonSerializable
{
  private string $id;
  private ?int $user_role_id;
  private ?string $name;
  private ?string $email;
  private ?string $password;
  private ?string $profilePicUrl;

  public function __construct(
    string $id = null,
    int $user_role_id = null,
    string $name = null,
    string $email = null,
    string $password = null,
    string $profilePicUrl = null,
  ) {
    $this->id = $id;
    $this->user_role_id = $user_role_id;
    $this->name = $name;
    $this->email = $email;
    $this->password = $password;
    $this->profilePicUrl = $profilePicUrl;
  }

  public function jsonSerialize(): array
  {
    return get_object_vars($this);
  }

  public static function fromArray(array $data): UserModel
  {
    return new UserModel(
      $data['id'] ?? null,
      $data['user_role_id'] ?? null,
      $data['name'] ?? null,
      $data['email'] ?? null,
      $data['password'] ?? null,
      $data['profile_pic_url'] ?? '',
    );
  }

  /** 
   * @desc getter methods  
   */
  public function getId(): string
  {
    return $this->id;
  }

  public function getUserRoleId(): string
  {
    return $this->user_role_id;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function getProfilePicUrl(): string
  {
    return $this->profilePicUrl;
  }

  /** 
   * @desc setter methods 
   */
  public function setId($id): UserModel
  {
    $this->id = $id;
    return $this;
  }

  public function setUserRoleId(?int $userRoleId): UserModel
  {
    $this->userRoleId = $userRoleId;
    return $this;
  }

  public function setName($name): UserModel
  {
    $this->name = $name;
    return $this;
  }

  public function setEmail($email): UserModel
  {
    $this->email = $email;
    return $this;
  }

  public function setPassword($password): UserModel
  {
    $this->password = $password;
    return $this;
  }

  public function setProfilePicUrl($profilePicUrl): UserModel
  {
    $this->profilePicUrl = $profilePicUrl;
    return $this;
  }
}