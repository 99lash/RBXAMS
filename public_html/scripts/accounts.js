document.addEventListener('DOMContentLoaded', () => {
  // --- DOM Elements ---
  const accountsTableBody = document.getElementById('accounts-table-body');
  const pendingCountSpan = document.getElementById('pending-count');
  const fastflipCountSpan = document.getElementById('fastflip-count');
  const accountTabs = document.querySelectorAll('.tabs .tab');
  const searchInput = document.getElementById('search-input');
  const statusFilter = document.getElementById('status-filter');
  const selectAllCheckbox = document.getElementById('select-all-accounts');
  const bulkUpdateBtn = document.getElementById('bulk-update-btn');
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
  let filteredAccounts = [];
  let currentAccountType = 'pending'; // 'pending' or 'fastflip'
  let currentSearchTerm = '';
  let currentStatusFilter = 'all';
  let currentSortBy = 'name';
  let currentSortOrder = 'asc';
  let currentPage = 1;
  const itemsPerPage = 10; // For client-side pagination if needed

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

  // --- Data Fetching & Rendering ---
  const fetchAccounts = async () => {
    const colspanCount = currentAccountType === 'pending' ? 14 : 12;
    accountsTableBody.innerHTML = `<tr><td colspan="${colspanCount}" class="text-center"><span class="loading loading-spinner loading-lg"></span></td></tr>`;
    try {
      const params = new URLSearchParams();
      if (currentSortBy) {
        params.append('sort_by', currentSortBy);
        params.append('sort_order', currentSortOrder);
      }
      const response = await fetch(`/api/accounts?${params.toString()}`);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      allAccounts = await response.json();
      applyFiltersAndRender();
    } catch (error) {
      console.error("Failed to fetch accounts:", error);
      accountsTableBody.innerHTML = `<tr><td colspan="${colspanCount}" class="text-center text-error">Failed to load accounts.</td></tr>`;
      showToast('Failed to load accounts.', 'error');
    }
  };

  const applyFiltersAndRender = () => {
    let accountsToRender = allAccounts;

    // Filter by account type (tab)
    accountsToRender = accountsToRender.filter(account => {
      const type = account.account_type.toLowerCase();
      return currentAccountType === 'pending' ? type === 'pending' : type !== 'pending';
    });

    // Search
    if (currentSearchTerm) {
      const searchTermLower = currentSearchTerm.toLowerCase();
      accountsToRender = accountsToRender.filter(account =>
        account.name.toLowerCase().includes(searchTermLower) ||
        account.robux.toString().includes(searchTermLower)
      );
    }

    // Status Filter
    if (currentStatusFilter !== 'all') {
      accountsToRender = accountsToRender.filter(account => account.status === currentStatusFilter);
    }

    filteredAccounts = accountsToRender;
    switchTableHeader();
    renderAccounts(filteredAccounts);
    updateCounts();
  };

  const switchTableHeader = () => {
    if (currentAccountType === 'pending') {
      pendingHeader.classList.remove('hidden');
      fastflipHeader.classList.add('hidden');
    } else {
      pendingHeader.classList.add('hidden');
      fastflipHeader.classList.remove('hidden');
    }
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
      const costRate = account.robux > 0 && account.cost_php ? (account.cost_php / account.robux / 1000) : 0;
      const pricePhp = account.robux > 0 && account.sold_rate_usd && account.usd_to_php_rate_on_sale
        ? (account.robux / 1000) * (account.sold_rate_usd * account.usd_to_php_rate_on_sale) 
        : 0;
      const profitPhp = pricePhp - (account.cost_php ?? 0);
      
      const row = document.createElement('tr');
      row.dataset.accountId = account.id;
      
      if (currentAccountType === 'pending') {
        row.innerHTML = `
          <td><input type="checkbox" class="checkbox checkbox-sm account-checkbox" data-id="${account.id}" /></td>
          <td>${account.name}</td>
          <td class="min-w-[130px]">
            <select class="select select-bordered select-xs editable-field w-full" data-field="status" data-id="${account.id}">
              <option value="Pending" ${account.status === 'pending' ? 'selected' : ''}>Pending</option>
              <option value="Sold" ${account.status === 'sold' ? 'selected' : ''}>Sold</option>
              <option value="Unpend" ${account.status === 'unpend' ? 'selected' : ''}>Unpend</option>
              <option value="Retrieved" ${account.status === 'retrieved' ? 'selected' : ''}>Retrieved</option>
            </select>
          </td>
          <td><input type="number" class="input input-bordered input-xs w-24 editable-field" data-field="robux" data-id="${account.id}" value="${account.robux}" /></td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="cost_php" data-id="${account.id}" value="${account.cost_php ?? ''}" /></td>
          <td>${costRate > 0 ? formatCurrency(costRate) : 'N/A'}</td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="sold_rate_usd" data-id="${account.id}" value="${account.sold_rate_usd ?? ''}" placeholder="USD" /></td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="usd_to_php_rate_on_sale" data-id="${account.id}" value="${account.usd_to_php_rate_on_sale ?? ''}" placeholder="PHP" /></td>
          <td>${pricePhp > 0 ? formatCurrency(pricePhp) : 'N/A'}</td>
          <td class="${profitPhp > 0 ? 'text-success' : profitPhp < 0 ? 'text-error' : ''}">${pricePhp > 0 ? formatCurrency(profitPhp) : 'N/A'}</td>
          <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="date_added" data-id="${account.id}" value="${account.date_added ? account.date_added.replace(' ', 'T').substring(0, 16) : ''}" /></td>
          <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="unpend_date" data-id="${account.id}" value="${account.unpend_date ? account.unpend_date.replace(' ', 'T').substring(0, 16) : ''}" /></td>
          <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="sold_date" data-id="${account.id}" value="${account.sold_date ? account.sold_date.replace(' ', 'T').substring(0, 16) : ''}" ${account.status === 'Unpend' ? 'disabled' : ''} /></td>
          <td>
            <button class="btn btn-ghost btn-xs delete-btn" data-id="${account.id}">
              <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
          </td>
        `;
      } else {
        // Fastflip accounts
        row.innerHTML = `
          <td><input type="checkbox" class="checkbox checkbox-sm account-checkbox" data-id="${account.id}" /></td>
          <td>${account.name}</td>
          <td class="min-w-[130px]">
            <select class="select select-bordered select-xs editable-field w-full" data-field="status" data-id="${account.id}">
              <option value="Sold" ${account.status === 'sold' ? 'selected' : ''}>Sold</option>
              <option value="Unpend" ${account.status === 'unpend' ? 'selected' : ''}>Unpend</option>
              <option value="Retrieved" ${account.status === 'retrieved' ? 'selected' : ''}>Retrieved</option>
            </select>
          </td>
          <td><input type="number" class="input input-bordered input-xs w-24 editable-field" data-field="robux" data-id="${account.id}" value="${account.robux}" /></td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="cost_php" data-id="${account.id}" value="${account.cost_php ?? ''}" /></td>
          <td>${costRate > 0 ? formatCurrency(costRate) : 'N/A'}</td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="sold_rate_usd" data-id="${account.id}" value="${account.sold_rate_usd ?? ''}" placeholder="USD" /></td>
          <td><input type="number" step="0.01" class="input input-bordered input-xs w-24 editable-field" data-field="usd_to_php_rate_on_sale" data-id="${account.id}" value="${account.usd_to_php_rate_on_sale ?? ''}" placeholder="PHP" /></td>
          <td>${pricePhp > 0 ? formatCurrency(pricePhp) : 'N/A'}</td>
          <td class="${profitPhp > 0 ? 'text-success' : profitPhp < 0 ? 'text-error' : ''}">${pricePhp > 0 ? formatCurrency(profitPhp) : 'N/A'}</td>
          <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="date_added" data-id="${account.id}" value="${account.date_added ? account.date_added.replace(' ', 'T').substring(0, 16) : ''}" /></td>
          <td><input type="datetime-local" class="input input-bordered input-xs w-40 editable-field" data-field="sold_date" data-id="${account.id}" value="${account.sold_date ? account.sold_date.replace(' ', 'T').substring(0, 16) : ''}" /></td>
          <td>
            <button class="btn btn-ghost btn-xs delete-btn" data-id="${account.id}">
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

  const updateCounts = () => {
    const pendingAccounts = allAccounts.filter(acc => acc.account_type.toLowerCase() === 'pending');
    const fastflipAccounts = allAccounts.filter(acc => acc.account_type.toLowerCase() !== 'pending');
    pendingCountSpan.textContent = pendingAccounts.length;
    fastflipCountSpan.textContent = fastflipAccounts.length;
  };

  // --- Inline Editing ---
  const attachInlineEditListeners = () => {
    const editableFields = document.querySelectorAll('.editable-field');
    editableFields.forEach(field => {
      field.addEventListener('change', async (e) => {
        const accountId = e.target.dataset.id;
        const fieldName = e.target.dataset.field;
        let value = e.target.value;

        // Convert datetime-local to MySQL datetime format
        if (e.target.type === 'datetime-local' && value) {
          value = value.replace('T', ' ') + ':00';
        }

        // Prepare update data
        const updateData = { [fieldName]: value };

        try {
          const response = await fetch(`/accounts/${accountId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(updateData)
          });
          const result = await response.json();
          console.log(updateData);

          if (result.success) {
            showToast(`${fieldName} updated successfully.`, 'success');
            // Update local data
            const accountIndex = allAccounts.findIndex(acc => acc.id == accountId);
            if (accountIndex !== -1) {
              allAccounts[accountIndex][fieldName] = value;
            }
            // Re-render to update calculated fields
            applyFiltersAndRender();
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

  // --- Event Listeners ---
  const sortableHeaders = document.querySelectorAll('th i[data-lucide="arrow-down-up"]');
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
      applyFiltersAndRender();
      // Re-attach select all checkbox listener
      attachSelectAllListener();
    });
  });

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

  searchInput.addEventListener('input', (e) => {
    currentSearchTerm = e.target.value;
    applyFiltersAndRender();
  });

  statusFilter.addEventListener('change', (e) => {
    currentStatusFilter = e.target.value;
    applyFiltersAndRender();
  });

  // Initial select all listener
  attachSelectAllListener();

  bulkUpdateBtn.addEventListener('click', () => {
    const selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked'))
      .map(cb => cb.dataset.id);
    if (selectedIds.length === 0) {
      showToast('No accounts selected for bulk update.', 'warning');
      return;
    }
    // TODO: Implement bulk update modal/logic
    showToast(`Bulk update for IDs: ${selectedIds.join(', ')}`, 'info');
  });

  bulkDeleteBtn.addEventListener('click', async () => {
    const selectedIds = Array.from(document.querySelectorAll('.account-checkbox:checked'))
      .map(cb => cb.dataset.id);
    if (selectedIds.length === 0) {
      showToast('No accounts selected for bulk delete.', 'warning');
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
        console.log(result);
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
  fetchAccounts();
});
