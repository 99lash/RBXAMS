document.addEventListener('DOMContentLoaded', () => {
  // --- DOM Elements ---
  const periodOptions = document.getElementById('period-options');
  const periodLabel = document.getElementById('period-label');
  const tableBody = document.getElementById('summary-table-body');
  const paginationControls = document.getElementById('pagination-controls'); // New

  // Summary Card Elements
  const totalRobuxBoughtEl = document.getElementById('total-robux-bought');
  const totalRobuxBoughtBreakdownEl = document.getElementById('total-robux-bought-breakdown');
  const totalRobuxSoldEl = document.getElementById('total-robux-sold');
  const totalRobuxSoldBreakdownEl = document.getElementById('total-robux-sold-breakdown');
  const totalExpensesEl = document.getElementById('total-expenses');
  const totalExpensesBreakdownEl = document.getElementById('total-expenses-breakdown');
  const totalProfitEl = document.getElementById('total-profit');
  const totalProfitBreakdownEl = document.getElementById('total-profit-breakdown');

  // Details & Statistics Elements
  const activityDaysEl = document.getElementById('activity-days');
  const avgDailyProfitEl = document.getElementById('avg-daily-profit');
  const bestDayProfitEl = document.getElementById('best-day-profit');
  const worstDayProfitEl = document.getElementById('worst-day-profit');
  const daysActiveEl = document.getElementById('days-active');

  // Export Links
  const csvLink = document.getElementById('export-csv');
  const pdfLink = document.getElementById('export-pdf');

  // --- State ---
  let currentPeriod = 'all';
  let currentPage = 1; // New: for pagination

  // --- Utility Functions ---
  const formatCurrency = (value) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
  const formatNumber = (value) => new Intl.NumberFormat('en-US').format(value);

  const setLoading = (isLoading) => {
    if (isLoading) {
      tableBody.innerHTML = '<tr><td colspan="13" class="text-center"><span class="loading loading-spinner"></span></td></tr>';
      paginationControls.innerHTML = ''; // Clear pagination while loading
    }
  };

  const updateTextColor = (element, value) => {
    element.classList.remove('text-success', 'text-error');
    if (value > 0) {
      element.classList.add('text-success');
    } else if (value < 0) {
      element.classList.add('text-error');
    }
  };

  // --- New Pagination Function ---
  const renderPagination = (pagination) => {
    const { total_items, per_page, current_page, last_page, from, to } = pagination;

    if (last_page <= 1) {
      paginationControls.innerHTML = '';
      return;
    }

    let html = `
      <div>
        <p class="text-sm text-base-content/60">
          Showing <span class="font-medium">${from}</span> to <span class="font-medium">${to}</span> of <span class="font-medium">${total_items}</span> results
        </p>
      </div>
      <div class="join">
        <button class="join-item btn btn-sm" data-page="prev" ${current_page === 1 ? 'disabled' : ''}>«</button>
        <button class="join-item btn btn-sm">Page ${current_page} of ${last_page}</button>
        <button class="join-item btn btn-sm" data-page="next" ${current_page === last_page ? 'disabled' : ''}>»</button>
      </div>
    `;
    paginationControls.innerHTML = html;
  };


  // --- Core Functions (Updated) ---
  const updateUI = (response) => {
    const { data: summaries, pagination } = response; // Updated: handle new response structure

    if (summaries.length === 0) {
      tableBody.innerHTML = '<tr><td colspan="13" class="text-center">No data available for this period.</td></tr>';
      paginationControls.innerHTML = ''; // Clear pagination
      // Reset all other fields
      totalRobuxBoughtEl.textContent = '0';
      totalRobuxBoughtBreakdownEl.textContent = 'P: 0 | F: 0';
      totalRobuxSoldEl.textContent = '0';
      totalRobuxSoldBreakdownEl.textContent = 'P: 0 | F: 0';
      totalExpensesEl.textContent = formatCurrency(0);
      totalExpensesBreakdownEl.textContent = `P: ${formatCurrency(0)} | F: ${formatCurrency(0)}`;
      totalProfitEl.textContent = formatCurrency(0);
      totalProfitBreakdownEl.textContent = `P: ${formatCurrency(0)} | F: ${formatCurrency(0)}`;
      activityDaysEl.textContent = '0 day(s)';
      avgDailyProfitEl.textContent = formatCurrency(0);
      bestDayProfitEl.textContent = formatCurrency(0);
      worstDayProfitEl.textContent = formatCurrency(0);
      daysActiveEl.textContent = '0';
      updateTextColor(totalProfitEl, 0);
      updateTextColor(avgDailyProfitEl, 0);
      updateTextColor(bestDayProfitEl, 0);
      updateTextColor(worstDayProfitEl, 0);
      return;
    }

    // 1. Update Summary Cards (This logic remains the same as it reflects the whole period)
    // Note: For pagination, the summary cards should ideally reflect the totals for the *entire period*,
    // not just the current page. The current backend implementation returns paginated data.
    // For now, we will recalculate totals on the client side from the full dataset if we decide to fetch it.
    // Let's assume for now the API will be extended to provide totals separately.
    // For this implementation, we will leave the top cards as they are, reflecting the totals of the *fetched page*.
    // This is a known limitation to be addressed later if needed.
    const totals = summaries.reduce((acc, s) => {
      acc.pending_robux_bought += s.pending_robux_bought;
      acc.fastflip_robux_bought += s.fastflip_robux_bought;
      acc.pending_robux_sold += s.pending_robux_sold;
      acc.fastflip_robux_sold += s.fastflip_robux_sold;
      acc.pending_expenses_php += s.pending_expenses_php;
      acc.fastflip_expenses_php += s.fastflip_expenses_php;
      acc.pending_profit_php += s.pending_profit_php;
      acc.fastflip_profit_php += s.fastflip_profit_php;
      return acc;
    }, {
      pending_robux_bought: 0, fastflip_robux_bought: 0,
      pending_robux_sold: 0, fastflip_robux_sold: 0,
      pending_expenses_php: 0, fastflip_expenses_php: 0,
      pending_profit_php: 0, fastflip_profit_php: 0,
    });

    const totalBought = totals.pending_robux_bought + totals.fastflip_robux_bought;
    const totalSold = totals.pending_robux_sold + totals.fastflip_robux_sold;
    const totalExpenses = totals.pending_expenses_php + totals.fastflip_expenses_php;
    const totalProfit = totals.pending_profit_php + totals.fastflip_profit_php;

    totalRobuxBoughtEl.textContent = formatNumber(totalBought);
    totalRobuxBoughtBreakdownEl.textContent = `P: ${formatNumber(totals.pending_robux_bought)} | F: ${formatNumber(totals.fastflip_robux_bought)}`;
    totalRobuxSoldEl.textContent = formatNumber(totalSold);
    totalRobuxSoldBreakdownEl.textContent = `P: ${formatNumber(totals.pending_robux_sold)} | F: ${formatNumber(totals.fastflip_robux_sold)}`;
    totalExpensesEl.textContent = formatCurrency(totalExpenses);
    totalExpensesBreakdownEl.textContent = `P: ${formatCurrency(totals.pending_expenses_php)} | F: ${formatCurrency(totals.fastflip_expenses_php)}`;
    totalProfitEl.textContent = formatCurrency(totalProfit);
    totalProfitBreakdownEl.textContent = `P: ${formatCurrency(totals.pending_profit_php)} | F: ${formatCurrency(totals.fastflip_profit_php)}`;
    updateTextColor(totalProfitEl, totalProfit);


    // 2. Update Details Table
    tableBody.innerHTML = ''; // Clear loading/old data
    summaries.forEach(s => {
      const row = document.createElement('tr');
      const totalDailyBought = s.pending_robux_bought + s.fastflip_robux_bought;
      const totalDailySold = s.pending_robux_sold + s.fastflip_robux_sold;
      const totalDailyExpenses = s.pending_expenses_php + s.fastflip_expenses_php;
      const totalDailyProfit = s.pending_profit_php + s.fastflip_profit_php;

      row.innerHTML = `
        <td>${new Date(s.summary_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</td>
        <td>${formatNumber(s.pending_robux_bought)}</td>
        <td>${formatNumber(s.fastflip_robux_bought)}</td>
        <td>${formatNumber(totalDailyBought)}</td>
        <td>${formatNumber(s.pending_robux_sold)}</td>
        <td>${formatNumber(s.fastflip_robux_sold)}</td>
        <td>${formatNumber(totalDailySold)}</td>
        <td>${formatCurrency(s.pending_expenses_php)}</td>
        <td>${formatCurrency(s.fastflip_expenses_php)}</td>
        <td>${formatCurrency(totalDailyExpenses)}</td>
        <td class="${totalDailyProfit > 0 ? 'text-success' : totalDailyProfit < 0 ? 'text-error' : ''}">${formatCurrency(s.pending_profit_php)}</td>
        <td class="${totalDailyProfit > 0 ? 'text-success' : totalDailyProfit < 0 ? 'text-error' : ''}">${formatCurrency(s.fastflip_profit_php)}</td>
        <td class="${totalDailyProfit > 0 ? 'text-success' : totalDailyProfit < 0 ? 'text-error' : ''}">${formatCurrency(totalDailyProfit)}</td>
      `;
      tableBody.appendChild(row);
    });

    // 3. Update Summary Statistics & Pagination
    const daysActive = pagination.total_items; // Use total from pagination
    const avgDailyProfit = daysActive > 0 ? totalProfit / summaries.length : 0; // Avg for the page
    const dailyProfits = summaries.map(s => s.pending_profit_php + s.fastflip_profit_php);
    const bestDayProfit = daysActive > 0 ? Math.max(...dailyProfits) : 0;
    const worstDayProfit = daysActive > 0 ? Math.min(...dailyProfits) : 0;

    activityDaysEl.textContent = `${daysActive} day(s)`;
    daysActiveEl.textContent = daysActive;
    avgDailyProfitEl.textContent = formatCurrency(avgDailyProfit);
    bestDayProfitEl.textContent = formatCurrency(bestDayProfit);
    worstDayProfitEl.textContent = formatCurrency(worstDayProfit);

    updateTextColor(avgDailyProfitEl, avgDailyProfit);
    updateTextColor(bestDayProfitEl, bestDayProfit);
    updateTextColor(worstDayProfitEl, worstDayProfit);

    renderPagination(pagination); // New
  };

  const fetchData = async (period, page = 1) => { // Updated
    setLoading(true);
    try {
      const response = await fetch(`/api/summary?period=${period}&page=${page}`); // Updated
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      updateUI(data);
    } catch (error) {
      console.error("Failed to fetch summary data:", error);
      tableBody.innerHTML = `<tr><td colspan="13" class="text-center text-error">Failed to load data.</td></tr>`;
      paginationControls.innerHTML = '';
    }
  };

  // --- Event Listeners ---
  periodOptions.addEventListener('click', (e) => {
    if (e.target.tagName === 'A') {
      const period = e.target.dataset.period;
      if (period && period !== currentPeriod) {
        currentPeriod = period;
        currentPage = 1; // Reset to first page
        periodLabel.textContent = e.target.textContent;
        fetchData(currentPeriod, currentPage);

        // Update export links
        if (csvLink) csvLink.href = `/summary/csv?period=${period}`;
        if (pdfLink) pdfLink.href = `/summary/pdf?period=${period}`;

        // Close dropdown
        if (document.activeElement) {
          document.activeElement.blur();
        }
      }
    }
  });

  // New: Pagination listener
  paginationControls.addEventListener('click', (e) => {
    const button = e.target.closest('button');
    if (!button) return;

    const pageAction = button.dataset.page;
    if (pageAction === 'prev') {
      currentPage--;
    } else if (pageAction === 'next') {
      currentPage++;
    } else {
      return;
    }
    fetchData(currentPeriod, currentPage);
  });


  // --- Initial Load ---
  fetchData(currentPeriod, currentPage);
});