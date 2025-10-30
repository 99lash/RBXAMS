// --- DOM Elements ---
const accountsTableBody = document.getElementById('accounts-table-body');
const pendingCountSpan = document.getElementById('pending-count');
const fastflipCountSpan = document.getElementById('fastflip-count');
const accountTabs = document.querySelectorAll('.tabs .tab');
const searchInput = document.getElementById('search-input');
const statusFilter = document.getElementById('status-filter');
const bulkUpdateOptions = document.getElementById('bulk-update-options');
const bulkDeleteBtn = document.getElementById('bulk-delete-btn');
const addAccountForm = document.getElementById('add-account-form');
const editAccountForm = document.getElementById('edit-account-form');
const editAccountId = document.getElementById('edit-account-id');
const editAccountName = document.getElementById('edit-account-name');
const editAccountRobux = document.getElementById('edit-account-robux');
const editAccountCostPhp = document.getElementById('edit-account-cost-php');
const editAccountPricePhp = document.getElementById('edit-account-price-php');
const editAccountStatus = document.getElementById('edit-account-status');
const paginationControls = document.getElementById('pagination-controls');
const pendingHeader = document.getElementById('pending-header');
const fastflipHeader = document.getElementById('fastflip-header');

// --- State ---
let allAccounts = [];
let currentAccountType = 'pending'; // 'pending' or 'fastflip'
let currentSearchTerm = '';
let currentStatusFilter = 'all';
let currentSortBy = 'name';
let currentSortOrder = 'asc';
let currentPage = 1;
const itemsPerPage = 10; // For client-side pagination if needed
let currentUsdToPhpRate = 57; // Fallback rate

// --- Utility Functions ---
const formatCurrency = (value) => new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' }).format(value);
const formatNumber = (value) => new Intl.NumberFormat('en-US').format(value);
const formatDate = (dateString) => {
  if (!dateString || dateString === '0000-00-00 00:00:00') return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};

const updateTextColor = (element, value) => {
  element.classList.remove('text-success', 'text-error');
  if (value > 0) {
    element.classList.add('text-success');
  } else if (value < 0) {
    element.classList.add('text-error');
  }
};

const showToast = (message, type = 'success') => {
  const toastContainer = document.querySelector('.toast-container') || document.createElement('div');
  if (!toastContainer.classList.contains('toast-container')) {
    toastContainer.classList.add('toast-container', 'toast', 'toast-end');
    document.body.appendChild(toastContainer);
  }

  const alertDiv = document.createElement('div');
  alertDiv.classList.add('alert', `alert-${type}`);
  alertDiv.innerHTML = `<span>${message}</span>`;
  toastContainer.appendChild(alertDiv);

  setTimeout(() => {
    alertDiv.remove();
  }, 3000);
};

// --- Pagination Function ---
const renderPagination = (pagination) => {
  const { total_items, per_page, current_page, last_page, from, to } = pagination;

  // if (last_page <= 1) {
  //   paginationControls.innerHTML = '';
  //   return;
  // }

  let html = `
    <div>
      <p class="text-sm text-base-content/60">
        Showing <span class="font-medium">${from}</span> to <span class="font-medium">${to}</span> of <span class="font-medium">${total_items}</span> results
      </p>
    </div>
    <div class="join">
      <button class="join-item btn btn-sm" data-page="prev" ${current_page === 1 ? 'disabled' : ''}>«</button>
      <button class="join-item btn btn-sm">Page ${current_page} of ${last_page || 1}</button>
      <button class="join-item btn btn-sm" data-page="next" ${current_page === last_page || last_page === 0 ? 'disabled' : ''}>»</button>
    </div>
  `;
  paginationControls.innerHTML = html;

  paginationControls.querySelectorAll('.join-item.btn').forEach(button => {
    button.addEventListener('click', () => {
      const pageAction = button.dataset.page;
      if (pageAction === 'prev') {
        currentPage--;
      } else if (pageAction === 'next') {
        currentPage++;
      } else {
        return;
      }
      fetchAccounts();
    });
  });
};

// --- Data Fetching & Rendering ---
const fetchExchangeRate = async () => {
  try {
    const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD');
    if (!response.ok) {
      throw new Error('Failed to fetch exchange rate');
    }
    const data = await response.json();
    if (data && data.rates && data.rates.PHP) {
      currentUsdToPhpRate = data.rates.PHP;
    }
  } catch (error) {
    console.error('Error fetching exchange rate:', error);
    // Fallback rate is already set
  }
};

const fetchAccounts = async (preservePage = false) => {
  const colspanCount = currentAccountType === 'pending' ? 14 : 12;
  accountsTableBody.innerHTML = `<tr><td colspan="${colspanCount}" class="text-center"><span class="loading loading-spinner loading-lg"></span></td></tr>`;
  try {
    const params = new URLSearchParams();
    if (!preservePage) {
      currentPage = 1; // Reset to first page unless specified
    }
    params.append('page', currentPage);
    params.append('limit', itemsPerPage);
    if (currentSortBy) {
      params.append('sort_by', currentSortBy);
      params.append('sort_order', currentSortOrder);
    }
    if (currentSearchTerm) {
      params.append('search', currentSearchTerm);
    }
    if (currentStatusFilter !== 'all') {
      params.append('status', currentStatusFilter);
    }
    params.append('account_type', currentAccountType);

    const response = await fetch(`/api/accounts?${params.toString()}`);
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    const result = await response.json();

    // Update allAccounts with the current page's data
    // This assumes backend handles filtering/sorting completely
    allAccounts = result.data;

    // allAccounts = result.data.filter(account => {
    //   const type = account.account_type.toLowerCase();
    //   return currentAccountType === 'pending' ? type === 'pending' : type !== 'pending';
    // });
    // This is no longer needed as the backend is filtering by account_type

    renderAccounts(allAccounts);
    updateCounts(result.total_pending, result.total_fastflip);
    renderPagination(result.pagination);
  } catch (error) {
    console.error("Failed to fetch accounts:", error);
    accountsTableBody.innerHTML = `<tr><td colspan="${colspanCount}" class="text-center text-error">Failed to load accounts.</td></tr>`;
    showToast('Failed to load accounts.', 'error');
  }
};

const applyFiltersAndRender = () => {
  currentPage = 1; // Always reset to first page on filter/search change
  fetchAccounts();
};

const switchTableHeader = () => {
  if (currentAccountType === 'pending') {
    pendingHeader.classList.remove('hidden');
    fastflipHeader.classList.add('hidden');
    statusFilter.innerHTML = `
      <option value="all">All Status</option>
      <option value="pending">Pending</option>
      <option value="sold">Sold</option>
      <option value="unpend">Unpend</option>
      <option value="retrieved">Retrieved</option>
    `;
  } else {
    pendingHeader.classList.add('hidden');
    fastflipHeader.classList.remove('hidden');
    statusFilter.innerHTML = `
      <option value="all">All Status</option>
      <option value="sold">Sold</option>
      <option value="unpend">Unpend</option>
      <option value="retrieved">Retrieved</option>
    `;
  }
  // After rebuilding the options, re-apply the current filter selection
  statusFilter.value = currentStatusFilter;
  lucide.createIcons();
};

const renderAccounts = (accounts) => {
  accountsTableBody.innerHTML = '';
  const colspanCount = currentAccountType === 'pending' ? 14 : 12;
  if (accounts.length === 0) {
    accountsTableBody.innerHTML = `<tr><td colspan="${colspanCount}" class="text-center">No accounts found.</td></tr>`;
    return;
  }

  accounts.forEach(account => {
    // Calculate formulas
    const costRate = account.robux > 0 && account.cost_php ? (account.cost_php / (account.robux / 1000)) : 0;
    const pricePhp = account.robux > 0 && account.sold_rate_usd && account.usd_to_php_rate_on_sale
      ? (account.robux / 1000) * (account.sold_rate_usd * account.usd_to_php_rate_on_sale)
      : 0;
    const profitPhp = pricePhp - (account.cost_php ?? 0);

    const row = document.createElement('tr');
    row.dataset.accountId = account.id;

    const status = account.status.toLowerCase();
    let usdToPhpRate;
    if (status === 'sold' || status === 'retrieved') {
      usdToPhpRate = account.usd_to_php_rate_on_sale ?? currentUsdToPhpRate;
    } else {
      usdToPhpRate = currentUsdToPhpRate;
    }

    const isSold = status === 'sold';
    const disabledAttr = isSold ? 'disabled' : '';

    if (currentAccountType === 'pending') {
      row.innerHTML = `
        <td><input type="checkbox" class="checkbox checkbox-sm account-checkbox" data-id="${account.id}"/></td>
        <td>${account.name}</td>
        <td class="min-w-[130px]">
          <select class="select select-bordered select-xs editable-field w-full" data-field="status" data-id="${account.id}">
            <option value="Pending" ${account.status === 'pending' ? 'selected' : ''}>Pending</option>
            <option value="Sold" ${account.status === 'sold' ? 'selected' : ''}>Sold</option>
            <option value="Unpend" ${account.status === 'unpend' ? 'selected' : ''}>Unpend</option>
            <option value="Retrieved" ${account.status === 'retrieved' ? 'selected' : ''}>Retrieved</option>
          </select>
        </td>
        <td><input type="number" class="input input-bordered input-xs w-24 editable-field" data-field="robux" data-id="${account.id}" value="${account.robux}" ${disabledAttr} /></td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="cost_php" data-id="${account.id}" value="${account.cost_php ?? ''}" ${disabledAttr} /></td>
        <td>${costRate > 0 ? formatCurrency(costRate) : 'N/A'}</td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="sold_rate_usd" data-id="${account.id}" value="${account.sold_rate_usd ?? ''}" placeholder="USD" ${disabledAttr} /></td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="usd_to_php_rate_on_sale" data-id="${account.id}" value="${Number.parseInt(usdToPhpRate)}" placeholder="PHP" ${disabledAttr} /></td>
        <td>${pricePhp > 0 ? formatCurrency(pricePhp) : 'N/A'}</td>
        <td class="${profitPhp > 0 ? 'text-success' : profitPhp < 0 ? 'text-error' : ''}">${pricePhp > 0 ? formatCurrency(profitPhp) : 'N/A'}</td>
        <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="date_added" data-id="${account.id}" value="${account.date_added ? account.date_added.replace(' ', 'T').substring(0, 16) : ''}" ${disabledAttr} /></td>
        <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="unpend_date" data-id="${account.id}" value="${account.unpend_date ? account.unpend_date.replace(' ', 'T').substring(0, 16) : ''}" ${disabledAttr} /></td>
        <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="sold_date" data-id="${account.id}" value="${account.sold_date ? account.sold_date.replace(' ', 'T').substring(0, 16) : ''}" ${account.status === 'Unpend' ? 'disabled' : ''} ${disabledAttr} /></td>
        <td>
          <button class="btn btn-ghost btn-xs delete-btn" data-id="${account.id}" ${disabledAttr}>
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </td>
      `;
    } else {
      // Fastflip accounts
      row.innerHTML = `
        <td><input type="checkbox" class="checkbox checkbox-sm account-checkbox" data-id="${account.id}"/></td>
        <td>${account.name}</td>
        <td class="min-w-[130px]">
          <select class="select select-bordered select-xs editable-field w-full" data-field="status" data-id="${account.id}">
            <option value="Sold" ${account.status === 'sold' ? 'selected' : ''}>Sold</option>
            <option value="Unpend" ${account.status === 'unpend' ? 'selected' : ''}>Unpend</option>
            <option value="Retrieved" ${account.status === 'retrieved' ? 'selected' : ''}>Retrieved</option>
          </select>
        </td>
        <td><input type="number" class="input input-bordered input-xs w-24 editable-field" data-field="robux" data-id="${account.id}" value="${account.robux}" ${disabledAttr} /></td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="cost_php" data-id="${account.id}" value="${account.cost_php ?? ''}" ${disabledAttr} /></td>
        <td>${costRate > 0 ? formatCurrency(costRate) : 'N/A'}</td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="sold_rate_usd" data-id="${account.id}" value="${account.sold_rate_usd ?? ''}" placeholder="USD" ${disabledAttr} /></td>
        <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="usd_to_php_rate_on_sale" data-id="${account.id}" value="${Number.parseInt(usdToPhpRate)}" placeholder="PHP" ${disabledAttr} /></td>
        <td>${pricePhp > 0 ? formatCurrency(pricePhp) : 'N/A'}</td>
        <td class="${profitPhp > 0 ? 'text-success' : profitPhp < 0 ? 'text-error' : ''}">${pricePhp > 0 ? formatCurrency(profitPhp) : 'N/A'}</td>
        <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="date_added" data-id="${account.id}" value="${account.date_added ? account.date_added.replace(' ', 'T').substring(0, 16) : ''}" ${disabledAttr} /></td>
        <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="sold_date" data-id="${account.id}" value="${account.sold_date ? account.sold_date.replace(' ', 'T').substring(0, 16) : ''}" ${disabledAttr} /></td>
        <td>
          <button class="btn btn-ghost btn-xs delete-btn" data-id="${account.id}" ${disabledAttr}>
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </td>
      `;
    }

    accountsTableBody.appendChild(row);
  });
  lucide.createIcons(); // Re-render lucide icons for new elements
  attachInlineEditListeners();
};

const updateCounts = (totalPending, totalFastflip) => {
  pendingCountSpan.textContent = totalPending;
  fastflipCountSpan.textContent = totalFastflip;
};

// --- Inline Editing ---
const attachInlineEditListeners = () => {
  const editableFields = document.querySelectorAll('.editable-field');
  editableFields.forEach((field, index) => {
    field.addEventListener('change', async (e) => {
      const accountId = e.target.dataset.id;
      const fieldName = e.target.dataset.field;
      let value = e.target.value;

      const updateData = { [fieldName]: value };

      if (fieldName === 'sold_date' && value === '') {
        value = null;
      }

      // If unpend_date is edited, check if it's in the future and update status to 'Pending'
      if (fieldName === 'unpend_date' && value) {
        const unpendDate = new Date(value.replace(' ', 'T'));
        const now = new Date();
        const account = allAccounts.find(acc => acc.id == accountId);
        if (account) {
          if (unpendDate > now) {
            updateData.status = 'Pending';
          } else if (unpendDate <= now && account.status.toLowerCase() === 'pending') {
            updateData.status = 'Unpend';
          }
        }
      }

      // Convert datetime-local to MySQL datetime format
      if (e.target.type === 'datetime-local' && value) {
        value = value.replace('T', ' ') + ':00';
      }

      // Validation for setting status to "Sold"
      if (fieldName === 'status' && value.toLowerCase() === 'sold') {
        const account = allAccounts.find(acc => acc.id == accountId);
        if (account) {
          const isPending = account.account_type.toLowerCase() === 'pending';
          const prerequisites = ['cost_php', 'sold_rate_usd', 'usd_to_php_rate_on_sale'];

          // Find missing or invalid (zero or less) prerequisite fields
          const missingOrInvalid = prerequisites.filter(field => !account[field] || parseFloat(account[field]) <= 0);

          let errors = [...missingOrInvalid];

          if (isPending) {
            const unpendDate = account.unpend_date ? new Date(account.unpend_date.replace(' ', 'T')) : null;
            if (!unpendDate || unpendDate > new Date()) {
              errors.push('unpend_date (must be valid and in the past)');
            }
          }

          if (errors.length > 0) {
            showToast(`Cannot mark as "Sold". Please set: ${errors.join(', ')}.`, 'error');
            e.target.value = account.status; // Revert UI change
            fetchAccounts();
            return; // Stop the update
          }
        }
      }



      // If status changes FROM sold to unpend or retrieved, nullify sold_date
      if (fieldName === 'status') {
        const account = allAccounts.find(acc => acc.id == accountId);
        if (account && account.status.toLowerCase() === 'sold' && (value.toLowerCase() === 'unpend' || value.toLowerCase() === 'retrieved')) {
          updateData.sold_date = null;
          updateData.revert_sold = true;
        }
      }

      if (fieldName === 'cost_php' || fieldName === 'sold_rate_usd') {
        const account = allAccounts.find(acc => Number.parseInt(accountId) === acc.id);
        if (account && account.status.toLowerCase() === 'unpend' || account.status.toLowerCase() === 'pending') {
          const row = e.target.closest('tr');
          const rateInput = row.querySelector('[data-field="usd_to_php_rate_on_sale"]');
          if (rateInput) {
            updateData.usd_to_php_rate_on_sale = rateInput.value;
          }
        }
      }
      try {
        const response = await fetch(`/accounts/${accountId}`, {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(updateData)
        });

        const result = await response.json();
        if (result.success) {
          showToast(`${fieldName} updated successfully.`, 'success');
          // Update local data
          const accountIndex = allAccounts.findIndex(acc => acc.id == accountId);
          if (accountIndex !== -1) {
            allAccounts[accountIndex][fieldName] = value;
          }
          // Re-render to update calculated fields
          fetchAccounts(); // Re-fetch all accounts to update counts and pagination
        } else {
          showToast(`Failed to update ${fieldName}.`, 'error');
          // Revert the field value
          fetchAccounts();
        }
      } catch (error) {
        console.error('Error updating field:', error);
        showToast('An error occurred while updating.', 'error');
        fetchAccounts();
      }
    });
  });
};

const attachSelectAllListener = () => {
  const currentSelectAll = currentAccountType === 'pending'
    ? document.getElementById('select-all-accounts')
    : document.getElementById('select-all-accounts-fastflip');

  if (currentSelectAll) {
    // Remove old listeners by cloning
    const newSelectAll = currentSelectAll.cloneNode(true);
    currentSelectAll.parentNode.replaceChild(newSelectAll, currentSelectAll);

    newSelectAll.addEventListener('change', (e) => {
      document.querySelectorAll('.account-checkbox').forEach(checkbox => {
        checkbox.checked = e.target.checked;
      });
    });
  }
};

const renderBulkUpdateDropdown = () => {
  let options = `
      <li><a class="bulk-status-option" data-status="sold">Set to Sold</a></li>
      <li><a class="bulk-status-option" data-status="unpend">Set to Unpend</a></li>
      <li><a class="bulk-status-option" data-status="retrieved">Set to Retrieved</a></li>
  `;
  if (currentAccountType === 'pending') {
    options = `
      <li><a class="bulk-status-option" data-status="sold">Set to Sold</a></li>
      <li><a class="bulk-status-option" data-status="unpend">Set to Unpend</a></li>
      <li><a class="bulk-status-option" data-status="pending">Set to Pending</a></li>
      <li><a class="bulk-status-option" data-status="retrieved">Set to Retrieved</a></li>
    `;
  }
  bulkUpdateOptions.innerHTML = options;
};


document.addEventListener('DOMContentLoaded', () => {
  // --- Event Listeners ---
  const sortableHeaders = document.querySelectorAll('th [data-lucide="arrow-down-up"]');
  sortableHeaders.forEach(headerIcon => {
    const th = headerIcon.closest('th');
    const sortBy = th.textContent.trim().toLowerCase().replace(/ /g, '_'); // Convert 'Name ' to 'name'
    th.style.cursor = 'pointer'; // Add pointer cursor to indicate sortable
    th.addEventListener('click', () => {
      if (currentSortBy === sortBy) {
        currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        currentSortBy = sortBy;
        currentSortOrder = 'asc';
      }
      // Update header icons
      sortableHeaders.forEach(icon => {
        icon.dataset.lucide = 'arrow-down-up';
      });
      headerIcon.dataset.lucide = currentSortOrder === 'asc' ? 'arrow-up' : 'arrow-down';
      lucide.createIcons();
      fetchAccounts();
    });
  });

  accountTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      accountTabs.forEach(t => t.classList.remove('tab-active'));
      tab.classList.add('tab-active');
      currentAccountType = tab.dataset.accountType;
      renderBulkUpdateDropdown();
      switchTableHeader(); // Switch table header and status filter
      applyFiltersAndRender();
      // Re-attach select all checkbox listener
      attachSelectAllListener();
    });
  });

  // Initial select all listener
  attachSelectAllListener();

  searchInput.addEventListener('input', (e) => {
    currentSearchTerm = e.target.value;
    applyFiltersAndRender();
  });

  statusFilter.addEventListener('change', (e) => {
    currentStatusFilter = e.target.value;
    applyFiltersAndRender();
  });

  bulkUpdateOptions.addEventListener('click', async (e) => {
    if (!e.target.classList.contains('bulk-status-option')) return;
    e.preventDefault(); // Prevent link navigation

    const status = e.target.dataset.status;
    const selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked'))
      .map(cb => cb.dataset.id);

    if (status === 'sold') {
      const accountsToUpdate = allAccounts.filter(acc => selectedIds.includes(String(acc.id)));
      const accountsWithErrors = [];

      for (const account of accountsToUpdate) {
        const isPending = account.account_type.toLowerCase() === 'pending';
        const prerequisites = ['cost_php', 'sold_rate_usd', 'usd_to_php_rate_on_sale'];
        const missingOrInvalid = prerequisites.filter(field => !account[field] || parseFloat(account[field]) <= 0);

        let errors = [...missingOrInvalid];

        if (isPending) {
          const unpendDate = account.unpend_date ? new Date(account.unpend_date.replace(' ', 'T')) : null;
          if (!unpendDate || unpendDate > new Date()) {
            errors.push('unpend_date (must be valid and in the past)');
          }
        }

        if (errors.length > 0) {
          accountsWithErrors.push({
            name: account.name,
            errors
          });
        }
      }

      if (accountsWithErrors.length > 0) {
        showToast(`Could not update ${accountsWithErrors.length} accounts to "Sold" due to missing prerequisites. See console for details.`, 'error');
        console.error('Bulk update validation failed for:', accountsWithErrors);
        return; // Stop the process
      }
    }
    if (selectedIds.length === 0) {
      showToast('No accounts selected.', 'error');
      return;
    }

    if (!confirm(`Are you sure you want to set ${selectedIds.length} account(s) to "${status}"?`)) {
      return;
    }

    try {
      const payload = { ids: selectedIds, status: status };

      if (status === 'unpend' || status === 'retrieved') {
        const accountsToUpdate = allAccounts.filter(acc => selectedIds.includes(String(acc.id)));
        const allSelectedAreSold = accountsToUpdate.length > 0 && accountsToUpdate.every(acc => acc.status.toLowerCase() === 'sold');

        if (allSelectedAreSold) {
          payload.sold_date = null;
        }
      }

      const response = await fetch('/accounts/bulk-update/status', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const result = await response.json();
      if (result.success) {
        showToast(`Successfully updated ${result.updated.length} account(s).`);
        fetchAccounts();
      } else {
        showToast(`Failed to update accounts: ${result.detail || 'Unknown error'}`, 'error');
      }
    } catch (error) {
      console.error('Error during bulk status update:', error);
      showToast('An error occurred during bulk update.', 'error');
    }
  });

  bulkDeleteBtn.addEventListener('click', async () => {
    const selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked'))
      .map(cb => cb.dataset.id);
    if (selectedIds.length === 0) {
      showToast('No accounts selected for bulk delete.', 'warning');
      return;
    }

    const accountsToDelete = allAccounts.filter(acc => selectedIds.includes(String(acc.id)));
    const protectedAccounts = accountsToDelete.filter(acc => acc.status.toLowerCase() === 'sold' || acc.status.toLowerCase() === 'retrieved');

    if (protectedAccounts.length > 0) {
      const protectedNames = protectedAccounts.map(acc => acc.name).join(', ');
      showToast(`Cannot delete selected accounts. Accounts in 'Sold' or 'Retrieved' status cannot be bulk deleted: ${protectedNames}.`, 'error');
      return;
    }

    if (!confirm(`Are you sure you want to delete ${selectedIds.length} account(s)?`)) {
      return;
    }

    try {
      const response = await fetch('/accounts/bulk-delete', {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ ids: selectedIds })
      });
      const result = await response.json();
      if (result.success) {
        showToast(`Successfully deleted ${result.deleted.length} account(s).`);
        fetchAccounts(); // Re-fetch and render accounts
      } else {
        showToast(`Failed to delete accounts: ${result.detail || 'Unknown error'}`, 'error');
      }
    } catch (error) {
      console.error('Error during bulk delete:', error);
      showToast('An error occurred during bulk delete.', 'error');
    }
  });

  addAccountForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(addAccountForm);
    try {
      const response = await fetch('/accounts', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();
      if (response.ok) {
        if (result.created.length > 0) {
          showToast(`Successfully added ${result.created.length} account(s).`);
        }
        if (result.duplicate.length > 0) {
          result.duplicate.forEach(dup => showToast(`Duplicate: ${dup.account.name} (${dup.message})`, 'warning'));
        }
        if (result.failed.length > 0) {
          result.failed.forEach(fail => showToast(`Failed to add: ${fail.cookie} (${fail.message})`, 'error'));
        }
        add_account_modal.close();
        addAccountForm.reset();
        fetchAccounts();
      } else {
        showToast(`Error adding accounts: ${result.detail || 'Unknown error'}`, 'error');
      }
    } catch (error) {
      console.error('Error adding accounts:', error);
      showToast('An error occurred while adding accounts.', 'error');
    }
  });

  accountsTableBody.addEventListener('click', async (e) => {
    // Delete button
    if (e.target.closest('.delete-btn')) {
      const id = e.target.closest('.delete-btn').dataset.id;
      if (!confirm(`Are you sure you want to delete account ${id}?`)) {
        return;
      }
      try {
        const response = await fetch(`/accounts/bulk-delete`, {
          method: 'DELETE',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ids: [id] })
        });
        const result = await response.json();
        if (result.success) {
          showToast(`Account ${id} deleted successfully.`);
          fetchAccounts();
        } else {
          showToast(`Failed to delete account ${id}: ${result.detail || 'Unknown error'}`, 'error');
        }
      } catch (error) {
        console.error('Error deleting account:', error);
        showToast('An error occurred while deleting the account.', 'error');
      }
    }
  });

  editAccountForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = editAccountId.value;
    const formData = new FormData(editAccountForm);
    const patchData = {};
    for (const [key, value] of formData.entries()) {
      patchData[key] = value;
    }
    // Remove id from patchData as it's in the URL
    delete patchData.id;

    try {
      const response = await fetch(`/accounts/updateById/${id}`, {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(patchData)
      });
      const result = await response.json();
      if (result.success) {
        showToast(`Account ${id} updated successfully.`);
        edit_account_modal.close();
        fetchAccounts();
      } else {
        showToast(`Failed to update account ${id}.`, 'error');
      }
    } catch (error) {
      console.error('Error updating account:', error);
      showToast('An error occurred while updating the account.', 'error');
    }
  });

  // --- Initial Load ---
  fetchExchangeRate().then(() => {
    renderBulkUpdateDropdown();
    switchTableHeader(); // Initialize status filter and table header
    fetchAccounts();
  });
});