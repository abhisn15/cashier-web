<?php
require './functions.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Pastikan token ada
if (!isset($_POST["token"])) {
  die("Token tidak ditemukan");
}

$token = $_POST["token"];
$token_hash = hash("sha256", $token);

global $conn;

// Query untuk mengambil user berdasarkan token hash
$stmt = $conn->prepare("SELECT * FROM users WHERE reset_token_hash = ?");
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
  die("Token tidak ditemukan");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
  die("Token sudah kedaluwarsa");
}

if (isset($_POST["password"])) {
  $password = $_POST["password"];
  $password_confirmation = $_POST["password_confirmation"];

  if (strlen($password) < 8) {
    die("Password harus lebih dari 8 karakter");
  }

  if (!preg_match("/[a-z]/i", $password)) {
    die("Password harus mengandung minimal satu huruf");
  }

  if (!preg_match("/[0-9]/", $password)) {
    die("Password harus mengandung minimal satu angka");
  }

  if ($password !== $password_confirmation) {
    die("Password dan konfirmasi password tidak cocok");
  }

  // Hash password baru
  $password_hash = password_hash($password, PASSWORD_DEFAULT);

  // Update password dan kosongkan token
  $sql = "UPDATE users
            SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL
            WHERE id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("ss", $password_hash, $user["id"]);
  $stmt->execute();

  echo "Password berhasil diperbarui. Anda sekarang dapat login.";
}
