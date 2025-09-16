<?php

namespace App\Utils;

use App\Utils\IdGeneratorInterface;

class UserId implements IdGeneratorInterface
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
    $segment1 = $self->formattedId('0123456789', 3);
    $segment2 = $self->formattedId('0123456789', 3);
    $segment3 = $self->formattedId('0123456789', 3);
    return "u{$segment1}i{$segment2}d{$segment3}";
  }
}