<?php
session_start();

$username = $_SESSION['nama'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || isset($_SESSION['User'])) {
  header('Location: ../login.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pelanggan | Dashboard</title>
</head>
<body>
  
</body>
</html>