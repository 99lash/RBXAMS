<?php
$currentUser = '';
if (property_exists($this, 'currentUser')) {
  // echo 'Hello';
  $currentUser = $this->currentUser;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($title) ? $title : 'RBXAMS' ?> </title>
  <link rel="stylesheet" href="/css/styles.css">
  <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
</head>

<body>

  <div class="min-h-screen flex w-full" id="app">
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
      <main class="min-h-screen">
        <?php
        if (isset($page)) {
          switch ($page) {
            case '/':
              require 'Pages/Home.php';
              break;

            case '/accounts':
              require 'Pages/ManageAccount.php';
              break;

            case '/accounts/new':
              require 'Pages/newAccount.php';
              break;

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
      require_once 'Partials/Footer.php';
      ?>
    </div>
  </div>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="/scripts/index.js"></script>
</body>

</html>