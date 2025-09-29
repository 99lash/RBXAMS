<?php

namespace App\Services\RobloxAPI;

class RobloxService
{
  public static function getAccountTransaction($id, $cookie)
  {
    $ch = curl_init("https://economy.roblox.com/v2/users/{$id}/transaction-totals?timeFrame=Month&transactionType=summary");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Cookie: .ROBLOSECURITY=$cookie"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }

  public static function getAccountDetails($cookie)
  {

    $ch = curl_init('https://users.roblox.com/v1/users/authenticated');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Cookie: .ROBLOSECURITY=$cookie"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }

  public static function getAccountRobux($cookie)
  {
    $ch = curl_init('https://economy.roblox.com/v1/user/currency');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Cookie: .ROBLOSECURITY=$cookie"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
  }
}