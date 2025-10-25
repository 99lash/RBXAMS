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
<?php elseif ($page != '/404' && $page != '/500'): ?>
  <!-- Default sidebar -->
  <aside id="sidebar" class="group sidebar bg-base-100 dark:bg-base-200 w-64 h-full flex flex-col justify-between
    border-r border-base-300 transition-all duration-300 overflow-hidden">

    <div>
      <!-- Branding -->
      <div class="p-4 flex items-center gap-2">
        <i data-lucide="line-chart" class="w-6 h-6 text-primary"></i>
        <span class="font-bold text-lg sidebar-text">RBXAMS</span>
      </div>

      <!-- Navigation -->
      <nav class="mt-4 flex flex-col align-center justify-between">
        <div class="text-xs font-bold ps-5 sidebar-text">Navigation</div>
        <ul class="menu p-2 min-w-full">
          <li>
            <a href="/" class="flex items-center gap-2">
              <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
              <span class="sidebar-text">Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/accounts" class="flex items-center gap-2">
              <i data-lucide="users" class="w-4 h-4"></i>
              <span class="sidebar-text">Accounts</span>
            </a>
          </li>
          <li>
            <a href="/summary" class="flex items-center gap-2">
              <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
              <span class="sidebar-text">Daily Summary</span>
            </a>
          </li>
          <li>
            <a href="/guide" class="flex items-center gap-2">
              <i data-lucide="book-open" class="w-4 h-4"></i>
              <span class="sidebar-text">Guide</span>
            </a>
          </li>
        </ul>
      </nav>
    </div>

    <!-- User info dropdown-->
    <div id="user-info-root" class="relative w-full p-4">
      <button id="userMenuBtn" class="btn btn-ghost normal-case flex justify-start items-center gap-2 w-full px-4 py-8 rounded-full bg-base-300
           group-[.collapsed]:justify-center
           group-[.collapsed]:rounded-none
           group-[.collapsed]:bg-transparent
           group-[.collapsed]:border-none
           group-[.collapsed]:shadow-none"
           aria-expanded="false" aria-controls="userMenu">
        <div class="avatar placeholder">
          <div class="bg-neutral text-neutral-content rounded-full w-8 flex items-center justify-center">
            <span class="text-sm">ASH</span>
          </div>
        </div>
        <div class="sidebar-text flex-grow text-left group-[.collapsed]:hidden">
          <p class="text-xs base-content"><?= $currentUser['name'] ?? '' ?></p>
          <p class="text-xs text-gray-500 dark:text-gray-400"><?= $currentUser['email'] ?? '' ?></p>
        </div>
      </button>

      <!-- Hidden template for the dropdown content. We'll portal this into body on open -->
      <template id="userMenuTemplate">
        <ul id="userMenu" class="menu p-2 shadow border border-base-300 bg-base-100/95 rounded-box z-[2000] w-60"
          role="menu" style="position:fixed; display:block; visibility:hidden;">
          <li><a href="#" role="menuitem"><i data-lucide="bell" class="w-4 h-4 inline-block mr-2"></i>Notifications</a>
          </li>
          <li>
            <form method="POST" action="/logout" class="w-full flex">
              <button type="submit" class="flex flex-1 items-center gap-2" role="menuitem">
                <i data-lucide="log-out" class="w-4 h-4 inline-block mr-2"></i>Logout
              </button>
            </form>
          </li>
        </ul>
      </template>
    </div>

    <!-- <script>
     
    </script> -->

  </aside>
<?php endif; ?>