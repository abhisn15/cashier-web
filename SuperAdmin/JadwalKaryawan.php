<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../functions.php';
session_start();

$username = $_SESSION['nama'];

$role = $_SESSION['role'];

// Cek apakah user sudah login dan memiliki role SuperAdmin
if (
  !isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'SuperAdmin'
) {
  header('Location: ../../login.php');
  exit();
}

$jadwalKaryawan = getJadwalKaryawan();

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
          <a href="Dashboard.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-2xl"></ion-icon>
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
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-400 group">
            <ion-icon name="time-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Jadwal Karyawan</span>
          </a>
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

  <div class="sm:pl-8 py-5 sm:ml-64 sm:mr-10 m-4">

    <h3 class="text-md text-gray-500">Selamat Datang, <strong><?= $username; ?>!</strong></h3>
    <br>
    <h1 class="text-2xl">Jadwal Karyawan</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Jadwal Karyawan</a>
      <span class="text-gray-500">/</span>
    </div>
    <br>
    <button onclick="location.href = './JadwalKaryawan/tambah.php'" class="relative overflow-hidden text-white py-3 px-6 font-semibold rounded-3xl shadow-xl transform transition-all duration-500">
      <span class="absolute top-0 left-0 w-full h-full bg-orange-400"></span>
      <div class="flex flex-row items-center gap-2">
        <ion-icon name="add-sharp" class="text-sm sm:text-2xl text-white font-extrabold"></ion-icon>
        <span class="relative z-10 text-[12px] sm:text-sm">Tambah Jadwal Karyawan</span>
      </div>
    </button>
    <br>
    <br>
    <table class="leading-normal w-full text-sm text-left rtl:text-right text-gray-500">
      <thead claas='text-xs text-gray-700 uppercase bg-gray-50'>
        <tr>
          <th scope="col" class="px-6 py-3">Nama Karyawan</th>
          <th scope="col" class="px-6 py-3">Tanggal Shift</th>
          <th scope="col" class="px-6 py-3">Jadwal Masuk</th>
          <th scope="col" class="px-6 py-3">Jadwal Keluar</th>
          <th scope="col" class="px-6 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $jadwalKaryawan = getJadwalKaryawan();
        foreach ($jadwalKaryawan as $jadwal) : ?>
          <tr>
            <td class="px-6 py-4 text-black"><?= $jadwal['nama_karyawan']; ?> (<?= $jadwal['role'] ?>)</td>
            <td class="px-8 py-4 text-black"><?= $jadwal['tanggal']; ?></td>
            <td class="px-10 py-4 text-black"><?= $jadwal['jam_masuk']; ?></td>
            <td class="px-12 py-4 text-black"><?= $jadwal['jam_keluar']; ?></td>
            <td>
              <a href="./JadwalKaryawan/edit.php?id=<?= $jadwal['id']; ?>" class="text-blue-500">Edit</a> |
              <a href="./JadwalKaryawan/hapus.php?id=<?= $jadwal['id']; ?>" onclick="return confirm(' Yakin ingin menghapus jadwal ini?');" class="text-red-500">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>
</body>

</html>