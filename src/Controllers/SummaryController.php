<?php

namespace App\Controllers;

use App\Security\AuthManager;
use App\Services\SummaryService;

class SummaryController
{
  private AuthManager $authManager;
  private SummaryService $summaryService;
  private array $currentUser;

  public function __construct()
  {
    $this->authManager = new AuthManager();
    $this->summaryService = new SummaryService();
    $this->currentUser = $this->authManager->requireAuth();
  }

  public function index()
  {
    $page = '/summary';
    $title = 'Daily Summary | RBXAMS';
    $nav = 'Daily Summary';
    require __DIR__ . '/../Views/index.php';
  }

  public function getSummaryData()
  {
    $period = $_GET['period'] ?? 'all';
    $summaries = $this->summaryService->getSummaries($period);

    header('Content-Type: application/json');
    if (is_array($summaries)) {
      $response = array_map(fn ($summary) => $summary->jsonSerialize(), $summaries);
      echo json_encode($response);
    } else if ($summaries) {
      echo json_encode($summaries->jsonSerialize());
    } else {
      echo json_encode([]);
    }
  }

  public function exportCsv()
  {
    $period = $_GET['period'] ?? 'all';
    $summaries = $this->summaryService->getSummaries($period);
    $summariesArray = is_array($summaries) ? array_map(fn ($s) => $s->jsonSerialize(), $summaries) : ($summaries ? [$summaries->jsonSerialize()] : []);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="RBXAMS_Summary_' . $period . '_' . date('Y-m-d') . '.csv"');

    $output = fopen('php://output', 'w');

    // Add header row
    fputcsv($output, [
      'Date',
      'Pending Robux Bought', 'Fastflip Robux Bought', 'Total Robux Bought',
      'Pending Robux Sold', 'Fastflip Robux Sold', 'Total Robux Sold',
      'Pending Expenses (PHP)', 'Fastflip Expenses (PHP)', 'Total Expenses (PHP)',
      'Pending Profit (PHP)', 'Fastflip Profit (PHP)', 'Total Profit (PHP)'
    ]);

    // Add data rows
    foreach ($summariesArray as $s) {
      fputcsv($output, [
        $s['summary_date'],
        $s['pending_robux_bought'],
        $s['fastflip_robux_bought'],
        $s['pending_robux_bought'] + $s['fastflip_robux_bought'],
        $s['pending_robux_sold'],
        $s['fastflip_robux_sold'],
        $s['pending_robux_sold'] + $s['fastflip_robux_sold'],
        $s['pending_expenses_php'],
        $s['fastflip_expenses_php'],
        $s['pending_expenses_php'] + $s['fastflip_expenses_php'],
        $s['pending_profit_php'],
        $s['fastflip_profit_php'],
        $s['pending_profit_php'] + $s['fastflip_profit_php'],
      ]);
    }

    fclose($output);
    exit;
  }

  public function exportPdf()
  {
    echo "PDF export is not yet implemented. Please install a PDF library like dompdf/dompdf first.";
  }
}
