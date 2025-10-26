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
          <span id="period-label">All Time</span>
          <i data-lucide="chevron-down" class="w-4 h-4"></i>
        </label>
        <ul tabindex="0" id="period-options" class="dropdown-content menu p-2 shadow bg-base-200 rounded-box w-40">
          <li><a data-period="today">Today</a></li>
          <li><a data-period="week">This Week</a></li>
          <li><a data-period="month">This Month</a></li>
          <li><a data-period="quarter">This Quarter</a></li>
          <li><a data-period="year">This Year</a></li>
          <li><a data-period="all">All Time</a></li>
        </ul>
      </div>
      <a id="export-csv" class="btn btn-outline" href="/summary/csv?period=all">
        <i data-lucide="download" class="w-4 h-4"></i>
        CSV
      </a>
      <a id="export-pdf" class="btn btn-outline" href="/summary/pdf?period=all">
        <i data-lucide="file-text" class="w-4 h-4"></i>
        PDF
      </a>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Robux Bought</h2>
        <p class="text-3xl font-bold" id="total-robux-bought">0</p>
        <p class="text-sm text-base-content/50" id="total-robux-bought-breakdown">P: 0 | F: 0</p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Robux Sold</h2>
        <p class="text-3xl font-bold" id="total-robux-sold">0</p>
        <p class="text-sm text-base-content/50" id="total-robux-sold-breakdown">P: 0 | F: 0</p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Expenses</h2>
        <p class="text-3xl font-bold" id="total-expenses">₱0.00</p>
        <p class="text-sm text-base-content/50" id="total-expenses-breakdown">P: ₱0.00 | F: ₱0.00</p>
      </div>
    </div>
    <div class="card bg-base-200">
      <div class="card-body">
        <h2 class="card-title text-base-content/60">Total Profit</h2>
        <p class="text-3xl font-bold text-error" id="total-profit">-₱0.00</p>
        <p class="text-sm text-base-content/50" id="total-profit-breakdown">P: -₱0.00 | F: ₱0.00</p>
      </div>
    </div>
  </div>

  <!-- Daily Activity Details Table -->
  <div class="card bg-base-200 mb-8">
    <div class="card-body">
      <div class="flex justify-between items-center mb-4">
        <h2 class="card-title">Daily Activity Details</h2>
        <span class="badge badge-neutral" id="activity-days">0 day(s)</span>
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
          <tbody id="summary-table-body">

          </tbody>
        </table>
      </div>
      <div id="pagination-controls" class="flex justify-between items-center mt-4">

      </div>
    </div>
  </div>

  <!-- Summary Statistics -->
  <div class="card bg-base-200">
    <div class="card-body">
      <h2 class="card-title mb-4">Summary Statistics</h2>
      <div class="stats stats-vertical lg:stats-horizontal shadow bg-base-100">
        <div class="stat">
          <div class="stat-title">Avg Daily Profit</div>
          <div class="stat-value" id="avg-daily-profit">₱0.00</div>
        </div>
        <div class="stat">
          <div class="stat-title">Best Day</div>
          <div class="stat-value text-success" id="best-day-profit">₱0.00</div>
        </div>
        <div class="stat">
          <div class="stat-title">Worst Day</div>
          <div class="stat-value text-error" id="worst-day-profit">₱0.00</div>
        </div>
        <div class="stat">
          <div class="stat-title">Days Active</div>
          <div class="stat-value" id="days-active">0</div>
        </div>
      </div>
    </div>
  </div>

</div>

<script src="/scripts/summary.js"></script>