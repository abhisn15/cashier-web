<?php
require '../../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'SuperAdmin') {
  header('Location: ../../login.php');
  exit();
}

// Mengambil id transaksi dari URL
$id_transaksi = isset($_GET['id']) ? $_GET['id'] : null;

// Jika id tidak ada, redirect ke halaman transaksi
if ($id_transaksi === null) {
  header('Location: ../Transaksi.php');
  exit();
}

// Query untuk mengambil detail transaksi berdasarkan id
$query = "SELECT transaksi.*, users.nama AS nama_kasir, detail_transaksi.kuantitas, barang.nama_barang, barang.harga 
          FROM transaksi 
          INNER JOIN users ON transaksi.id_kasir = users.id
          INNER JOIN detail_transaksi ON transaksi.id = detail_transaksi.id_transaksi
          INNER JOIN barang ON detail_transaksi.id_barang = barang.id
          WHERE transaksi.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  // Jika tidak ada data transaksi, redirect ke halaman transaksi
  header('Location: ../Transaksi.php');
  exit();
}

$transaksi = $result->fetch_all(MYSQLI_ASSOC);
$kembalian = $transaksi[0]['tunai'] - $transaksi[0]['total_harga'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pelanggan | Detail Transaksi</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="mx-10">
  <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-10 h-10" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
    </svg>
  </button>

  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-[90dvh] transition-transform -translate-x-full bg-white rounded-r-2xl shadow-xl sm:translate-x-0 border-r-2" aria-label="Sidebar">
    <div class="flex flex-row py-5 items-end pl-5">
      <span class="text-3xl font-bold text-orange-400">Bi</span>
      <span class="text-2xl font-medium">Kasir</span>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white rounded-b-2xl bg-gray-800 border-r-2">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="../Dashboard.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-2xl"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="../Produk.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="cube" class="text-2xl"></ion-icon>
            <span class=" flex-1 ms-3 whitespace-nowrap">Produk</span>
          </a>
        </li>
        <li>
          <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-orange-400 hover:text-white" onclick="location.href = '../Users.php'">
            <ion-icon name="people-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Users</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
            </svg>
          </button>
        </li>
        <li>
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-400 group">
            <ion-icon name="wallet-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Transaksi</span>
          </a>
        </li>
        <li>
          <a href="../../logout.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:text-white hover:bg-red-600 group">
            <ion-icon name="log-out-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <div class="sm:pl-8 py-5 sm:ml-64 sm:mr-10">
    <h1 class="text-2xl">Detail Transaksi</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="../Transaksi.php" class="text-gray-500">Transaksi</a>
      <span class="text-gray-500">/</span>
      <a href="#" class="text-orange-500">Detail Transaksi</a>
    </div>
    <br>
    <br>
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <button onclick="location.href = 'Cetak.php'" class="relative overflow-hidden text-white py-3 px-6 font-semibold rounded-3xl shadow-xl transform transition-all duration-500">
        <span class="absolute top-0 left-0 w-full h-full bg-orange-400"></span>
        <div class="flex flex-row items-center gap-2">
          <ion-icon name="receipt-sharp" class="text-sm sm:text-2xl text-white font-extrabold"></ion-icon>
          <span class="relative z-10 text-[12px] sm:text-sm">Cetak Detail Transaksi</span>
        </div>
      </button>
    </div>
    <br>
    <br>
    <div class="w-full flex justify-center">
      <div class="flex flex-col items-start bg-white py-8 px-5 shadow-md">
        <div class="w-full flex flex-row py-5 items-end justify-center pl-5">
          <span class="text-3xl font-bold text-gray-400">Bi</span>
          <span class="text-2xl font-medium text-gray-400"><strong>Shop</strong></span>
        </div>
        <div class="w-full flex flex-col items-center justify-center">
          <div class="text-gray-400 border-dashed border-t-2 border-b-2 gap-6 w-[100%] py-4 px-6 flex flex-row items-center justify-around">
            <span><?= $transaksi[0]['tanggal_transaksi'] ?></span>
            <span><?= $transaksi[0]['id'] ?>/<?= $transaksi[0]['nama_kasir'] ?></span>
          </div>
          <div class="flex flex-col items-start gap-4 py-4 w-full">
            <?php foreach ($transaksi as $detail) : ?>
              <div class="text-gray-400 flex flex-row justify-between items-start gap-4 text-[14px]">
                <span class="flex-grow w-60"><?= $detail['nama_barang'] ?></span>
                <span style="width: 30px; text-align: center;" class="break-words"><?= $detail['kuantitas'] ?></span>
                <span style="width: 30px; text-align: center;">x</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp <?= number_format($detail['harga'], 0, ',', '.') ?></span>
                <span style="width: 30px; text-align: center;">=</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp <?= number_format($detail['kuantitas'] * $detail['harga'], 0, ',', '.') ?></span>
              </div>
              <div class="flex justify-end w-full">
                <div class="w-60 border-dashed border-t-2"></div>
              </div>
            <?php endforeach; ?>
            <div class="w-full flex flex-col justify-end text-gray-400">
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Total</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp <?= number_format(array_sum(array_column($transaksi, 'total_harga')), 0, ',', '.') ?></span>
              </div>
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Tunai</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp <?= number_format($transaksi[0]['tunai'], 0, ',', '.') ?></span>
              </div>
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Kembalian</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
              </div>

              <div class="w-full border-dashed border-2"></div>

              <span class="text-center w-full text-gray-400">TERIMAKASIH. SELAMAT BELANJA KEMBALI</span>

              <div class="w-full flex flex-row items-center justify-center text-gray-400 gap-2">
                <span>=====</span>
                <span>LAYANAN KONSUMEN BIKASIR</span>
                <span>=====</span>
              </div>

              <span class="text-center w-full text-gray-400">EMAIL : KONTAK@BISHOP.CO.ID</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>
</body>

</html>