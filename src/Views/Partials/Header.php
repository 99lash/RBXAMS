<?php if ($page != '/login'): ?>
  <header class="border-b border-base-300 flex justify-between items-center px-4 py-3 sticky top-0 z-[1000] bg-base-100">
    <?php if ($page != '/404' && $page != '/404'): ?>
      <!-- Left: Sidebar toggle and breadcrumbs -->
      <div class="flex items-center gap-2">
        <button id="toggle-sidebar" class="btn btn-sm btn-ghost">
          <i data-lucide="panel-right" class="w-4 h-4"></i>
        </button>
        <nav class="breadcrumbs">
          <ul class="flex items-center gap-1 text-sm text-gray-700 dark:text-gray-400">
            <li>
              <a class="link">RBXAMS</a>
            </li>
            <?php if (isset($nav) && isset($page)): ?>
              <li>
                <a class="link" href="<?= $page ?>">
                  <?= $nav ?>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </nav>
      </div>
    <?php else: ?>
      <!-- Branding -->
      <div class="p-4 flex items-center gap-2">
        <i data-lucide="line-chart" class="w-6 h-6 text-primary"></i>
        <span class="font-bold text-lg sidebar-text">RBXAMS</span>
      </div>
    <?php endif; ?>

    <!-- Right: Actions -->
    <div class="flex items-center gap-4">
      <!-- Theme toggle -->
      <div class="dropdown dropdown-end">
        <label tabindex="0" class="btn btn-sm btn-ghost">
          <i data-lucide="sun" class="w-4 h-4"></i>
        </label>
        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-200 rounded-box w-40">
          <li class="theme-option" data-name="light">
            <a>Light</a>
          </li>
          <li class="theme-option" data-name="dark">
            <a>Dark</a>
          </li>
          <li class="theme-option" data-name="system">
            <a>System</a>
          </li>
        </ul>
      </div>
    </div>
  </header>
<?php endif; ?>