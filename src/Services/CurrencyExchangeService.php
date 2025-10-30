<?php

namespace App\Services;

/**
 * CurrencyExchangeService
 * 
 * Provides USD to PHP exchange rate functionality.
 * Uses exchangerate-api.com as the primary source.
 * Implements caching to avoid hitting API rate limits.
 */
class CurrencyExchangeService
{
  private const API_URL = 'https://api.exchangerate-api.com/v4/latest/USD';
  private const CACHE_FILE = __DIR__ . '/../../storage/cache/exchange_rate.json';
  private const CACHE_DURATION = 3600; // 1 hour in seconds
  private const FALLBACK_RATE = 56.50; // Fallback rate if API fails

  /**
   * Get the current USD to PHP exchange rate
   * 
   * @return float The exchange rate
   */
  public function getUsdToPhpRate(): float
  {
    // Try to get from cache first
    $cachedRate = $this->getCachedRate();
    if ($cachedRate !== null) {
      return $cachedRate;
    }

    // Fetch from API
    $rate = $this->fetchRateFromApi();
    if ($rate !== null) {
      $this->cacheRate($rate);
      return $rate;
    }

    // Return fallback rate if API fails
    return self::FALLBACK_RATE;
  }

  /**
   * Get cached exchange rate if valid
   * 
   * @return float|null The cached rate or null if cache is invalid/expired
   */
  private function getCachedRate(): ?float
  {
    if (!file_exists(self::CACHE_FILE)) {
      return null;
    }

    $cacheData = json_decode(file_get_contents(self::CACHE_FILE), true);
    if (!$cacheData || !isset($cacheData['rate']) || !isset($cacheData['timestamp'])) {
      return null;
    }

    // Check if cache is still valid
    if (time() - $cacheData['timestamp'] > self::CACHE_DURATION) {
      return null;
    }

    return (float) $cacheData['rate'];
  }

  /**
   * Fetch exchange rate from API
   * 
   * @return float|null The exchange rate or null if fetch fails
   */
  private function fetchRateFromApi(): ?float
  {
    try {
      $context = stream_context_create([
        'http' => [
          'timeout' => 5, // 5 seconds timeout
          'ignore_errors' => true
        ]
      ]);

      $response = @file_get_contents(self::API_URL, false, $context);
      if ($response === false) {
        return null;
      }

      $data = json_decode($response, true);
      if (!$data || !isset($data['rates']['PHP'])) {
        return null;
      }

      return (float) $data['rates']['PHP'];
    } catch (\Exception $e) {
      error_log("CurrencyExchangeService: Failed to fetch rate - " . $e->getMessage());
      return null;
    }
  }

  /**
   * Cache the exchange rate
   * 
   * @param float $rate The rate to cache
   * @return void
   */
  private function cacheRate(float $rate): void
  {
    try {
      $cacheDir = dirname(self::CACHE_FILE);
      if (!is_dir($cacheDir)) {
        mkdir($cacheDir, 0755, true);
      }

      $cacheData = [
        'rate' => $rate,
        'timestamp' => time()
      ];

      file_put_contents(self::CACHE_FILE, json_encode($cacheData));
    } catch (\Exception $e) {
      error_log("CurrencyExchangeService: Failed to cache rate - " . $e->getMessage());
    }
  }

  /**
   * Clear the cache (useful for testing or manual refresh)
   * 
   * @return bool True if cache was cleared, false otherwise
   */
  public function clearCache(): bool
  {
    if (file_exists(self::CACHE_FILE)) {
      return unlink(self::CACHE_FILE);
    }
    return true;
  }
}
