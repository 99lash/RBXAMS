<?php

namespace App\Models;
class User
{
  private $name;
  private $age;

  public function __construct($name = null, $age = null)
  {
    $this->name = $name;
    $this->age = $age;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setName($name) {
    $this->name = $name;
    return $this;
  }
  public function getAge()
  {
    return $this->age;
  }

  public function setAge($age) {
    $this->age = $age;
    return $this;
  }
  
}