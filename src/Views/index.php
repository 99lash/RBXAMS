<?php
$currentUser = '';
if (property_exists($this, 'currentUser')) {
  // echo 'Hello';
  $currentUser = $this->currentUser;
}
?>

<!DOCTYPE html>
<html lang="en" class="hidden">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? $title : 'RBXAMS' ?> </title>
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
  <link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css" rel="stylesheet">
  <script src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>
</head>

<script>
  (function () {
    const savedTheme = localStorage.getItem('theme');
    let theme = savedTheme;

    if (!theme || theme === 'system') {
      const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
      theme = prefersDark ? 'dark' : 'light';
    }
    document.documentElement.setAttribute('data-theme', theme);

    document.addEventListener('DOMContentLoaded', () => {
      const sidebar = document.getElementById('sidebar');
      if (localStorage.getItem('sidebar') === 'collapsed') {
        sidebar.classList.remove('w-64');
        sidebar.classList.add('w-16', 'collapsed');
      }
      document.documentElement.classList.remove('hidden');
    });
  })();
</script>

<body>

  <div class="min-h-[90vh] flex transition-all duration-300" id="app">
    <!-- Sidebar -->
    <?php
    require_once 'Partials/Sidebar.php';
    ?>
    <div class="flex-1" id="main-container">
      <!-- Header -->
      <?php
      require_once 'Partials/Header.php';
      ?>
      <!-- Main -->
      <main class="min-h-[90vh]">
        <?php
        if (isset($page)) {
          switch ($page) {
            case '/':
              require 'Pages/Home.php';
              break;

            case '/accounts':
              require 'Pages/ManageAccount.php';
              break;

            // case '/accounts/new':
            //   require 'Pages/newAccount.php';
            //   break;
        
            case '/summary':
              require 'Pages/DailySummary.php';
              break;

            case '/login':
              require 'Pages/Login.php';
              break;

            case '/guide':
              require 'Pages/Guide.php';
              break;

            // case '/register':
            //   require 'Pages/Register.php';
            //   break;
            default:
              var_dump($page);
              require 'Pages/404.php';
              break;
          }
        } else {
          throw new Exception('View not set');
        }
        ?>
      </main>

      <!-- Footer -->
      <?php
      //  require_once 'Partials/Footer.php';
      ?>
    </div>
  </div>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="/scripts/index.js"></script>
</body>

</html>