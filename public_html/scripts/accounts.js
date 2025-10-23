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
    accountsTableBody.innerHTML = '<tr><td colspan="11" class="text-center"><span class="loading loading-spinner loading-lg"></span></td></tr>';
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
      accountsTableBody.innerHTML = '<tr><td colspan="11" class="text-center text-error">Failed to load accounts.</td></tr>';
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
    renderAccounts(filteredAccounts);
    updateCounts();
  };

  const renderAccounts = (accounts) => {
    accountsTableBody.innerHTML = '';
    if (accounts.length === 0) {
      accountsTableBody.innerHTML = '<tr><td colspan="11" class="text-center">No accounts found.</td></tr>';
      return;
    }

    accounts.forEach(account => {
      const profitPhp = (account.price_php ?? 0) - (account.cost_php ?? 0);
      const row = document.createElement('tr');
      row.innerHTML = `
        <td><input type="checkbox" class="checkbox checkbox-sm account-checkbox" data-id="${account.id}" /></td>
        <td>${account.name}</td>
        <td>
          <span class="badge badge-outline ${account.status === 'Sold' ? 'badge-success' : account.status === 'Pending' ? 'badge-warning' : 'badge-info'}">
            ${account.status}
          </span>
        </td>
        <td>${formatNumber(account.robux)}</td>
        <td>${formatCurrency(account.cost_php)}</td>
        <td>${formatCurrency(account.cost_php / account.robux)}</td>
        <td>${formatCurrency(account.price_php ?? 0)}</td>
        <td class="${profitPhp > 0 ? 'text-success' : profitPhp < 0 ? 'text-error' : ''}">${formatCurrency(profitPhp)}</td>
        <td>${formatDate(account.date_added)}</td>
        <td>${formatDate(account.sold_date)}</td>
        <td>
          <button class="btn btn-ghost btn-xs edit-btn" data-id="${account.id}">
            <i data-lucide="edit" class="w-4 h-4"></i>
          </button>
          <button class="btn btn-ghost btn-xs delete-btn" data-id="${account.id}">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </td>
      `;
      accountsTableBody.appendChild(row);
    });
    lucide.createIcons(); // Re-render lucide icons for new elements
  };

  const updateCounts = () => {
    const pendingAccounts = allAccounts.filter(acc => acc.account_type.toLowerCase() === 'pending');
    const fastflipAccounts = allAccounts.filter(acc => acc.account_type.toLowerCase() !== 'pending');
    pendingCountSpan.textContent = pendingAccounts.length;
    fastflipCountSpan.textContent = fastflipAccounts.length;
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
    });
  });

  searchInput.addEventListener('input', (e) => {
    currentSearchTerm = e.target.value;
    applyFiltersAndRender();
  });

  statusFilter.addEventListener('change', (e) => {
    currentStatusFilter = e.target.value;
    applyFiltersAndRender();
  });

  selectAllCheckbox.addEventListener('change', (e) => {
    document.querySelectorAll('.account-checkbox').forEach(checkbox => {
      checkbox.checked = e.target.checked;
    });
  });

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
    // Edit button
    if (e.target.closest('.edit-btn')) {
      const id = e.target.closest('.edit-btn').dataset.id;
      const accountToEdit = allAccounts.find(acc => acc.id == id);
      if (accountToEdit) {
        editAccountId.value = accountToEdit.id;
        editAccountName.value = accountToEdit.name;
        editAccountRobux.value = accountToEdit.robux;
        editAccountCostPhp.value = accountToEdit.cost_php;
        editAccountPricePhp.value = accountToEdit.price_php ?? '';
        editAccountStatus.value = accountToEdit.status;
        edit_account_modal.showModal();
      }
    }

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
