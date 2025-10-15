<?php

namespace App\Models;

use DateTimeImmutable;

class HeroModel
{
  private ?DateTimeImmutable $created_at;
  private ?DateTimeImmutable $updated_at;

  public function __construct(?DateTimeImmutable $created_at, ?DateTimeImmutable $updated_at = null) {
    $this->created_at = $created_at;
    $this->updated_at = $updated_at;
  }

  /**
   * @desc getter methods 
   */
  public function getCreatedAt(): DateTimeImmutable
  {
    return $this->created_at;
  }
  public function getUpdatedAt(): ?DateTimeImmutable
  {
    return $this->updated_at;
  }

  /**
   * @desc setter methods
   */
  public function setCreatedAt(DateTimeImmutable $dateNow): HeroModel {
    $this->created_at = $dateNow;
    return $this;
  }
  public function setUpdatedAt(DateTimeImmutable $dateNow): HeroModel {
    $this->created_at = $dateNow;
    return $this;
  }
}