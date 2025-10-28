<?php

namespace App\Services;

use App\Models\AccountModel;
use App\Repositories\SummaryRepository;
use App\Utils\AccountType;
use DateTime;
use Dompdf\Dompdf;
use Dompdf\Options;

class SummaryService
{
    private SummaryRepository $summaryRepo;

    public function __construct(SummaryRepository $summaryRepo = null)
    {
        $this->summaryRepo = $summaryRepo ?? new SummaryRepository();
    }

    public function getSummaryForDate(string $userId, string $date)
    {
        $this->ensureSummaryExists($userId, $date);
        return $this->summaryRepo->findByDate($userId, $date);
    }

    private function ensureSummaryExists(string $userId, string $date): void
    {
        if (!$this->summaryRepo->findByDate($userId, $date)) {
            $this->summaryRepo->create($userId, $date);
        }
    }

    public function updateSummaryOnBuy(string $userId, AccountModel $account): void
    {
        $today = (new DateTime())->format('Y-m-d');
        $this->ensureSummaryExists($userId, $today);

        $summary = $this->summaryRepo->findByDate($userId, $today);

        if ($account->getAccountType() === AccountType::PENDING) {
            $summary->setPendingRobuxBought($summary->getPendingRobuxBought() + $account->getRobux());
            $summary->setPendingExpensesPhp($summary->getPendingExpensesPhp() + $account->getCostPhp());
        } else { // Assuming FASTFLIP
            $summary->setFastflipRobuxBought($summary->getFastflipRobuxBought() + $account->getRobux());
            $summary->setFastflipExpensesPhp($summary->getFastflipExpensesPhp() + $account->getCostPhp());
        }

        $this->summaryRepo->update($summary);
    }

    public function updateSummaryOnSell(string $userId, AccountModel $account): void
    {
        $today = (new DateTime())->format('Y-m-d');
        $this->ensureSummaryExists($userId, $today);

        $summary = $this->summaryRepo->findByDate($userId, $today);
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

    public function getSummaries(string $userId, string $period = 'today', int $page = 1, int $limit = 10)
    {
        $offset = ($page - 1) * $limit;
        $today = new DateTime();
        $this->ensureSummaryExists($userId, $today->format('Y-m-d'));

        $result = [];
        $total = 0;

        switch ($period) {
            case 'week':
                $startDate = $today->modify('monday this week')->format('Y-m-d');
                $endDate = $today->modify('sunday this week')->format('Y-m-d');
                $result = $this->summaryRepo->findAndCountBetweenDates($userId, $startDate, $endDate, $limit, $offset);
                break;
            case 'month':
                $startDate = $today->modify('first day of this month')->format('Y-m-d');
                $endDate = $today->modify('last day of this month')->format('Y-m-d');
                $result = $this->summaryRepo->findAndCountBetweenDates($userId, $startDate, $endDate, $limit, $offset);
                break;
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
                $result = $this->summaryRepo->findAndCountBetweenDates($userId, $startDate, $endDate, $limit, $offset);
                break;
            case 'year':
                $year = $today->format('Y');
                $startDate = "$year-01-01";
                $endDate = "$year-12-31";
                $result = $this->summaryRepo->findAndCountBetweenDates($userId, $startDate, $endDate, $limit, $offset);
                break;
            case 'all':
                $result = $this->summaryRepo->findAndCountAll($userId, $limit, $offset);
                break;
            case 'today':
            default:
                $date = $today->format('Y-m-d');
                $result = $this->summaryRepo->findAndCountBetweenDates($userId, $date, $date, $limit, $offset);
                break;
        }

        $summaries = $result['data'] ?? [];
        $total = $result['total'] ?? 0;
        $lastPage = ceil($total / $limit);

        return [
            'data' => array_map(fn ($s) => $s->jsonSerialize(), $summaries),
            'pagination' => [
                'total_items' => $total,
                'per_page' => $limit,
                'current_page' => $page,
                'last_page' => $lastPage > 0 ? $lastPage : 1,
                'from' => $offset + 1,
                'to' => $offset + count($summaries)
            ]
        ];
    }

    public function generatePdfForPeriod(string $userId, string $period): string
    {
        $summariesResult = $this->getSummaries($userId, $period);
        $summariesArray = $summariesResult['data'] ?? [];

        // Corrected path to the stylesheet
        // $stylesheetPath = __DIR__ . '/../../public_html/css/styles.css';
        // $stylesheet = file_get_contents($stylesheetPath);

        // $html = '<!DOCTYPE html><html><head><style>' . $stylesheet . '</style></head><body>';
        $html = '<h1>Daily Activity Summary (' . htmlspecialchars($period) . ')</h1>';
        $html .= '<p>Report generated on: ' . date('Y-m-d H:i:s') . '</p>';
        
        // --- Totals and Statistics ---
        $totals = [
            'total_bought' => 0, 'total_sold' => 0, 'total_expenses' => 0, 'total_profit' => 0
        ];
        foreach ($summariesArray as $s) {
            $totals['total_bought'] += $s['pending_robux_bought'] + $s['fastflip_robux_bought'];
            $totals['total_sold'] += $s['pending_robux_sold'] + $s['fastflip_robux_sold'];
            $totals['total_expenses'] += $s['pending_expenses_php'] + $s['fastflip_expenses_php'];
            $totals['total_profit'] += $s['pending_profit_php'] + $s['fastflip_profit_php'];
        }
        $html .= '<h2>Overall Statistics</h2>';
        $html .= '<table class="table"><thead><tr><th>Metric</th><th>Value</th></tr></thead><tbody>';
        $html .= '<tr><td>Total Robux Bought</td><td>' . number_format($totals['total_bought']) . '</td></tr>';
        $html .= '<tr><td>Total Robux Sold</td><td>' . number_format($totals['total_sold']) . '</td></tr>';
        $html .= '<tr><td>Total Expenses</td><td>PHP ' . number_format($totals['total_expenses'], 2) . '</td></tr>';
        $html .= '<tr><td>Total Profit</td><td>PHP ' . number_format($totals['total_profit'], 2) . '</td></tr>';
        $html .= '</tbody></table>';

        $html .= '<h2>Daily Details</h2>';
        $html .= '<table class="table table-zebra"><thead><tr>';
        $html .= '<th>Date</th><th>Total Bought</th><th>Total Sold</th><th>Total Expenses</th><th>Total Profit</th>';
        $html .= '</tr></thead><tbody>';

        if (empty($summariesArray)) {
            $html .= '<tr><td colspan="5">No data available for this period.</td></tr>';
        } else {
            foreach ($summariesArray as $s) {
                $totalDailyBought = $s['pending_robux_bought'] + $s['fastflip_robux_bought'];
                $totalDailySold = $s['pending_robux_sold'] + $s['fastflip_robux_sold'];
                $totalDailyExpenses = $s['pending_expenses_php'] + $s['fastflip_expenses_php'];
                $totalDailyProfit = $s['pending_profit_php'] + $s['fastflip_profit_php'];
                $html .= '<tr>';
                $html .= '<td>' . $s['summary_date'] . '</td>';
                $html .= '<td>' . number_format($totalDailyBought) . '</td>';
                $html .= '<td>' . number_format($totalDailySold) . '</td>';
                $html .= '<td>' . number_format($totalDailyExpenses, 2) . '</td>';
                $html .= '<td>' . number_format($totalDailyProfit, 2) . '</td>';
                $html .= '</tr>';
            }
        }

        $html .= '</tbody></table></body></html>';

        $options = new Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'sans-serif');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        // Return the PDF content as a string
        return $dompdf->output();
    }

    public function getDashboardData(string $userId, string $period = 'today')
    {
        $today = new DateTime();
        $startDate = $today->format('Y-m-d');
        $endDate = $today->format('Y-m-d');

        switch ($period) {
            case 'week':
                $startDate = $today->modify('monday this week')->format('Y-m-d');
                $endDate = $today->modify('sunday this week')->format('Y-m-d');
                break;
            case 'month':
                $startDate = $today->modify('first day of this month')->format('Y-m-d');
                $endDate = $today->modify('last day of this month')->format('Y-m-d');
                break;
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
                break;
            case 'year':
                $year = $today->format('Y');
                $startDate = "$year-01-01";
                $endDate = "$year-12-31";
                break;
            case 'all':
                $startDate = '1970-01-01';
                $endDate = $today->format('Y-m-d');
                break;
        }

        $summary = $this->summaryRepo->getAggregatedSummary($userId, $startDate, $endDate);

        $accountRepo = new \App\Repositories\AccountRepository();
        $accountTypeDistribution = $accountRepo->getAccountTypeDistribution($userId);
        $accountStatusDistribution = $accountRepo->getAccountStatusDistribution($userId);

        return [
            'summary' => $summary,
            'accountTypeDistribution' => $accountTypeDistribution,
            'accountStatusDistribution' => $accountStatusDistribution
        ];
    }
}
