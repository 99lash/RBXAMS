<?php
use App\Utils\Flash;
// var_dump(Flash::has('error'));
?>

<div class="flex w-full items-center justify-center min-h-screen bg-base-100 dark:bg-base-200">
  <!-- Theme toggle -->
  <div class="absolute top-5 right-5">
    <div class="dropdown dropdown-end">
      <label tabindex="0" class="btn btn-sm bg-base-500 border-1 border-base-300">
        <i id="theme-icon" data-lucide="sun" class="w-4 h-4"></i>
      </label>
      <ul tabindex="0" class="menu menu-sm dropdown-content mt-2 z-[1] p-2 shadow bg-base-300 rounded-box w-40">
        <li class="theme-option" data-name="light">
          <a">Light</a>
        </li>
        <li class="theme-option" data-name="dark">
          <a">Dark</a>
        </li>
        <li class="theme-option" data-name="system">
          <a">System</a>
        </li>
      </ul>
    </div>
  </div>

  <!-- Form -->
  <div class="max-w-md w-full px-6">
    <h2 class="text-2xl font-bold text-center mb-2">Welcome Back</h2>
    <p class="text-center text-gray-500 mb-6">Sign in to access your account dashboard</p>
    <?php if (Flash::has('error')): ?>
      <div role="alert" class="alert alert-error alert-soft mb-6">
        <span><?= Flash::get('error') ?></span>
      </div>
    <?php endif; ?>
    <form action="/login" method="POST" class="space-y-4">
      <div class="form-control">
        <label class="label"><span class="label-text">Username or Email Address</span></label>
        <input type="text" name="nameOrEmail" class="input input-bordered w-full" placeholder="your.email@example.com"
          required>
      </div>
      <div class="form-control">
        <label class="label"><span class="label-text">Password</span></label>
        <div class="relative">
          <input type="password" name="password" id="password" class="input input-bordered w-full pr-10"
            placeholder="Enter your password" required>
          <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 px-3 flex items-center"
            style="z-index: 10;">
            <i id="toggleIcon" data-lucide="eye" class="w-5 h-5 text-gray-400"></i>
          </button>
        </div>
        <label class="label">
          <a href="/forgot-password" class="label-text-alt link link-hover">Forgot password?</a>
        </label>
      </div>
      <button type="submit" class="btn btn-primary w-full">Sign In</button>
    </form>
    </p>
  </div>
</div>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
</script>
<script src="/scripts/auth.js"></script>