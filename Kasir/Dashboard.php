<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];
$id_kasir = $_SESSION['id'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Kasir') {
  header('Location: ../login.php');
  exit();
}

// Query untuk total transaksi per kasir (berdasarkan id dari session)
$query_transaksi = "SELECT COUNT(t.id) AS total_transaksi, SUM(t.total_harga) AS total_pendapatan 
                    FROM transaksi t
                    WHERE t.id_kasir = '$id_kasir'";
$result_transaksi = mysqli_query($conn, $query_transaksi);
$total_transaksi_data = mysqli_fetch_assoc($result_transaksi);

// Query untuk 3 produk terlaris
$query_produk_terlaris = "SELECT b.nama_barang, SUM(dt.kuantitas) AS total_terjual
                          FROM detail_transaksi dt
                          JOIN barang b ON dt.id_barang = b.id
                          GROUP BY b.id
                          ORDER BY total_terjual DESC
                          LIMIT 3";
$result_produk_terlaris = mysqli_query($conn, $query_produk_terlaris);

checkSessionValidity($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir | Dashboard</title>
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
    <div class="flex flex-row py-5 items-end pl-5 w-full">
      <span class="text-3xl font-bold text-orange-400">Bi</span>
      <span class="text-2xl font-medium">Kasir</span>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white rounded-2xl bg-gray-800 border-r-2">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-100 bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-gray-900 text-2xl text-white"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="Kasir.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <svg width="26px" height="26px" class="fill-black group-hover:fill-white " viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22,5H2A1,1,0,0,0,1,6v4a3,3,0,0,0,2,2.82V22a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V12.82A3,3,0,0,0,23,10V6A1,1,0,0,0,22,5ZM15,7h2v3a1,1,0,0,1-2,0ZM11,7h2v3a1,1,0,0,1-2,0ZM7,7H9v3a1,1,0,0,1-2,0ZM4,11a1,1,0,0,1-1-1V7H5v3A1,1,0,0,1,4,11ZM14,21H10V19a2,2,0,0,1,4,0Zm5,0H16V19a4,4,0,0,0-8,0v2H5V12.82a3.17,3.17,0,0,0,1-.6,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3.17,3.17,0,0,0,1,.6Zm2-11a1,1,0,0,1-2,0V7h2ZM4.3,3H20a1,1,0,0,0,0-2H4.3a1,1,0,0,0,0,2Z" />
            </svg>
            <span class=" flex-1 ms-3 whitespace-nowrap">Kasir</span>
          </a>
        </li>
        <li>
          <a href="Transaksi.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="card-sharp" class="text-2xl"></ion-icon>
            <span class=" flex-1 ms-3 whitespace-nowrap">Transaksi</span>
          </a>
        </li>
        <li>
          <a href="../logout.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:text-white hover:bg-red-600 group">
            <ion-icon name="log-out-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Logout</span>
          </a>
        </li>
      </ul>
    </div>
  </aside>

  <div class="sm:pl-8 py-5 sm:ml-64 sm:mr-10">

    <h3 class="text-md text-gray-500">Selamat Datang, <strong><?= $username ?>, Sebagai <?= $role ?></strong></h3>
    <br>
    <h1 class="text-2xl">Dashboard</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Dashboard</a>
      <span class="text-gray-500">/</span>
    </div>
    <br>
    <div class="flex flex-col min-[1400px]:flex-row gap-10 justify-center">
      <div class="flex flex-col items-center gap-10 w-full">
        <div class="flex flex-wrap items-center justify-center xl:justify-start gap-10 w-full">
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
            <div class="flex flex-row justify-between items-center">
              <span class="text-md text-green-600">Total Transaksi</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex" aria-expanded="false" data-dropdown-toggle="dropdown-transaksi">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-transaksi">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Transaksi</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="card-sharp" class="rounded-full p-2 bg-green-600 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium"><?= $total_transaksi_data['total_transaksi'] ?> Transaksi</span>
            </div>
          </div>

        </div>
        <div class=" w-full py-4 px-6 bg-white rounded-xl shadow-xl w-full border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
          <div class="flex flex-row justify-between items-center">
            <span class="text-md text-yellow-500">Produk Terlaris</span>
            <div class="flex items-center">
              <div class="flex items-center ms-3">
                <div>
                  <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-terlaris">
                    <span class="sr-only opacity-0">Open user menu</span>
                    <span class="text-gray-300 font-extrabold">...</span>
                  </button>
                </div>
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-terlaris">
                  <ul class="py-1" role="none">
                    <li>
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Produk Terlaris</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <br>
          <?php $index = 1;
          while ($produk = mysqli_fetch_assoc($result_produk_terlaris)) { ?>
            <div class="flex flex-row items-center gap-5">
              <span class="text-yellow-500 text-xl"><?= $index ?>.</span>
              <div class="flex flex-col items-start">
                <span class="text-xl text-gray-500 font-medium"><?= $produk['nama_barang'] ?></span>
                <span class="text-md text-gray-400">Terjual: <?= $produk['total_terjual'] ?> unit</span>
              </div>
            </div>
          <?php $index++;
          } ?>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho <?= date('Y') ?></span>
  </footer>
</body>

</html>