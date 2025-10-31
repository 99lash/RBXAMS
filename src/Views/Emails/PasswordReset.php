
<?php

/**
 * @var string $name The user's name.
 * @var string $resetLink The password reset link.
 */

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Request</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .container {
      background-color: #ffffff;
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .header {
      text-align: center;
      padding-bottom: 20px;
    }

    .header h1 {
      color: #333333;
      margin: 0;
    }

    .content {
      color: #555555;
      line-height: 1.6;
    }

    .content p {
      margin: 0 0 20px;
    }

    .button {
      display: inline-block;
      background-color: #007bff;
      color: #ffffff;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 5px;
      font-weight: bold;
    }

    .footer {
      text-align: center;
      padding-top: 20px;
      font-size: 12px;
      color: #999999;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h1>RBXAMS</h1>
    </div>
    <div class="content">
      <p>Hello <?= htmlspecialchars($name) ?>,</p>
      <p>You are receiving this email because we received a password reset request for your account.</p>
      <p>Please click on the following button to reset your password:</p>
      <p style="text-align: center;">
        <a href="<?= htmlspecialchars($resetLink) ?>" class="button">Reset Password</a>
      </p>
      <p>This link will expire in 1 hour.</p>
      <p>If you did not request a password reset, no further action is required.</p>
    </div>
    <div class="footer">
      <p>&copy; <?= date('Y') ?> RBXAMS. All rights reserved.</p>
    </div>
  </div>
</body>

</html>
