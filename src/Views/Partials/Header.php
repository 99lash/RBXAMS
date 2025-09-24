<?php if ($page != '/login'): ?>
  <header class="z-30 bg-base-100 border-b border-base-300 flex justify-between items-center px-6 py-3">
    <!-- Left: Page Title -->
    <div class="flex items-center gap-2">
      <h1 class="font-semibold text-lg">
        <?php echo isset($title) ? $title : 'Dashboard'; ?>
      </h1>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-4">
      <!-- Install App button -->
      <button class="btn btn-sm btn-outline flex items-center gap-1">
        <i data-lucide="download" class="w-4 h-4"></i>
        Install App
      </button>

      <!-- Theme toggle -->
      <button class="btn btn-sm btn-ghost"
        onclick="document.documentElement.dataset.theme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark'">
        <i data-lucide="moon" class="w-5 h-5"></i>
      </button>
    </div>
  </header>
<?php endif; ?>