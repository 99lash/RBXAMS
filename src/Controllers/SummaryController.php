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
    $period = $_GET['period'] ?? 'today';
    $summaries = $this->summaryService->getSummaries($period);
    $page = '/summary';
    $title = 'Daily Summary | RBXAMS';
    $nav = 'Daily Summary';
    require __DIR__ . '/../Views/index.php';
    // header('Content-Type: application/json');
    // if (is_array($summaries)) {
    //   $response = array_map(fn ($summary) => $summary->jsonSerialize(), $summaries);
    //   echo json_encode($response);
    // } else if ($summaries) {
    //   echo json_encode($summaries->jsonSerialize());
    // } else {
    //   echo json_encode([]);
    // }
  }
}
