<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../functions.php';
session_start();

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'Staff') {
  header('Location: ../../login.php');
  exit;
}

$id = intval($_GET["id"]); // Pastikan $id adalah integer untuk keamanan

$nama_barang = query("SELECT nama_barang FROM barang WHERE id = $id");
$barang = $nama_barang[0]['barang'] ?? 'Barang';

if (hapusBarang($id) > 0) {
  echo "
    <script>
        alert('$barang berhasil dihapus!');
        document.location.href = '../Produk.php';
    </script>
    ";
} else {
  echo "
    <script>
        alert('$barang gagal dihapus!');
        document.location.href = '../Produk.php';
    </script>
    ";
}
