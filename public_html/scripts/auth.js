document.addEventListener('DOMContentLoaded', function () {
  const togglePassword = document.getElementById('togglePassword');
  const passwordInput = document.getElementById('password');

  if (togglePassword) {
    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);

      if (type === 'password') {
        togglePassword.innerHTML = '<i data-lucide="eye" class="w-5 h-5 text-gray-400"></i>';
      } else {
        togglePassword.innerHTML = '<i data-lucide="eye-off" class="w-5 h-5"></i>';
      }
      lucide.createIcons();
    });
  }
});