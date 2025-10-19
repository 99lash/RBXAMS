document.addEventListener('DOMContentLoaded', () => {
  // --- DOM Elements ---
  const periodOptions = document.getElementById('period-options');
  const periodLabel = document.getElementById('period-label');
  const tableBody = document.getElementById('summary-table-body');

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

  // --- State ---
  let currentPeriod = 'all';

  // --- Utility Functions ---
  const formatCurrency = (value) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
  const formatNumber = (value) => new Intl.NumberFormat('en-US').format(value);

  const setLoading = (isLoading) => {
    if (isLoading) {
      tableBody.innerHTML = '<tr><td colspan="13" class="text-center"><span class="loading loading-spinner"></span></td></tr>';
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

  // --- Core Functions ---
  const updateUI = (data) => {
    // The API might return a single object (for 'today') or an array
    const summaries = Array.isArray(data) ? data : [data];

    if (summaries.length === 0 || !summaries[0]) {
      tableBody.innerHTML = '<tr><td colspan="13" class="text-center">No data available for this period.</td></tr>';
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

    // 1. Update Summary Cards
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

    // 3. Update Summary Statistics
    const daysActive = summaries.length;
    const avgDailyProfit = daysActive > 0 ? totalProfit / daysActive : 0;
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
  };

  const fetchData = async (period) => {
    setLoading(true);
    try {
      const response = await fetch(`/summary?period=${period}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      updateUI(data);
    } catch (error) {
      console.error("Failed to fetch summary data:", error);
      tableBody.innerHTML = `<tr><td colspan="13" class="text-center text-error">Failed to load data.</td></tr>`;
    }
  };

  // --- Event Listeners ---
  periodOptions.addEventListener('click', (e) => {
    if (e.target.tagName === 'A') {
      const period = e.target.dataset.period;
      if (period && period !== currentPeriod) {
        currentPeriod = period;
        periodLabel.textContent = e.target.textContent;
        fetchData(currentPeriod);
        // Close dropdown
        if (document.activeElement) {
          document.activeElement.blur();
        }
      }
    }
  });

  // --- Initial Load ---
  fetchData(currentPeriod);
});
