<?php if ($page == '/login'): ?>
  <div class="hidden lg:flex w-1/2 flex-col justify-between 
              bg-gradient-to-b from-gray-100 to-gray-200 
              dark:from-gray-900 dark:to-gray-800 
              p-10 transition-colors duration-300">
    <!-- Branding -->
    <div>
      <h1 class="text-2xl font-bold flex items-center gap-2 text-gray-900 dark:text-gray-100">
        <i data-lucide="gamepad-2" class="w-6 h-6"></i>
        Roblox Asset Monitoring Platform
      </h1>
      <p class="text-sm text-gray-600 dark:text-gray-400">Account Profit Management</p>

      <!-- Features -->
      <div class="mt-10 space-y-6">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-300 dark:bg-gray-700">
            <i data-lucide="line-chart" class="w-5 h-5 text-gray-100"></i>
          </span>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Track Your Profits</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Monitor your ROBLOX account investments and returns in
              real-time</p>
          </div>
        </div>
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 flex items-center justify-center rounded-lg bg-gray-300 dark:bg-gray-700">
            <i data-lucide="shield-check" class="w-5 h-5 text-gray-100"></i>
          </span>
          <div>
            <h3 class="font-semibold text-gray-900 dark:text-gray-100">Secure & Reliable</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Your data is stored locally and securely managed</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Image -->
    <img src="/assets/coins.jpeg" alt="Coins" class="rounded-xl shadow-md">
  </div>
<?php else: ?>
  <!-- Default sidebar (kung hindi login page) -->
  <aside
    class="sidebar bg-base-200 w-64 h-screen flex flex-col justify-between fixed left-0 top-0 border-r border-base-300">
    <div>
      <!-- Branding -->
      <div class="p-4 flex items-center gap-2">
        <i data-lucide="line-chart" class="w-6 h-6 text-primary"></i>
        <span class="font-bold text-lg">RBXAMS</span>
      </div>

      <!-- Navigation -->
      <nav class="mt-4">
        <ul class="menu p-2">
          <li><a href="/" class="flex items-center gap-2"><i data-lucide="layout-dashboard" class="w-5 h-5"></i>
              Dashboard</a></li>
          <li><a href="/accounts" class="flex items-center gap-2"><i data-lucide="users" class="w-5 h-5"></i> Manage
              Accounts</a></li>
          <li><a href="/accounts/new" class="flex items-center gap-2"><i data-lucide="plus-circle" class="w-5 h-5"></i>
              Add Account</a></li>
          <li><a href="/summary" class="flex items-center gap-2"><i data-lucide="bar-chart-3" class="w-5 h-5"></i> Daily
              Summary</a></li>
          <li><a href="/guide" class="flex items-center gap-2"><i data-lucide="book-open" class="w-5 h-5"></i> Guide</a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- User info -->
    <div class="p-4 border-t border-base-300 flex items-center gap-2">
      <div class="avatar placeholder">
        <div class="bg-neutral text-neutral-content rounded-full w-10 flex">
          <span>AS</span>
        </div>
      </div>
      <div>
        <p class="font-medium"><?= $currentUser['name'] ?? '' ?></p>
        <p class="text-sm text-gray-500 dark:text-gray-400"><?= $currentUser['email'] ?? '' ?></p>
      </div>
      <form method="post" action="/logout">
        <button type="submit" class="btn">Logout</button>
      </form>
    </div>
  </aside>
<?php endif; ?>