<?php

namespace App\Models;

use DateTimeImmutable;
use App\Utils\AccountType;

class AccountModel extends HeroModel
{
  private ?int $id;
  private ?string $user_id;
  private ?string $account_type;
  private ?int $account_status_id;
  private ?string $name;
  private ?string $cookie_enc;
  private ?float $robux;
  private ?float $cost_php;
  private ?float $price_php;
  // ++ ADDED: Stores the exchange rate at the time of sale for historical accuracy.
  private ?float $usd_to_php_rate_on_sale;
  private ?float $sold_rate_usd;
  private ?DateTimeImmutable $unpend_date;
  private ?DateTimeImmutable $sold_date;
  private ?DateTimeImmutable $deleted_at;

  // ++ ADDED: This public property will hold the calculated profit.
  public ?float $profit_php;

  public function __construct(
    int $id = null,
    string $user_id = null,
    string $account_type = null,
    int $account_status_id = null,
    string $name = null,
    string $cookie_enc = null,
    float $robux = null,
    float $cost_php = null,
    float $price_php = null,
    // ++ ADDED: New property for the constructor.
    float $usd_to_php_rate_on_sale = null,
    float $sold_rate_usd = null,
    DateTimeImmutable $unpend_date = null,
    DateTimeImmutable $sold_date = null,
    DateTimeImmutable $created_at = null,
    DateTimeImmutable $updated_at = null,
    DateTimeImmutable $deleted_at = null
  ) {
    $this->id = $id;
    $this->user_id = $user_id;
    $this->account_type = $account_type;
    $this->account_status_id = $account_status_id;
    $this->name = $name;
    $this->cookie_enc = $cookie_enc;
    $this->robux = $robux;
    $this->cost_php = $cost_php;
    $this->price_php = $price_php;
    // ++ ADDED: Assign the new property.
    $this->usd_to_php_rate_on_sale = $usd_to_php_rate_on_sale;
    $this->sold_rate_usd = $sold_rate_usd;
    $this->unpend_date = $unpend_date;
    $this->sold_date = $sold_date;
    $this->deleted_at = $deleted_at;

    // ++ ADDED: Automatically calculate profit whenever an Account object is created.
    // This ensures profit is always up-to-date without storing it in the DB.
    $this->profit_php = ($this->price_php ?? 0) - $this->cost_php;

    // ++ ADDED: construct the hero model;
    parent::__construct($created_at, $updated_at);
  }


  /**
   * Returns the model properties as an array.
   * @return array
   */
  public function toArray(): array
  {
    return [
      'id' => $this->id,
      'user_id' => $this->user_id,
      'account_type' => $this->account_type,
      'account_status_id' => $this->account_status_id,
      'name' => $this->name,
      'cookie_enc' => $this->cookie_enc,
      'robux' => $this->robux,
      'cost_php' => $this->cost_php,
      'price_php' => $this->price_php,
      // ++ ADDED: New property in array conversion.
      'usd_to_php_rate_on_sale' => $this->usd_to_php_rate_on_sale,
      // -- REMOVED: 'profit_php' is no longer a direct property from the DB.
      'sold_rate_usd' => $this->sold_rate_usd,
      // ++ ADDED: Include the calculated profit in the array output.
      'profit_php' => $this->profit_php,
      'unpend_date' => $this->unpend_date,
      'sold_date' => $this->sold_date,
      // 'created_at' => $this->created_at,
      // 'updated_at' => $this->updated_at,
      'deleted_at' => $this->deleted_at
    ];
  }
  // public function toArray()
  // {
  //   return get_object_vars($this);
  // }

  public static function fromArray(array $row): AccountModel
  {
    return new AccountModel(
      $row['id'] ?? null,
      $row['user_id'] ?? null,
      isset($row['account_type']) ? AccountType::from($row['account_type']) : null,
      $row['account_status_id'] ?? null,
      $row['name'] ?? null,
      $row['cookie_enc'] ?? null,
      $row['robux'] ?? null,
      $row['cost_php'] ?? null,
      $row['price_php'] ?? null,
      $row['usd_to_php_rate_on_sale'] ?? null,
      $row['sold_rate_usd'] ?? null,
      isset($row['unpend_date']) ? new DateTimeImmutable($row['unpend_date']) : null,
      isset($row['sold_date']) ? new DateTimeImmutable($row['sold_date']) : null,
      isset($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null,
      isset($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null,
      isset($row['deleted_at']) ? new DateTimeImmutable($row['deleted_at']) : null,
    );
  }

  // ---- Getters ----
  public function getId(): ?int
  {
    return $this->id;
  }
  public function getUserId(): ?string
  {
    return $this->user_id;
  }
  public function getAccountType(): ?string
  {
    return $this->account_type;
  }
  public function getAccountStatusId(): ?int
  {
    return $this->account_status_id;
  }
  public function getName(): ?string
  {
    return $this->name;
  }
  public function getCookieEnc(): ?string
  {
    return $this->cookie_enc;
  }
  public function getRobux(): ?float
  {
    return $this->robux;
  }
  public function getCostPhp(): ?float
  {
    return $this->cost_php;
  }
  public function getPricePhp(): ?float
  {
    return $this->price_php;
  }

  public function getUsdToPhpRateOnSale(): ?float
  {
    return $this->usd_to_php_rate_on_sale;
  }

  public function getProfitPhp(): ?float
  {
    return $this->profit_php;
  }
  public function getSoldRateUsd(): ?float
  {
    return $this->sold_rate_usd;
  }
  public function getUnpendDate(): ?DateTimeImmutable
  {
    return $this->unpend_date;
  }
  public function getSoldDate(): ?DateTimeImmutable
  {
    return $this->sold_date;
  }
  public function getDeletedAt(): ?DateTimeImmutable
  {
    return $this->deleted_at;
  }

  // ---- Setters ----
  public function setId(int $id): self
  {
    $this->id = $id;
    return $this;
  }
  public function setUserId(string $user_id): self
  {
    $this->user_id = $user_id;
    return $this;
  }
  public function setAccountType(string $account_type): self
  {
    $this->account_type = $account_type;
    return $this;
  }
  public function setAccountStatusId(int $account_status_id): self
  {
    $this->account_status_id = $account_status_id;
    return $this;
  }
  public function setName(string $name): self
  {
    $this->name = $name;
    return $this;
  }
  public function setCookieEnc(string $cookie_enc): self
  {
    $this->cookie_enc = $cookie_enc;
    return $this;
  }
  public function setRobux(float $robux): self
  {
    $this->robux = $robux;
    return $this;
  }
  public function setCostPhp(float $cost_php): self
  {
    $this->cost_php = $cost_php;
    return $this;
  }
  public function setPricePhp(float $price_php): self
  {
    $this->price_php = $price_php;
    return $this;
  }

  public function setUsdToPhpRateOnSale(float $usd_to_php_rate_on_sale): self
  {
    $this->usd_to_php_rate_on_sale = $usd_to_php_rate_on_sale;
    return $this;
  }

  public function setProfitPhp(float $profit_php): self
  {
    $this->profit_php = $profit_php;
    return $this;
  }
  public function setSoldRateUsd(float $sold_rate_usd): self
  {
    $this->sold_rate_usd = $sold_rate_usd;
    return $this;
  }
  public function setUnpendDate(DateTimeImmutable $unpend_date): self
  {
    $this->unpend_date = $unpend_date;
    return $this;
  }
  public function setSoldDate(DateTimeImmutable $sold_date): self
  {
    $this->sold_date = $sold_date;
    return $this;
  }
  public function setDeletedAt(DateTimeImmutable $deleted_at): self
  {
    $this->deleted_at = $deleted_at;
    return $this;
  }
}