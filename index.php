<?php
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
  header('Location: login.php');
  exit;
}

$role = $_SESSION['role'];

if ($role == 'User') {
  header('Location: Pelanggan/Dashboard.php');
} elseif ($role == 'Kasir') {
  header('Location: Kasir/Dashboard.php');
} elseif ($role == 'Staff') {
  header('Location: Staff/Dashboard.php');
} elseif ($role == 'SuperAdmin') {
  header('Location: SuperAdmin/Dashboard.php');
}
