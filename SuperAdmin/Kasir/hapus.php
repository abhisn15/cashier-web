<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../functions.php';
session_start();
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'SuperAdmin') {
  header('Location: ../../login.php');
  exit();
}

$id = intval($_GET["id"]); // Pastikan $id adalah integer untuk keamanan

$nama_user = query("SELECT nama FROM users WHERE id = $id");
$nama = $nama_user[0]['nama'] ?? 'User';

if (hapus($id) > 0) {
  echo "
    <script>
        alert('User dengan nama $nama berhasil dihapus!');
        document.location.href = '../Kasir.php';
    </script>
    ";
} else {
  echo "
    <script>
        alert('User dengan nama $nama gagal dihapus!');
        document.location.href = '../Kasir.php';
    </script>
    ";
}
