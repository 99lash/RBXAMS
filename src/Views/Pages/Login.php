<?php
use App\Utils\Flash;
// var_dump(Flash::has('error'));
?>

<div class="flex w-full items-center justify-center bg-base-100 dark:bg-base-200 min-h-screen">
  <!-- Theme toggle -->
  <div class="absolute top-5 right-5">
    <div class="dropdown dropdown-end">
      <label tabindex="0" class="btn btn-sm btn-ghost">
        <i data-lucide="sun" class="w-5 h-5"></i>
      </label>
      <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-200 rounded-box w-40">
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
        <input type="password" name="password" class="input input-bordered w-full" placeholder="Enter your password"
          required>
      </div>
      <button type="submit" class="btn btn-primary w-full">Sign In</button>
    </form>
    <p class="mt-4 text-center text-sm">New to ROBLOX Tracker? <a href="/register" class="link">Create an account</a>
    </p>
  </div>
</div>