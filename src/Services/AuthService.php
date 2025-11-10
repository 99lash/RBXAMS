<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Security\SessionManager;
use App\Config\Database;

class AuthService
{
  private UserRepository $userRepository;
  private \mysqli $db;

  public function __construct()
  {
    $this->userRepository = new UserRepository();
    $this->db = (new Database())->getConnection();
  }

  public function login($nameOrEmail, $password): bool
  {
    $user = $this->userRepository->findByNameOrEmail($nameOrEmail);
    if (!$user || !password_verify($password, $user->getPassword())) {
      return false;
    }
    $_SESSION['user'] = [
      'id' => $user->getId(),
      'name' => $user->getName(),
      'email' => $user->getEmail(),
      'role' => $user->getUserRoleId(),
      'profilePicUrl' => $user->getProfilePicUrl()
    ];
    return true;
  }

  public function logout()
  {
    SessionManager::destroy();
  }

  public function currentUser(): array
  {
    return $_SESSION['user'] ?? null;
  }

  public function checkAuth(): bool
  {
    return isset($_SESSION['user']);
  }

  public function sendPasswordResetLink(string $email): array
  {
    $user = $this->userRepository->findByEmail($email);

    if (!$user) {
      // For security, always return a success message even if the email doesn't exist.
      return ['success' => true, 'message' => 'If an account with that email exists, a password reset link has been sent.'];
    }

    // Generate a unique token
    $token = bin2hex(random_bytes(32));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Store token in database
    $stmt = $this->db->prepare("INSERT INTO password_resets (email, token, created_at, expires_at) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sss", $email, $token, $expiresAt);
    $stmt->execute();

    // Send email
    // BASE_URL needs to be defined, e.g., in a config file
    $resetLink = BASE_URL . '/reset-password?token=' . $token;
    $subject = 'Password Reset Request';

    // Start output buffering
    ob_start();
    // Include the email template, passing variables to it
    $name = $user->getName();
    require __DIR__ . '/../Views/Emails/PasswordReset.php';
    // Get the content of the buffer and clean it
    $body = ob_get_clean();

    $mailService = new MailService(); // This service needs to be created
    $mailSent = $mailService->sendMail($email, $user->getName(), $subject, $body);

    if ($mailSent) {
      return ['success' => true, 'message' => 'Password reset link sent to your email.'];
    } else {
      return ['success' => false, 'message' => 'Failed to send password reset email. Please try again later.'];
    }
  }

  public function validateResetToken(string $token): bool
  {
    $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
  }

  public function resetPassword(string $token, string $newPassword): array
  {
    $stmt = $this->db->prepare("SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $resetRequest = $result->fetch_assoc();

    if (!$resetRequest) {
      return ['success' => false, 'message' => 'Invalid or expired password reset token.'];
    }

    $user = $this->userRepository->findByEmail($resetRequest['email']);

    if (!$user) {
      return ['success' => false, 'message' => 'User not found.'];
    }

    // Update user password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $this->userRepository->updatePassword($user->getId(), $hashedPassword);

    // Delete the used token
    $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    return ['success' => true, 'message' => 'Your password has been reset successfully. You can now log in with your new password.'];
  }
}