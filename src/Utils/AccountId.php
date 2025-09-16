<?php

namespace App\Utils;

use App\Utils\IdGeneratorInterface;

class AccountId implements IdGeneratorInterface
{
  /**
   * @var \Hidehalo\Nanoid\Client $nanoid The client of nanoid-php library..
   */
  private $nanoid;

  /**
   * @param \Hidehalo\Nanoid\Client $client The client of nanoid-php library invoked from a constructor param.
   */
  public function __construct($client)
  {
    $this->nanoid = $client;
  }
  public function generate(): string
  {
    $self = $this->nanoid;
    $mode = $self::MODE_DYNAMIC;
    $segment1 = $self->generateId(6, $mode);
    $segment2 = $self->generateId(6, $mode);
    $segment3 = $self->generateId(6, $mode);
    return "a{$segment1}i{$segment2}d{$segment3}";
  }
}