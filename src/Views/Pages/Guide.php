<div class="p-4">
  <h1 class="text-2xl font-bold mb-2">User Guide</h1>
  <p class="text-base-content/70 mb-6">
    Complete guide to using the Roblox Asset Monitoring System (RBXAMS)
  </p>

  <!-- Tabs -->
  <div role="tablist" class="tabs tabs-bordered w-full">
    <a role="tab" class="tab tab-active" id="tab-started">Getting Started</a>
    <a role="tab" class="tab" id="tab-features">Features</a>
    <a role="tab" class="tab" id="tab-management">Account Management</a>
    <a role="tab" class="tab" id="tab-faq">FAQ</a>
  </div>

  <!-- Tab Content -->
  <div id="tab-contents" class="mt-6">
    <!-- Getting Started (default visible) -->
    <div id="content-started" class="tab-content block">

      <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300 mb-6">
        <h2 class="font-semibold mb-4">Quick Start Guide</h2>
        <ol class="list-decimal list-inside space-y-4">
          <li>
            <b>Add Your First Account</b><br>
            Navigate to "Add Account" and paste your Roblox cookies. The system will automatically fetch account details
            and determine the account type.
          </li>
          <li>
            <b>Monitor Your Dashboard</b><br>
            View real-time statistics including total accounts, robux amounts, expenses, and profits all in one
            centralized dashboard.
          </li>
          <li>
            <b>Manage Account Status</b><br>
            Use the "Manage Accounts" section to update account statuses, set unpend dates, and track profit/loss for
            each account.
          </li>
          <li>
            <b>Review Daily Summary</b><br>
            Check your daily activity summary for detailed reports and analytics with export options.
          </li>
        </ol>
      </div>

      <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
        <h2 class="font-semibold mb-4">Account Types Explained</h2>
        <div class="mb-2">
          <span class="badge badge-warning">Pending</span><br>
          Accounts with incoming Robux transactions. These start with "Pending" status and can be automatically changed
          to "Unpend" based on set dates.
        </div>
        <div>
          <span class="badge badge-info">Fastflip</span><br>
          Accounts without incoming transactions. These start with "Unpend" status and are ready for immediate
          transactions.
        </div>
      </div>
    </div>

    <!-- Features (hidden initially) -->
    <div id="content-features" class="tab-content hidden">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
          <h2 class="font-semibold mb-4">Dashboard Analytics</h2>
          <ul class="list-disc list-inside space-y-2">
            <li>Real-time account statistics</li>
            <li>Daily activity tracking</li>
            <li>Profit/loss calculations</li>
            <li>Visual charts and graphs</li>
            <li>Performance metrics</li>
          </ul>
        </div>
        <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
          <h2 class="font-semibold mb-4">Bulk Account Import</h2>
          <ul class="list-disc list-inside space-y-2">
            <li>Import multiple accounts at once</li>
            <li>Automatic account type detection</li>
            <li>Cookie-based authentication</li>
            <li>Real-time balance fetching</li>
            <li>Automatic cost calculations</li>
          </ul>
        </div>
        <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
          <h2 class="font-semibold mb-4">Advanced Management</h2>
          <ul class="list-disc list-inside space-y-2">
            <li>Multi-row selection</li>
            <li>Bulk status updates</li>
            <li>Bulk delete functionality</li>
            <li>Inline editing</li>
            <li>Advanced sorting & filtering</li>
          </ul>
        </div>
        <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
          <h2 class="font-semibold mb-4">Automation Features</h2>
          <ul class="list-disc list-inside space-y-2">
            <li>Automatic status changes</li>
            <li>Unpend date scheduling</li>
            <li>Real-time notifications</li>
            <li>Daily activity summaries</li>
            <li>Export capabilities</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Account Management (hidden initially) -->
    <div id="content-management" class="tab-content hidden">
      <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300 mb-6">
        <h2 class="font-semibold mb-4">Account Status Workflow</h2>
        <p class="mb-2">Understanding the account lifecycle</p>
        <div class="flex items-center space-x-2 mb-4">
          <span class="badge badge-warning">Pending</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
          <span class="badge badge-info">Unpend</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
          <span class="badge badge-success">Sold</span>
        </div>

        <h3 class="font-semibold mb-2">Status Descriptions:</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
          <div>
            <p><b>Pending</b></p>
            <p class="text-sm text-base-content/70">Account has incoming transactions, waiting to clear</p>
          </div>
          <div>
            <p><b>Unpend</b></p>
            <p class="text-sm text-base-content/70">Account is ready for transactions</p>
          </div>
          <div>
            <p><b>Sold</b></p>
            <p class="text-sm text-base-content/70">Account has been sold successfully</p>
          </div>
          <div>
            <p><b>Retrieved</b></p>
            <p class="text-sm text-base-content/70">Account has been retrieved or reclaimed</p>
          </div>
        </div>
      </div>

      <div class="bg-base-100 p-6 rounded-xl shadow border border-base-300">
        <h2 class="font-semibold mb-4">Bulk Operations</h2>
        <p class="mb-2">Efficiently manage multiple accounts</p>
        <h3 class="font-semibold mb-2">Selection Methods:</h3>
        <ul class="list-disc list-inside space-y-1 mb-4">
          <li>Click checkboxes to select individual accounts</li>
          <li>Use "Select All" checkbox in table header</li>
          <li>Selected accounts are highlighted</li>
        </ul>
        <h3 class="font-semibold mb-2">Available Operations:</h3>
        <ul class="list-disc list-inside space-y-1">
          <li>Bulk status updates (change status for all selected)</li>
          <li>Bulk delete (remove multiple accounts)</li>
          <li>Selection count display</li>
        </ul>
      </div>
    </div>

    <!-- FAQ (hidden initially) -->
    <div id="content-faq" class="tab-content hidden">
      <div class="bg-base-100 p-6 rounded-xl shadow border">
        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" checked="checked" />
          <div class="collapse-title text-xl font-medium">
            How do I add multiple accounts at once?
          </div>
          <div class="collapse-content">
            <p>Navigate to "Add Account" and paste multiple Roblox cookies in the textarea, one per line. The system
              will automatically process each cookie and create accounts with proper naming and type detection.</p>
          </div>
        </div>

        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" />
          <div class="collapse-title text-xl font-medium">
            What's the difference between Pending and Fastflip accounts?
          </div>
          <div class="collapse-content">
            <p>Pending accounts have incoming Robux transactions and need time to clear before use. Fastflip accounts
              don't have pending transactions and can be used immediately for transactions.</p>
          </div>
        </div>

        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" />
          <div class="collapse-title text-xl font-medium">
            How does automatic status changing work?
          </div>
          <div class="collapse-content">
            <p>Set an "Unpend date" for Pending accounts. The system checks every minute and automatically changes the
              status from "Pending" to "Unpend" when the date is reached.</p>
          </div>
        </div>

        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" />
          <div class="collapse-title text-xl font-medium">
            Can I edit account information after adding?
          </div>
          <div class="collapse-content">
            <p>Yes! You can use inline editing by clicking on any editable field in the table, or use the edit dialog
              for more comprehensive changes. Cost rates are automatically recalculated.</p>
          </div>
        </div>

        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" />
          <div class="collapse-title text-xl font-medium">
            Is my data secure?
          </div>
          <div class="collapse-content">
            <p>All data is stored locally in your browser. Nothing is sent to external servers. Your authentication
              cookies and account information remain on your device only.</p>
          </div>
        </div>

        <div class="collapse collapse-arrow bg-base-200 mb-2">
          <input type="radio" name="my-accordion-4" />
          <div class="collapse-title text-xl font-medium">
            Can I install this as an app?
          </div>
          <div class="collapse-content">
            <p>Yes! RBXAMS is a Progressive Web App (PWA). You can install it on your desktop or mobile device for a
              native app-like experience. Look for the install banner or use your browser's install option.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
      tab.addEventListener('click', function () {
        tabs.forEach(t => t.classList.remove('tab-active'));
        this.classList.add('tab-active');

        tabContents.forEach(content => {
          content.classList.remove('block');
          content.classList.add('hidden');
        });

        const targetId = this.id.replace('tab-', 'content-');
        const targetContent = document.getElementById(targetId);
        targetContent.classList.remove('hidden');
        targetContent.classList.add('block');
      });
    });
  });
</script>