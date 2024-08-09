<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || isset($_SESSION['SuperAdmin'])) {
  header('Location: ../login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SuperAdmin | Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body class="">
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
          <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-orange-400 hover:text-white" onclick="location.href = 'Users.php'">
            <ion-icon name="people-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Users</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
            </svg>
          </button>
        </li>
        <li>
          <a href="Transaksi.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:text-white hover:bg-orange-100 hover:bg-orange-400 group">
            <ion-icon name="wallet-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Transaksi</span>
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

    <h3 class="text-md text-gray-500">Selamat Datang, <strong><?= $username; ?>!</strong></h3>
    <br>
    <h1 class="text-2xl">Dashboard</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Dashboard</a>
      <span class="text-gray-500">/</span>
    </div>
    <br>
    <div class="flex flex-col min-[1400px]:flex-row gap-10">
      <div class="flex flex-col items-center gap-10">
        <div class="flex flex-wrap items-center justify-center xl:justify-start gap-10 w-full">
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full xl:w-[47%] border-orange-400 hover:scale-105 border-0 duration-200 hover:border-b-4 hover:border-r-4">
            <div class="flex flex-row justify-between items-center">
              <span class="text-md text-orange-400">Total Pelanggan Terdaftar</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-pelanggan">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-pelanggan">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Pelanggan</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="person-sharp" class="rounded-full p-2 bg-orange-400 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium">10 Pelanggan</span>
            </div>
          </div>
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full xl:w-[47%] border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
            <div class="flex flex-row justify-between items-center">
              <span class="text-md text-blue-400">Total Kasir</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-kasir">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-kasir">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Kasir</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="people-sharp" class="rounded-full p-2 bg-blue-400 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium">4 Kasir</span>
            </div>
          </div>
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full xl:w-[47%] border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
            <div class="flex flex-row justify-between items-center">
              <span class="text-md text-red-400">Total Staff</span>
              <div class="flex items-center">
                <div class="flex items-center ms-3">
                  <div>
                    <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-staff">
                      <span class="sr-only opacity-0">Open user menu</span>
                      <span class="text-gray-300 font-extrabold">...</span>
                    </button>
                  </div>
                  <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-staff">
                    <ul class="py-1" role="none">
                      <li>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Staff</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="flex flex-row items-center gap-5">
              <ion-icon name="people-sharp" class="rounded-full p-2 bg-red-400 text-white text-4xl"></ion-icon>
              <span class="text-xl text-gray-500 font-medium">2 Staff</span>
            </div>
          </div>
          <div class="py-4 px-6 bg-white rounded-xl shadow-xl w-full xl:w-[47%] border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
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
            <span class="text-md text-green-600">Total Transaksi</span>
            <div class="flex items-center">
              <div class="flex items-center ms-3">
                <div>
                  <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-transaksi">
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
            <ion-icon name="card-sharp" class="rounded-full p-4 bg-green-600/40 text-green-600 text-4xl"></ion-icon>
            <div class="flex flex-col items-start">
              <span class="text-xl text-gray-500 font-medium">40 Transaksi</span>
              <span class="text-md text-gray-400">Penghasilan: Rp 2.000.000</span>
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
      <div class="flex flex-col items-center gap-4 w-[100%] xl:w-auto">
        <div class="rounded-xl shadow-xl py-5 px-8 bg-white w-full xl:w-auto border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
          <div class="flex flex-row justify-between items-center">
            <h4 class="text-xl text-orange-400">Kasir Teraktif</h4>
            <div class="flex items-center">
              <div class="flex items-center ms-3">
                <div>
                  <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-kasir-teraktif">
                    <span class="sr-only opacity-0">Open user menu</span>
                    <span class="text-gray-300 font-extrabold">...</span>
                  </button>
                </div>
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-kasir-teraktif">
                  <ul class="py-1" role="none">
                    <li>
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Kasir Teraktif</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="flex flex-col items-center gap-3 w-full border-orange-400">
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">1.</span>
              <span class="flex-1 px-2">Alvaro Mayza</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 10</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">2.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 4</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">3.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 4</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">4.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">5.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">6.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">7.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">8.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">9.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">10.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 1</span>
            </div>
          </div>
        </div>
        <div class="rounded-xl shadow-xl py-5 px-8 bg-white w-full xl:w-auto border-orange-400 hover:scale-105 duration-200 hover:border-b-4 hover:border-r-4 border-0">
          <div class="flex flex-row justify-between items-center">
            <h4 class="text-xl text-orange-400">Pelanggan Teraktif</h4>
            <div class="flex items-center">
              <div class="flex items-center ms-3">
                <div>
                  <button type="button" class="flex " aria-expanded="false" data-dropdown-toggle="dropdown-pelanggan-teraktif">
                    <span class="sr-only opacity-0">Open user menu</span>
                    <span class="text-gray-300 font-extrabold">...</span>
                  </button>
                </div>
                <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow" id="dropdown-pelanggan-teraktif">
                  <ul class="py-1" role="none">
                    <li>
                      <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" role="menuitem">Detail Pelanggan Teraktif</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <br>
          <div class="flex flex-col items-center gap-3 w-full">
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">1.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 80</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">2.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 4</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">3.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 4</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">4.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">5.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">6.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 3</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">7.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">8.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">9.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 2</span>
            </div>
            <div class="flex justify-between w-full max-w-lg text-sm font-medium">
              <span class="text-right">10.</span>
              <span class="flex-1 px-2">Abhi Surya Nugroho</span>
              <span class="mr-20 lg:mr-auto">|</span>
              <span class="w-40 text-right">Total Transaksi: 1</span>
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