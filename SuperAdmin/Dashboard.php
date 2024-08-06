<?php
session_start();

$username = $_SESSION['nama'];

if (!isset($_SESSION['login']) || $_SESSION !== true || isset($_SESSION['SuperAdmin'])) {
  header('Location: ../login.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SuperAdmin | Dashboard</title>
</head>

<body>

</body>

</html>