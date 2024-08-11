<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Staff') {
  header('Location: ../login.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff | Dashboard</title>
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
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-100 bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-gray-900 text-2xl text-white"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="Produk.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="cube" class="text-2xl"></ion-icon>
            <span class=" flex-1 ms-3 whitespace-nowrap">Produk</span>
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

    <h3 class="text-md text-gray-500">Selamat Datang, <strong><?= $username ?>, Sebagai <?= $role ?> Barang</strong></h3>
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
              <span class="text-md text-blue-400">Total Stok Produk</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-stok">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-stok">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Stok Produk</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="git-merge-sharp" class="rounded-full p-2 bg-blue-400 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium">100 Stok</span>
            </div>
          </div>
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
            <div class="flex flex-row justify-between items-center">
              <span class="text-md text-orange-800">Total Produk</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-produk">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-produk">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Produk</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="cube" class="rounded-full p-2 bg-orange-800 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium">200 Produk</span>
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
          <div class="flex flex-row items-center gap-5">
            <span class="text-yellow-500">1.</span><img src="https://cdn.onemars.net/sites/whiskas_id_xGoUJ_mwh5/image/mockup_wks_pouch_ad_mackerel_new-look_-80g_f_1705068811793_1705678005614_1709124356942.png" alt="produk-terlaris1" width="100">
            <div class="flex flex-col items-start">
              <span class="text-xl text-gray-500 font-medium">Whiskas Wadidaw</span>
              <span class="text-md text-gray-400">Terjual: 20</span>
              <span class="text-md text-gray-400">Stok Tersisa: 20</span>
            </div>
          </div>
          <br>
          <div class="flex flex-row items-center gap-5">
            <span>2.</span><img src="https://cdn.onemars.net/sites/whiskas_id_xGoUJ_mwh5/image/mockup_wks_pouch_ad_mackerel_new-look_-80g_f_1705068811793_1705678005614_1709124356942.png" alt="produk-terlaris1" width="100">
            <div class="flex flex-col items-start">
              <span class="text-xl text-gray-500 font-medium">Whiskas Wadidaw</span>
              <span class="text-md text-gray-400">Terjual: 20</span>
              <span class="text-md text-gray-400">Stok Tersisa: 20</span>
            </div>
          </div>
          <br>
          <div class="flex flex-row items-center gap-5">
            <span>3.</span><img src="https://cdn.onemars.net/sites/whiskas_id_xGoUJ_mwh5/image/mockup_wks_pouch_ad_mackerel_new-look_-80g_f_1705068811793_1705678005614_1709124356942.png" alt="produk-terlaris1" width="100">
            <div class="flex flex-col items-start">
              <span class="text-xl text-gray-500 font-medium">Whiskas Wadidaw</span>
              <span class="text-md text-gray-400">Terjual: 20</span>
              <span class="text-md text-gray-400">Stok Tersisa: 20</span>
            </div>
          </div>
          <br>
          <div class="flex flex-row items-center gap-5">
            <span>4.</span><img src="https://cdn.onemars.net/sites/whiskas_id_xGoUJ_mwh5/image/mockup_wks_pouch_ad_mackerel_new-look_-80g_f_1705068811793_1705678005614_1709124356942.png" alt="produk-terlaris1" width="100">
            <div class="flex flex-col items-start">
              <span class="text-xl text-gray-500 font-medium">Whiskas Wadidaw</span>
              <span class="text-md text-gray-400">Terjual: 20</span>
              <span class="text-md text-gray-400">Stok Tersisa: 20</span>
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