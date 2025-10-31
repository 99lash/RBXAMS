<?php
use App\Utils\Flash;
?>

<div class="flex w-full items-center justify-center min-h-screen bg-base-100 dark:bg-base-200">
  <div class="max-w-md w-full px-6">
    <h2 class="text-2xl font-bold text-center mb-2">Forgot Your Password?</h2>
    <p class="text-center text-gray-500 mb-6">Enter your email address and we'll send you a link to reset your password.</p>

    <?php if (Flash::has('error')): ?>
      <div role="alert" class="alert alert-error alert-soft mb-6">
        <span><?= Flash::get('error') ?></span>
      </div>
    <?php endif; ?>

    <?php if (Flash::has('success')): ?>
      <div role="alert" class="alert alert-success alert-soft mb-6">
        <span><?= Flash::get('success') ?></span>
      </div>
    <?php endif; ?>

    <form action="/forgot-password" method="POST" class="space-y-4">
      <div class="form-control">
        <label class="label"><span class="label-text">Email Address</span></label>
        <input type="email" name="email" class="input input-bordered w-full" placeholder="your.email@example.com" required>
      </div>
      <button type="submit" class="btn btn-primary w-full">Send Reset Link</button>
    </form>
    <div class="text-center mt-4">
      <a href="/login" class="link link-hover text-sm">Back to Login</a>
    </div>
  </div>
</div>