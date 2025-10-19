<?php
// Ensure $summaries exists and is not empty.
if (isset($summaries) && !empty($summaries)) {
  // The service might return a single object or an array of objects.
  $summaryObjects = is_array($summaries) ? $summaries : [$summaries];
  // Convert objects to arrays for easier access in the view, as properties are private.
  $summariesArray = array_map(fn($s) => $s->jsonSerialize(), $summaryObjects);

  // 1. Calculate Totals for Summary Cards
  $totals = [
    'pending_robux_bought' => 0,
    'fastflip_robux_bought' => 0,
    'pending_robux_sold' => 0,
    'fastflip_robux_sold' => 0,
    'pending_expenses_php' => 0,
    'fastflip_expenses_php' => 0,
    'pending_profit_php' => 0,
    'fastflip_profit_php' => 0,
  ];

  foreach ($summariesArray as $summary) {
    $totals['pending_robux_bought'] += $summary['pending_robux_bought'];
    $totals['fastflip_robux_bought'] += $summary['fastflip_robux_bought'];
    $totals['pending_robux_sold'] += $summary['pending_robux_sold'];
    $totals['fastflip_robux_sold'] += $summary['fastflip_robux_sold'];
    $totals['pending_expenses_php'] += $summary['pending_expenses_php'];
    $totals['fastflip_expenses_php'] += $summary['fastflip_expenses_php'];
    $totals['pending_profit_php'] += $summary['pending_profit_php'];
    $totals['fastflip_profit_php'] += $summary['fastflip_profit_php'];
  }

  $totalBought = $totals['pending_robux_bought'] + $totals['fastflip_robux_bought'];
  $totalSold = $totals['pending_robux_sold'] + $totals['fastflip_robux_sold'];
  $totalExpenses = $totals['pending_expenses_php'] + $totals['fastflip_expenses_php'];
  $totalProfit = $totals['pending_profit_php'] + $totals['fastflip_profit_php'];

  // 2. Calculate Summary Statistics
  $daysActive = count($summariesArray);
  $dailyProfits = !empty($summariesArray) ? array_map(fn($s) => $s['pending_profit_php'] + $s['fastflip_profit_php'], $summariesArray) : [0];
  $avgDailyProfit = $daysActive > 0 ? $totalProfit / $daysActive : 0;
  $bestDayProfit = $daysActive > 0 ? max($dailyProfits) : 0;
  $worstDayProfit = $daysActive > 0 ? min($dailyProfits) : 0;

} else {
  // Set default empty values if no summaries
  $summariesArray = [];
  $totalBought = 0;
  $totalSold = 0;
  $totalExpenses = 0;
  $totalProfit = 0;
  $totals = array_fill_keys(['pending_robux_bought', 'fastflip_robux_bought', 'pending_robux_sold', 'fastflip_robux_sold', 'pending_expenses_php', 'fastflip_expenses_php', 'pending_profit_php', 'fastflip_profit_php'], 0);
  $daysActive = 0;
  $avgDailyProfit = 0;
  $bestDayProfit = 0;
  $worstDayProfit = 0;
}

// Helper functions for formatting
function formatCurrency($value, $currency = '₱')
{
  return $currency . number_format($value, 2);
}

function formatNumber($value)
{
  return number_format($value);
}

function getProfitColorClass($value)
{
  if ($value > 0)
    return 'text-success';
  if ($value < 0)
    return 'text-error';
  return '';
}

$currentPeriod = $_GET['period'] ?? 'all';
$periodLabels = [
  'all' => 'All Time',
  'year' => 'This Year',
  'quarter' => 'This Quarter',
  'month' => 'This Month',
  'week' => 'This Week',
  'today' => 'Today',
];
$currentPeriodLabel = $periodLabels[$currentPeriod] ?? 'All Time';

?>
<div class="p-4 sm:p-6 md:p-8">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold">Daily Activity Summary</h1>
      <p class="text-base-content/70">Track your daily trading performance and export reports</p>
    </div>
    <div class="flex items-center gap-2">
      <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-outline w-40 justify-between">
          <span id="period-label"><?php echo htmlspecialchars($currentPeriodLabel); ?></span>
          <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </label>
        <ul tabindex="0" id="period-options" class="dropdown-content menu p-2 shadow bg-base-200 rounded-box w-40">
          <li><a href="?period=all">All Time</a></li>
          <li><a href="?period=year">This Year</a></li>
          <li><a href="?period=quarter">This Quarter</a></li>
          <li><a href="?period=month">This Month</a></li>
          <li><a href="?period=week">This Week</a></li>
          <li><a href="?period=today">Today</a></li>
        </ul>
      </div>
      <button class="btn btn-outline">
        <i data-lucide="download" class="w-4 h-4"></i>
        CSV
      </button>
      <button class="btn btn-outline">
        <i data-lucide="file-text" class="w-4 h-4"></i>
        PDF
      </button>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Robux Bought</h2>
        <p class="text-3xl font-bold"><?php echo formatNumber($totalBought); ?></p>
        <p class="text-sm text-base-content/50">P: <?php echo formatNumber($totals['pending_robux_bought']); ?> | F:
          <?php echo formatNumber($totals['fastflip_robux_bought']); ?>
        </p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Robux Sold</h2>
        <p class="text-3xl font-bold"><?php echo formatNumber($totalSold); ?></p>
        <p class="text-sm text-base-content/50">P: <?php echo formatNumber($totals['pending_robux_sold']); ?> | F:
          <?php echo formatNumber($totals['fastflip_robux_sold']); ?>
        </p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Expenses</h2>
        <p class="text-3xl font-bold"><?php echo formatCurrency($totalExpenses); ?></p>
        <p class="text-sm text-base-content/50">P: <?php echo formatCurrency($totals['pending_expenses_php']); ?> | F:
          <?php echo formatCurrency($totals['fastflip_expenses_php']); ?>
        </p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Profit</h2>
        <p class="text-3xl font-bold <?php echo getProfitColorClass($totalProfit); ?>">
          <?php echo formatCurrency($totalProfit); ?>
        </p>
        <p class="text-sm text-base-content/50">P: <?php echo formatCurrency($totals['pending_profit_php']); ?> | F:
          <?php echo formatCurrency($totals['fastflip_profit_php']); ?>
        </p>
      </div>
    </div>
  </div>

  <!-- Summary Statistics -->
  <div class="card bg-base-200 mb-8">
    <div class="card-body">
      <h2 class="card-title mb-4">Summary Statistics</h2>
      <div class="stats stats-vertical lg:stats-horizontal shadow bg-base-100">
        <div class="stat">
          <div class="stat-title">Avg Daily Profit</div>
          <div class="stat-value <?php echo getProfitColorClass($avgDailyProfit); ?>">
            <?php echo formatCurrency($avgDailyProfit); ?>
          </div>
        </div>
        <div class="stat">
          <div class="stat-title">Best Day</div>
          <div class="stat-value <?php echo getProfitColorClass($bestDayProfit); ?>">
            <?php echo formatCurrency($bestDayProfit); ?>
          </div>
        </div>
        <div class="stat">
          <div class="stat-title">Worst Day</div>
          <div class="stat-value <?php echo getProfitColorClass($worstDayProfit); ?>">
            <?php echo formatCurrency($worstDayProfit); ?>
          </div>
        </div>
        <div class="stat">
          <div class="stat-title">Days Active</div>
          <div class="stat-value"><?php echo $daysActive; ?></div>
        </div>
      </div>
    </div>
  </div>

  <!-- Daily Activity Details Table -->
  <div class="card bg-base-200 mb-8">
    <div class="card-body">
      <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">Daily Activity Details</h2>
        <span class="badge badge-neutral"><?php echo $daysActive; ?> day(s)</span>
      </div>
      <div class="overflow-x-auto">
        <table class="table table-zebra">
          <thead>
            <tr>
              <th>Date</th>
              <th>Pending Robux Bought</th>
              <th>Fastflip Robux Bought</th>
              <th>Total Bought</th>
              <th>Pending Robux Sold</th>
              <th>Fastflip Robux Sold</th>
              <th>Total Sold</th>
              <th>Pending Expenses</th>
              <th>Fastflip Expenses</th>
              <th>Total Expenses</th>
              <th>Pending Profit</th>
              <th>Fastflip Profit</th>
              <th>Total Profit</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($summariesArray)): ?>
              <tr>
                <td colspan="13" class="text-center">No data available for this period.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($summariesArray as $s): ?>
                <?php
                $totalDailyBought = $s['pending_robux_bought'] + $s['fastflip_robux_bought'];
                $totalDailySold = $s['pending_robux_sold'] + $s['fastflip_robux_sold'];
                $totalDailyExpenses = $s['pending_expenses_php'] + $s['fastflip_expenses_php'];
                $totalDailyProfit = $s['pending_profit_php'] + $s['fastflip_profit_php'];
                ?>
                <tr>
                  <td><?php echo (new DateTime($s['summary_date']))->format('M d, Y'); ?></td>
                  <td><?php echo formatNumber($s['pending_robux_bought']); ?></td>
                  <td><?php echo formatNumber($s['fastflip_robux_bought']); ?></td>
                  <td><?php echo formatNumber($totalDailyBought); ?></td>
                  <td><?php echo formatNumber($s['pending_robux_sold']); ?></td>
                  <td><?php echo formatNumber($s['fastflip_robux_sold']); ?></td>
                  <td><?php echo formatNumber($totalDailySold); ?></td>
                  <td><?php echo formatCurrency($s['pending_expenses_php']); ?></td>
                  <td><?php echo formatCurrency($s['fastflip_expenses_php']); ?></td>
                  <td><?php echo formatCurrency($totalDailyExpenses); ?></td>
                  <td class="<?php echo getProfitColorClass($s['pending_profit_php']); ?>">
                    <?php echo formatCurrency($s['pending_profit_php']); ?>
                  </td>
                  <td class="<?php echo getProfitColorClass($s['fastflip_profit_php']); ?>">
                    <?php echo formatCurrency($s['fastflip_profit_php']); ?>
                  </td>
                  <td class="<?php echo getProfitColorClass($totalDailyProfit); ?>">
                    <?php echo formatCurrency($totalDailyProfit); ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>


</div>