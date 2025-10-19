<?php

namespace App\Services;

use App\Models\AccountModel;
use App\Repositories\SummaryRepository;
use App\Utils\AccountType;
use DateTime;

class SummaryService
{
    private SummaryRepository $summaryRepo;

    public function __construct(SummaryRepository $summaryRepo = null)
    {
        $this->summaryRepo = $summaryRepo ?? new SummaryRepository();
    }

    public function getSummaryForDate(string $date)
    {
        $this->ensureSummaryExists($date);
        return $this->summaryRepo->findByDate($date);
    }

    private function ensureSummaryExists(string $date): void
    {
        if (!$this->summaryRepo->findByDate($date)) {
            $this->summaryRepo->create($date);
        }
    }

    public function updateSummaryOnBuy(AccountModel $account): void
    {
        $today = (new DateTime())->format('Y-m-d');
        $this->ensureSummaryExists($today);

        $summary = $this->summaryRepo->findByDate($today);

        if ($account->getAccountType() === AccountType::PENDING) {
            $summary->setPendingRobuxBought($summary->getPendingRobuxBought() + $account->getRobux());
            $summary->setPendingExpensesPhp($summary->getPendingExpensesPhp() + $account->getCostPhp());
        } else { // Assuming FASTFLIP
            $summary->setFastflipRobuxBought($summary->getFastflipRobuxBought() + $account->getRobux());
            $summary->setFastflipExpensesPhp($summary->getFastflipExpensesPhp() + $account->getCostPhp());
        }

        $this->summaryRepo->update($summary);
    }

    public function updateSummaryOnSell(AccountModel $account): void
    {
        $today = (new DateTime())->format('Y-m-d');
        $this->ensureSummaryExists($today);

        $summary = $this->summaryRepo->findByDate($today);
        $profit = ($account->getPricePhp() ?? 0) - ($account->getCostPhp() ?? 0);

        if ($account->getAccountType() === AccountType::PENDING) {
            $summary->setPendingRobuxSold($summary->getPendingRobuxSold() + $account->getRobux());
            $summary->setPendingProfitPhp($summary->getPendingProfitPhp() + $profit);
        } else { // Assuming FASTFLIP
            $summary->setFastflipRobuxSold($summary->getFastflipRobuxSold() + $account->getRobux());
            $summary->setFastflipProfitPhp($summary->getFastflipProfitPhp() + $profit);
        }

        $this->summaryRepo->update($summary);
    }

    public function getSummaries(string $period = 'today')
    {
        $today = new DateTime();
        switch ($period) {
            case 'week':
                $startDate = $today->modify('monday this week')->format('Y-m-d');
                $endDate = $today->modify('sunday this week')->format('Y-m-d');
                return $this->summaryRepo->findBetweenDates($startDate, $endDate);
            case 'month':
                $startDate = $today->modify('first day of this month')->format('Y-m-d');
                $endDate = $today->modify('last day of this month')->format('Y-m-d');
                return $this->summaryRepo->findBetweenDates($startDate, $endDate);
            case 'quarter':
                $month = $today->format('n');
                $year = $today->format('Y');
                if ($month <= 3) {
                    $startDate = "$year-01-01";
                    $endDate = "$year-03-31";
                } elseif ($month <= 6) {
                    $startDate = "$year-04-01";
                    $endDate = "$year-06-30";
                } elseif ($month <= 9) {
                    $startDate = "$year-07-01";
                    $endDate = "$year-09-30";
                } else {
                    $startDate = "$year-10-01";
                    $endDate = "$year-12-31";
                }
                return $this->summaryRepo->findBetweenDates($startDate, $endDate);
            case 'year':
                $year = $today->format('Y');
                $startDate = "$year-01-01";
                $endDate = "$year-12-31";
                return $this->summaryRepo->findBetweenDates($startDate, $endDate);
            case 'all':
                return $this->summaryRepo->findAll();
            case 'today':
            default:
                return $this->getSummaryForDate($today->format('Y-m-d'));
        }
    }
}
