<?php

namespace App\Utils;

use Exception;
use Hidehalo\Nanoid\Client;

interface IdGeneratorInterface
{
  public function generate();
}

class IdType
{
  public const USER = 'userId';
  public const ACCOUNT = 'accountId';
  public const SUMMARY = 'summaryId';
  public const TRANSACTION = 'transactionId';
}

class IdGeneratorFactory
{
  /**
   * @param string $type The type of ID you want to generate. 
   * @throws \Exception If the $type is invalid.
   */
  public static function createId(string $type)
  {
    $client = new Client();
    switch ($type) {
      case 'userId':
        return new UserId($client);

      case 'accountId':
        return new AccountId($client);

      case 'summaryId':
        return new SummaryId($client);

      case 'transactionId':
        return new TransactionId($client);

      default:
        throw new Exception('Invalid ID generator type.');
    }
  }
}