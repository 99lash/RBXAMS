<?php

namespace App\Repositories;

use App\Config\Database;
use App\Models\SummaryModel;
use mysqli;

class SummaryRepository
{
    private mysqli $mysqli;

    public function __construct()
    {
        $this->mysqli = (new Database())->getConnection();
    }

    public function findByDate(string $date): ?SummaryModel
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE summary_date = ?");
        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return null;
        }

        return SummaryModel::fromArray($result);
    }

    public function create(string $date): bool
    {
        $stmt = $this->mysqli->prepare("INSERT INTO daily_summary (summary_date) VALUES (?)");
        if ($stmt === false) {
            error_log('MySQLi prepare failed: ' . $this->mysqli->error);
            return false;
        }

        $stmt->bind_param('s', $date);
        
        if (!$stmt->execute()) {
            error_log('MySQLi execute failed: ' . $stmt->error);
            return false;
        }

        return true;
    }

    public function update(SummaryModel $summary): bool
    {
        $stmt = $this->mysqli->prepare("
            UPDATE daily_summary 
            SET 
                pending_robux_bought = ?, fastflip_robux_bought = ?,
                pending_robux_sold = ?, fastflip_robux_sold = ?,
                pending_expenses_php = ?, fastflip_expenses_php = ?,
                pending_profit_php = ?, fastflip_profit_php = ?
            WHERE summary_date = ?
        ");

        $pendingRobuxBought = $summary->getPendingRobuxBought();
        $fastflipRobuxBought = $summary->getFastflipRobuxBought();
        $pendingRobuxSold = $summary->getPendingRobuxSold();
        $fastflipRobuxSold = $summary->getFastflipRobuxSold();
        $pendingExpensesPhp = $summary->getPendingExpensesPhp();
        $fastflipExpensesPhp = $summary->getFastflipExpensesPhp();
        $pendingProfitPhp = $summary->getPendingProfitPhp();
        $fastflipProfitPhp = $summary->getFastflipProfitPhp();
        $summaryDate = $summary->getSummaryDate()->format('Y-m-d');

        $stmt->bind_param(
            'dddddddds',
            $pendingRobuxBought,
            $fastflipRobuxBought,
            $pendingRobuxSold,
            $fastflipRobuxSold,
            $pendingExpensesPhp,
            $fastflipExpensesPhp,
            $pendingProfitPhp,
            $fastflipProfitPhp,
            $summaryDate
        );

        return $stmt->execute();
    }

    public function findBetweenDates(string $startDate, string $endDate): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE summary_date BETWEEN ? AND ? ORDER BY summary_date DESC");
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }
        return $summaries;
    }

    public function findAll(): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary ORDER BY summary_date DESC");
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }
        return $summaries;
    }

    public function findAndCountBetweenDates(string $startDate, string $endDate, int $limit, int $offset): array
    {
        // Get total count for the period
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as total FROM daily_summary WHERE summary_date BETWEEN ? AND ?");
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        // Get paginated data
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE summary_date BETWEEN ? AND ? ORDER BY summary_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('ssii', $startDate, $endDate, $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }

        return [
            'data' => $summaries,
            'total' => $total
        ];
    }

    public function findAndCountAll(int $limit, int $offset): array
    {
        // Get total count
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as total FROM daily_summary");
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        // Get paginated data
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary ORDER BY summary_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('ii', $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }

        return [
            'data' => $summaries,
            'total' => $total
        ];
    }
}