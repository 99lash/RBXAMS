<div class="p-4 sm:p-6 md:p-8">
  <!-- Header -->
  <div class="flex flex-col sm:flex-row justify-between items-start gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-bold">Manage Accounts</h1>
      <p class="text-base-content/70">Add, view, edit, and manage your ROBLOX accounts with advanced features</p>
    </div>
    <div class="flex items-center gap-2">
      <button class="btn btn-primary" onclick="add_account_modal.showModal()">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Add Account
      </button>
    </div>
  </div>

  <!-- Tabs -->
  <div role="tablist" class="tabs tabs-boxed mb-4">
    <a role="tab" class="tab tab-active" data-account-type="pending">Pending Accounts (<span id="pending-count">0</span>)</a>
    <a role="tab" class="tab" data-account-type="fastflip">Fastflip Accounts (<span id="fastflip-count">0</span>)</a>
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
          <option value="all">All Status</option>
          <option value="Pending">Pending</option>
          <option value="Sold">Sold</option>
          <option value="Unpend">Unpend</option>
          <option value="Retrieved">Retrieved</option>
        </select>

        <button id="bulk-update-btn" class="btn btn-outline w-full md:w-auto">Bulk Update</button>
        <button id="bulk-delete-btn" class="btn btn-error btn-outline w-full md:w-auto">Bulk Delete</button>
      </div>
    </div>
  </div>

  <!-- Accounts Table -->
  <div class="card bg-base-200">
    <div class="card-body p-4">
      <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
          <thead>
            <tr>
              <th><input type="checkbox" class="checkbox checkbox-sm" id="select-all-accounts" /></th>
              <th>Name <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Status <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Robux <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Cost PHP <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Cost Rate <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Price PHP <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Profit PHP <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Date Added <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Sold Date <i data-lucide="arrow-down-up" class="w-3 h-3 inline"></i></th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="accounts-table-body">
            <!-- Account rows will be dynamically loaded here -->
          </tbody>
        </table>
      </div>
      <div class="flex justify-center mt-4">
        <div class="join" id="pagination-controls">
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
          <textarea name="cookies" class="textarea textarea-bordered h-32" placeholder="_ROBLOSECURITY=...\n_ROBLOSECURITY=..."></textarea>
        </div>
        <div class="modal-action">
          <button type="submit" class="btn btn-primary">Add Account(s)</button>
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
          <input type="number" id="edit-account-robux" name="robux" class="input input-bordered w-full" />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Cost PHP</span>
          </label>
          <input type="number" step="0.01" id="edit-account-cost-php" name="cost_php" class="input input-bordered w-full" />
        </div>
        <div class="form-control w-full mt-4">
          <label class="label">
            <span class="label-text">Price PHP</span>
          </label>
          <input type="number" step="0.01" id="edit-account-price-php" name="price_php" class="input input-bordered w-full" />
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

</div>

<script type="module" src="/scripts/accounts.js"></script>
