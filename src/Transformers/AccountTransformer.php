<?php

namespace App\Transformers;

// use App\Models\AccountModel;
use App\DTOs\AccountResponseDTO;
use DateTimeImmutable;

class AccountTransformer
{
  public static function transform(array $data)
  {

    // var_dump($data);
    $account = $data['model'];
    $statusName = $data['status'];
    $costRatePhp = $account->getRobux() > 0
      ? $account->getCostPhp() / ($account->getRobux() / 1000)
      : 0;

    $createdAt = $account->getCreatedAt() ?? null;
    $updatedAt = $account->getUpdatedAt() ?? null;
    $deletedAt = $account->getDeletedAt() ?? null;

    return [
      'id' => $account->getId(),
      // 'user_id' => $account->getUserId(),
      'name' => $account->getName(),
      'account_type' => $account->getAccountType(),
      'status' => $statusName, // Galing sa JOIN
      'robux' => $account->getRobux(),
      'cost_php' => $account->getCostPhp(),
      'cost_rate_php' => round($costRatePhp, 2), // Calculated on the fly
      'price_php' => $account->getPricePhp(),
      'profit_php' => $account->profit_php, // Calculated from the model's constructor
      'used_to_php_rate_on_sale' => $account->getUsdToPhpRateOnSale(),
      'sold_rate_usd' => $account->getSoldRateUsd(),
      'unpend_date' => $account->getUnpendDate(),
      'sold_date' => $account->getSoldDate(),
      'date_added' => $createdAt?->format('Y-m-d H:i:s'),
      'date_updated' => $updatedAt?->format('Y-m-d H:i:s'),
      'date_deleted' => $deletedAt?->format('Y-m-d H:i:s'),
    ];
    // return AccountResponseDTO::create($account, $statusName, $costRatePhp);
  }

  public static function transformCollection(array $accounts): array
  {
    return array_map([self::class, 'transform'], $accounts);
  }
}