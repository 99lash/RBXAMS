<div class="p-4 sm:p-6 md:p-8">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
    <div>
      <h1 class="text-xl sm:text-2xl font-bold">Manage Accounts</h1>
      <p class="text-sm sm:text-base text-base-content/70">Add, view, edit, and manage your ROBLOX accounts with advanced features</p>
    </div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary" onclick="add_account_modal.showModal()">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Add Account
      </button>
    </div>
  </div>

  <!-- Tabs -->
  <div role="tablist" class="tabs tabs-border mb-4 gap-1 sm:gap-0">
    <a role="tab" class="tab p-0 sm:px-2 tab-active" data-account-type="pending">Pending Accounts (<span
        id="pending-count">0</span>)</a>
    <a role="tab" class="tab p-0 sm:px-2" data-account-type="fastflip">Fastflip Accounts (<span id="fastflip-count">0</span>)</a>
  </div>

  <!-- Account List Controls -->
  <div class="card bg-base-200 mb-4">
    <div class="card-body p-4">
      <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <label class="input input-bordered flex items-center gap-2 flex-grow">
          <input id="search-input" type="text" class="grow" placeholder="Search accounts..." />
          <i data-lucide="search" class="w-4 h-4"></i>
        </label>

        <select id="status-filter" class="select select-bordered w-full md:w-auto">
          <!-- <option value="all">All Status</option>
          <option value="Pending">Pending</option>
          <option value="Sold">Sold</option>
          <option value="Unpend">Unpend</option>
          <option value="Retrieved">Retrieved</option> -->
        </select>

        <div class="dropdown dropdown-end">
          <button tabindex="0" role="button" class="btn btn-outline w-full md:w-auto">
            Bulk Actions
            <i data-lucide="chevron-down" class="w-4 h-4"></i>
          </button>
          <ul tabindex="0" id="bulk-update-options" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
            <!-- Options will be populated by JS -->
          </ul>
        </div>
        <button id="bulk-delete-btn" class="btn btn-error btn-outline w-full md:w-auto">Delete Selected</button>
      </div>
    </div>
  </div>

  <!-- Accounts Table -->
  <div class="card bg-base-200">
    <div class="card-body p-4">
      <div class="overflow-x-auto">
        <table class="table table-auto w-full">
          <thead id="accounts-table-header">
            <!-- Pending Accounts Header -->
            <tr id="pending-header" class="account-header">
              <th><input type="checkbox" class="checkbox checkbox-sm" id="select-all-accounts" /></th>
              <th data-sort="name">Name <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="status">Status <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="robux">Robux (R$)<i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="cost_php">Cost (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="cost_rate">Cost Rate (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="sold_rate_usd">Rate Sold ($) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="usd_to_php_rate_on_sale">Dollar-Peso Rate<i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="price">Price (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="profit">Profit (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="date_added">Date Added <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="unpend_date">Unpend Date <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="sold_date">Sold Date <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Actions</th>
            </tr>
            <!-- Fastflip Accounts Header -->
            <tr id="fastflip-header" class="account-header hidden">
              <th><input type="checkbox" class="checkbox checkbox-sm" id="select-all-accounts-fastflip" /></th>
              <th data-sort="name">Name <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="status">Status <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="robux">Robux (R$) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="cost_php">Cost (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="cost_rate">Cost Rate (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="sold_rate_usd">Rate Sold ($) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="usd_to_php_rate_on_sale">Dollar-Peso Rate <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="price">Price (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="profit">Profit (₱) <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="date_added">Date Added <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th data-sort="sold_date">Sold Date <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="accounts-table-body">
            <!-- Account rows will be dynamically loaded here -->
          </tbody>
        </table>
      </div>
      <div class="flex justify-center mt-4">
        <div class="join flex justify-between w-full" id="pagination-controls">
          <!-- Pagination will be dynamically loaded here -->
        </div>
      </div>
    </div>
  </div>

  <!-- Add Account Modal -->
  <dialog id="add_account_modal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Add New Account(s)</h3>
      <form id="add-account-form" method="POST">
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Roblox Account Cookies (one per line)</span>
          </label>
          <textarea name="cookies" class="textarea textarea-bordered h-32 w-full"
            placeholder="<?= "Cookie1\nCookie2\nCookie3\n..." ?>"></textarea>
        </div>
        <div class="modal-action">
          <button type="submit" id="add-account-btn" class="btn btn-primary">
            <span class="loading loading-spinner hidden"></span>
            <span class="btn-text">Add Account(s)</span>
          </button>
          <button type="button" class="btn" onclick="add_account_modal.close()">Cancel</button>
        </div>
      </form>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>

  <!-- Edit Account Modal -->
  <dialog id="edit_account_modal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Edit Account</h3>
      <form id="edit-account-form" method="POST">
        <input type="hidden" id="edit-account-id" name="id">
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Account Name</span>
          </label>
          <input type="text" id="edit-account-name" name="name" class="input input-bordered w-full" />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Robux</span>
          </label>
          <input type="number" id="edit-account-robux" name="robux" class="input input-bordered w-full" disabled />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Cost PHP</span>
          </label>
          <input type="number" step="0.01" id="edit-account-cost-php" name="cost_php"
            class="input input-bordered w-full" />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Price PHP</span>
          </label>
          <input type="number" step="0.01" id="edit-account-price-php" name="price_php"
            class="input input-bordered w-full" />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Status</span>
          </label>
          <select id="edit-account-status" name="status" class="select select-bordered w-full">
            <option value="Pending">Pending</option>
            <option value="Sold">Sold</option>
            <option value="Unpend">Unpend</option>
            <option value="Retrieved">Retrieved</option>
          </select>
        </div>
        <div class="modal-action">
          <button type="submit" class="btn btn-primary">Save Changes</button>
          <button type="button" class="btn" onclick="edit_account_modal.close()">Cancel</button>
        </div>
      </form>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>

  <!-- Confirmation Modal -->
  <dialog id="confirmation_modal" class="modal">
    <div class="modal-box">
      <h3 class="font-bold text-lg">Confirm Action</h3>
      <p id="confirmation_message" class="py-4"></p>
      <div class="modal-action">
        <button id="confirm_btn" class="btn btn-primary">Confirm</button>
        <button id="cancel_btn" class="btn">Cancel</button>
      </div>
    </div>
    <form method="dialog" class="modal-backdrop">
      <button>close</button>
    </form>
  </dialog>

</div>

<script type="module" src="/scripts/accounts.js"></script>