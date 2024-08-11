<?php
require '../../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Kasir') {
  header('Location: ../../login.php');
  exit();
}


// Fungsi untuk mencari data pengguna berdasarkan keyword
function cari($keyword)
{
  global $conn;
  $stmt = $conn->prepare("SELECT transaksi.*, users.nama AS judul_buku
                FROM transaksi INNER JOIN users ON transaksi.tanggal_transaksi = transaksi.id LIKE ?");
  $search = "%$keyword%";
  $stmt->bind_param('s', $search);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

// Mengecek apakah form pencarian telah disubmit
if (isset($_POST["cari"])) {
  $keyword = trim($_POST["keyword"]);
  if (empty($keyword)) {
    // Jika keyword kosong, ambil semua data dengan pagination
    $transaksi = query("SELECT * FROM transaksi");
    $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cari($keyword);
    $total = count($transaksi);
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $transaksi = query("SELECT * FROM transaksi");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir | Detail Transaksi</title>
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

  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-[90dvh] transition-transform -translate-x-full bg-white rounded-2xl shadow-xl sm:translate-x-0 border-r-2" aria-label="Sidebar">
    <div class="flex flex-row py-5 items-end pl-5 w-full">
      <span class="text-3xl font-bold text-orange-400">Bi</span>
      <span class="text-2xl font-medium">Kasir</span>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white rounded-2xl bg-gray-800 border-r-2">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="../Dashboard.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-gray-900 group-hover:text-white text-2xl"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="../Toko.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <svg width="26px" height="26px" class="fill-gray-900 group-hover:fill-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22,5H2A1,1,0,0,0,1,6v4a3,3,0,0,0,2,2.82V22a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V12.82A3,3,0,0,0,23,10V6A1,1,0,0,0,22,5ZM15,7h2v3a1,1,0,0,1-2,0ZM11,7h2v3a1,1,0,0,1-2,0ZM7,7H9v3a1,1,0,0,1-2,0ZM4,11a1,1,0,0,1-1-1V7H5v3A1,1,0,0,1,4,11ZM14,21H10V19a2,2,0,0,1,4,0Zm5,0H16V19a4,4,0,0,0-8,0v2H5V12.82a3.17,3.17,0,0,0,1-.6,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3.17,3.17,0,0,0,1,.6Zm2-11a1,1,0,0,1-2,0V7h2ZM4.3,3H20a1,1,0,0,0,0-2H4.3a1,1,0,0,0,0,2Z" />
            </svg>
            <span class=" flex-1 ms-3 whitespace-nowrap">Toko</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-100 bg-orange-400 group">
            <ion-icon name="card-sharp" class="text-2xl"></ion-icon>
            <span class=" flex-1 ms-3 whitespace-nowrap">Transaksi</span>
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
    <h1 class="text-2xl">Transaksi</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="../Transaksi.php" class="text-gray-500">Transaksi</a>
      <span class="text-gray-500">/</span>
      <a href="#" class="text-orange-500">Detail Transaksi</a>
    </div>
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
          <span></span>
          <div class="text-gray-400 border-dashed border-t-2 border-b-2 gap-6 w-[100%] py-4 px-6 flex flex-row items-center justify-around">
            <span>21-08-2024 07:18</span>
            <span>1/Ghiffari</span>
          </div>
          <div class="flex flex-col items-start gap-4 py-4 w-full">
            <div class="text-gray-400 flex flex-row justify-between items-start gap-4 text-[14px]">
              <span class="flex-grow w-60">Chitato Sapi Panggang 68 Gram Makanan Ringan</span>
              <span style="width: 30px; text-align: center;" class="break-words">2</span>
              <span style="width: 30px; text-align: center;">x</span>
              <span class="text-end" style="width: 80px;" class="break-words">Rp 8.000</span>
              <span style="width: 30px; text-align: center;">=</span>
              <span class="text-end" style="width: 80px;" class="break-words">Rp 16.000</span>
            </div>
            <div class="flex justify-end w-full">
              <div class="w-60 border-dashed border-t-2"></div>
            </div>
            <div class="w-full flex flex-col justify-end text-gray-400">
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Total</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp 16.000</span>
              </div>
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Tunai</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp 20.000</span>
              </div>
              <div class="text-gray-400 flex flex-row justify-end items-start gap-4 text-[14px]">
                <span class="text-start" style="width: 80px;" class="break-words">Kembalian</span>
                <span style="width: 30px; text-align: center;">:</span>
                <span class="text-end" style="width: 80px;" class="break-words">Rp 4.000</span>
              </div>
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

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const cartCount = document.getElementById('cart-count');
      let itemCount = 0;

      // Event listener untuk setiap tombol "add-to-cart"
      document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', () => {
          itemCount++;
          cartCount.textContent = itemCount;
          cartCount.classList.remove('hidden'); // Tampilkan notifikasi jika sebelumnya tersembunyi
        });
      });
    });
  </script>
</body>

</html>