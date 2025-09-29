<?php

namespace App\Utils;

class AccountType
{
  public const PENDING = 'pending';
  public const FASTFLIP = 'fastflip';

  public static function from(string $type): ?string
  {
    $type = strtolower($type);
    return match ($type) {
      self::PENDING => self::PENDING,
      self::FASTFLIP => self::FASTFLIP,
      default => null,
    };
  }
}