<?php

namespace App\DTOs;

use App\Models\AccountModel;

class AccountResponseDTO
{

  public function __construct(
    public int $id,
    public string $user_id,
    public string $account_type,
    public string $status,
    public string $name,
    public float $robux,
    public ?float $cost_php,
    public ?float $cost_rate_php,
    public ?float $price_php,
    public ?float $profit_php,
    public ?float $usd_to_php_rate_on_sale,
    public ?float $sold_rate_usd,
    public ?string $unpend_date,
    public ?string $sold_date,
    public ?string $created_at,
    public ?string $updated_at,
    public ?string $deleted_at
  ) {
  }

  public static function create(AccountModel $account, string $status, float $costRatePhp): self
  {
    $createdAt = $account->getCreatedAt() ?? null;
    $updatedAt = $account->getUpdatedAt() ?? null;
    $deletedAt = $account->getDeletedAt() ?? null;

    return new self(
      $account->getId(),
      $account->getUserId(),
      $account->getAccountType(),
      $status,
      $account->getName(),
      $account->getRobux(),
      $account->getCostPhp(),
      round($costRatePhp, 2),
      $account->getPricePhp(),
      $account->getProfitPhp(),
      $account->getUsdToPhpRateOnSale(),
      $account->getSoldRateUsd(),
      $account->getUnpendDate(),
      $account->getSoldDate(),
      $createdAt?->format('Y-m-d H:i:s'),
      $updatedAt?->format('Y-m-d H:i:s'),
      $deletedAt?->format('Y-m-d H:i:s')
    );
  }
}