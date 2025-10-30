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

    public function getSummaryCalculationData(string $userId, string $date): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT 
                a.account_type, 
                t.transaction_type, 
                a.robux, 
                t.amount
            FROM transactions AS t
            JOIN accounts AS a ON t.account_id = a.id
            WHERE t.user_id = ? 
              AND DATE(t.created_at) = ? 
              AND t.txn_status = 'active'
        ");
        $stmt->bind_param('ss', $userId, $date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function findByDate(string $userId, string $date): ?SummaryModel
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE user_id = ? AND summary_date = ?");
        $stmt->bind_param('ss', $userId, $date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) {
            return null;
        }

        return SummaryModel::fromArray($result);
    }

    public function create(string $userId, string $date): bool
    {
        $stmt = $this->mysqli->prepare("INSERT INTO daily_summary (user_id, summary_date) VALUES (?, ?)");
        if ($stmt === false) {
            error_log('MySQLi prepare failed: ' . $this->mysqli->error);
            return false;
        }

        $stmt->bind_param('ss', $userId, $date);
        
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
            WHERE user_id = ? AND summary_date = ?
        ");

        $pendingRobuxBought = $summary->getPendingRobuxBought();
        $fastflipRobuxBought = $summary->getFastflipRobuxBought();
        $pendingRobuxSold = $summary->getPendingRobuxSold();
        $fastflipRobuxSold = $summary->getFastflipRobuxSold();
        $pendingExpensesPhp = $summary->getPendingExpensesPhp();
        $fastflipExpensesPhp = $summary->getFastflipExpensesPhp();
        $pendingProfitPhp = $summary->getPendingProfitPhp();
        $fastflipProfitPhp = $summary->getFastflipProfitPhp();
        $userId = $summary->getUserId();
        $summaryDate = $summary->getSummaryDate()->format('Y-m-d');

        $stmt->bind_param(
            'ddddddddss',
            $pendingRobuxBought,
            $fastflipRobuxBought,
            $pendingRobuxSold,
            $fastflipRobuxSold,
            $pendingExpensesPhp,
            $fastflipExpensesPhp,
            $pendingProfitPhp,
            $fastflipProfitPhp,
            $userId,
            $summaryDate
        );

        return $stmt->execute();
    }

    public function findBetweenDates(string $userId, string $startDate, string $endDate): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE user_id = ? AND summary_date BETWEEN ? AND ? ORDER BY summary_date DESC");
        $stmt->bind_param('sss', $userId, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }
        return $summaries;
    }

    public function findAll(string $userId): array
    {
        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE user_id = ? ORDER BY summary_date DESC");
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $summaries = [];
        while ($row = $result->fetch_assoc()) {
            $summaries[] = SummaryModel::fromArray($row);
        }
        return $summaries;
    }

    public function findAndCountBetweenDates(string $userId, string $startDate, string $endDate, int $limit, int $offset): array
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as total FROM daily_summary WHERE user_id = ? AND summary_date BETWEEN ? AND ?");
        $stmt->bind_param('sss', $userId, $startDate, $endDate);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE user_id = ? AND summary_date BETWEEN ? AND ? ORDER BY summary_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('sssii', $userId, $startDate, $endDate, $limit, $offset);
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

    public function findAndCountAll(string $userId, int $limit, int $offset): array
    {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) as total FROM daily_summary WHERE user_id = ?");
        $stmt->bind_param('s', $userId);
        $stmt->execute();
        $total = $stmt->get_result()->fetch_assoc()['total'];

        $stmt = $this->mysqli->prepare("SELECT * FROM daily_summary WHERE user_id = ? ORDER BY summary_date DESC LIMIT ? OFFSET ?");
        $stmt->bind_param('sii', $userId, $limit, $offset);
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

    public function getAggregatedSummary(string $userId, string $startDate, string $endDate): array
    {
        $stmt = $this->mysqli->prepare("
            SELECT 
                SUM(pending_robux_bought) as total_pending_robux_bought,
                SUM(fastflip_robux_bought) as total_fastflip_robux_bought,
                SUM(pending_robux_sold) as total_pending_robux_sold,
                SUM(fastflip_robux_sold) as total_fastflip_robux_sold,
                SUM(pending_expenses_php) as total_pending_expenses_php,
                SUM(fastflip_expenses_php) as total_fastflip_expenses_php,
                SUM(pending_profit_php) as total_pending_profit_php,
                SUM(fastflip_profit_php) as total_fastflip_profit_php
            FROM daily_summary 
            WHERE user_id = ? AND summary_date BETWEEN ? AND ?
        ");
        $stmt->bind_param('sss', $userId, $startDate, $endDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result ?? [];
    }

    public function deleteByDate(string $userId, string $date): bool
    {
        $stmt = $this->mysqli->prepare("DELETE FROM daily_summary WHERE user_id = ? AND summary_date = ?");
        $stmt->bind_param('ss', $userId, $date);
        return $stmt->execute();
    }
}