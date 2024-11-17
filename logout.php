<?php
require 'functions.php'; // Ganti dengan path yang sesuai ke file functions.php

session_start();

// // Cek jika ada ID sesi pengguna di sesi
// if (isset($_SESSION["id"])) {
//   $id = $_SESSION["id"];

//   // Hapus session_id di database jika ada
//   $sql = "UPDATE users SET session_id = NULL WHERE id = ?";
//   if ($stmt = mysqli_prepare($conn, $sql)) {
//     mysqli_stmt_bind_param($stmt, "i", $id);
//     mysqli_stmt_execute($stmt);
//     mysqli_stmt_close($stmt);
//   }
// }

// Kosongkan array $_SESSION
$_SESSION = array();

// Hapus cookie sesi jika ada
if (ini_get("session.use_cookies")) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
  );
}

// Hancurkan sesi
session_destroy();

// Hapus cookie username
setcookie('username', '', time() - 3600, '/');

// Redirect ke halaman login
header('Location: login.php');
exit;
