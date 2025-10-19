<?php

namespace App\Models;

use DateTime;
use JsonSerializable;

class SummaryModel implements JsonSerializable
{
    private DateTime $summary_date;
    private float $pending_robux_bought;
    private float $fastflip_robux_bought;
    private float $pending_robux_sold;
    private float $fastflip_robux_sold;
    private float $pending_expenses_php;
    private float $fastflip_expenses_php;
    private float $pending_profit_php;
    private float $fastflip_profit_php;

    public function __construct(
        string $summary_date,
        float $pending_robux_bought = 0.0,
        float $fastflip_robux_bought = 0.0,
        float $pending_robux_sold = 0.0,
        float $fastflip_robux_sold = 0.0,
        float $pending_expenses_php = 0.0,
        float $fastflip_expenses_php = 0.0,
        float $pending_profit_php = 0.0,
        float $fastflip_profit_php = 0.0
    ) {
        $this->summary_date = new DateTime($summary_date);
        $this->pending_robux_bought = $pending_robux_bought;
        $this->fastflip_robux_bought = $fastflip_robux_bought;
        $this->pending_robux_sold = $pending_robux_sold;
        $this->fastflip_robux_sold = $fastflip_robux_sold;
        $this->pending_expenses_php = $pending_expenses_php;
        $this->fastflip_expenses_php = $fastflip_expenses_php;
        $this->pending_profit_php = $pending_profit_php;
        $this->fastflip_profit_php = $fastflip_profit_php;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['summary_date'],
            (float) ($data['pending_robux_bought'] ?? 0.0),
            (float) ($data['fastflip_robux_bought'] ?? 0.0),
            (float) ($data['pending_robux_sold'] ?? 0.0),
            (float) ($data['fastflip_robux_sold'] ?? 0.0),
            (float) ($data['pending_expenses_php'] ?? 0.0),
            (float) ($data['fastflip_expenses_php'] ?? 0.0),
            (float) ($data['pending_profit_php'] ?? 0.0),
            (float) ($data['fastflip_profit_php'] ?? 0.0)
        );
    }

    // Getters
    public function getSummaryDate(): DateTime
    {
        return $this->summary_date;
    }

    public function getPendingRobuxBought(): float
    {
        return $this->pending_robux_bought;
    }

    public function getFastflipRobuxBought(): float
    {
        return $this->fastflip_robux_bought;
    }

    public function getPendingRobuxSold(): float
    {
        return $this->pending_robux_sold;
    }

    public function getFastflipRobuxSold(): float
    {
        return $this->fastflip_robux_sold;
    }

    public function getPendingExpensesPhp(): float
    {
        return $this->pending_expenses_php;
    }

    public function getFastflipExpensesPhp(): float
    {
        return $this->fastflip_expenses_php;
    }

    public function getPendingProfitPhp(): float
    {
        return $this->pending_profit_php;
    }

    public function getFastflipProfitPhp(): float
    {
        return $this->fastflip_profit_php;
    }

    // Setters
    public function setSummaryDate(DateTime $summary_date): self
    {
        $this->summary_date = $summary_date;
        return $this;
    }

    public function setPendingRobuxBought(float $pending_robux_bought): self
    {
        $this->pending_robux_bought = $pending_robux_bought;
        return $this;
    }

    public function setFastflipRobuxBought(float $fastflip_robux_bought): self
    {
        $this->fastflip_robux_bought = $fastflip_robux_bought;
        return $this;
    }

    public function setPendingRobuxSold(float $pending_robux_sold): self
    {
        $this->pending_robux_sold = $pending_robux_sold;
        return $this;
    }

    public function setFastflipRobuxSold(float $fastflip_robux_sold): self
    {
        $this->fastflip_robux_sold = $fastflip_robux_sold;
        return $this;
    }

    public function setPendingExpensesPhp(float $pending_expenses_php): self
    {
        $this->pending_expenses_php = $pending_expenses_php;
        return $this;
    }

    public function setFastflipExpensesPhp(float $fastflip_expenses_php): self
    {
        $this->fastflip_expenses_php = $fastflip_expenses_php;
        return $this;
    }

    public function setPendingProfitPhp(float $pending_profit_php): self
    {
        $this->pending_profit_php = $pending_profit_php;
        return $this;
    }

    public function setFastflipProfitPhp(float $fastflip_profit_php): self
    {
        $this->fastflip_profit_php = $fastflip_profit_php;
        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'summary_date' => $this->getSummaryDate()->format('Y-m-d'),
            'pending_robux_bought' => $this->getPendingRobuxBought(),
            'fastflip_robux_bought' => $this->getFastflipRobuxBought(),
            'pending_robux_sold' => $this->getPendingRobuxSold(),
            'fastflip_robux_sold' => $this->getFastflipRobuxSold(),
            'pending_expenses_php' => $this->getPendingExpensesPhp(),
            'fastflip_expenses_php' => $this->getFastflipExpensesPhp(),
            'pending_profit_php' => $this->getPendingProfitPhp(),
            'fastflip_profit_php' => $this->getFastflipProfitPhp(),
        ];
    }
}