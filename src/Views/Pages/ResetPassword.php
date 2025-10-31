<?php
use App\Utils\Flash;

$token = $_GET['token'] ?? '';
?>

<div class="flex w-full items-center justify-center min-h-screen bg-base-100 dark:bg-base-200">
  <div class="max-w-md w-full px-6">
    <h2 class="text-2xl font-bold text-center mb-2">Reset Your Password</h2>
    <p class="text-center text-gray-500 mb-6">Enter your new password below.</p>

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

    <form action="/reset-password" method="POST" class="space-y-4">
      <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
      <div class="form-control">
        <label class="label"><span class="label-text">New Password</span></label>
        <input type="password" name="password" class="input input-bordered w-full" placeholder="Enter new password" required>
      </div>
      <div class="form-control">
        <label class="label"><span class="label-text">Confirm New Password</span></label>
        <input type="password" name="password_confirmation" class="input input-bordered w-full" placeholder="Confirm new password" required>
      </div>
      <button type="submit" class="btn btn-primary w-full">Reset Password</button>
    </form>
  </div>
</div>