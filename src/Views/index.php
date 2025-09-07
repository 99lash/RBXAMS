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
  <?php
  require_once 'Partials/Header.php';

  if (isset($page)) {
    switch ($page) {
      case 'home':
        require 'Pages/Home.php';
        break;

      case 'user':
        require 'Pages/Users.php';
        break;

      default:
        require 'Pages/404.php';
        break;
    }
  } else {
    throw new Exception('View not set');
  }

  require_once 'Partials/Footer.php';
  ?>
  <script src="/scripts/index.js"></script>
</body>

</html>