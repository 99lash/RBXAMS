<div class="p-4">
  <h1 class="text-2xl font-bold mb-2">Manage Accounts</h1>
  <p class="text-sm mb-4 text-gray-400">
    View, edit, and manage your ROBLOX accounts with advanced features
  </p>

  <!-- Tabs -->
  <div class="tabs mb-4">
    <a class="tab tab-bordered tab-active">Pending Accounts (1)</a>
    <a class="tab tab-bordered">Fastflip Accounts (0)</a>
  </div>

  <div class="flex items-center gap-2 mb-4">
    <input id="search-input" type="text" placeholder="Search accounts..." class="input input-bordered input-sm" />

    <select id="status-filter" class="select select-bordered select-sm">
      <option value="all">All Status</option>
      <option value="Pending">Pending</option>
      <option value="Sold">Sold</option>
      <option value="Unpend">Unpend</option>
      <option value="Retrieved">Retrieved</option>
    </select>

    <button id="bulk-update" class="btn btn-sm">Bulk Update</button>
    <button id="bulk-delete" class="btn btn-sm btn-error">Delete</button>
  </div>

  <!-- Table Container -->
  <div id="accounts-table"></div>
</div>