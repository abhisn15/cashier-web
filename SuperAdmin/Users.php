<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../functions.php';
session_start();

$username = $_SESSION['nama'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || isset($_SESSION['SuperAdmin'])) {
  header('Location: ../login.php');
  exit;
}

// Fungsi untuk mencari data pengguna berdasarkan keyword
function cari($keyword)
{
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM users WHERE nama LIKE ? AND role IN ('User', 'Kasir', 'Staff')");
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
    $user = query("SELECT * FROM users WHERE role IN ('User', 'Kasir', 'Staff')");
    $total = query("SELECT COUNT(*) AS total FROM users WHERE role IN ('User', 'Kasir', 'Staff')")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $user = cari($keyword);
    $total = count($user);
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $user = query("SELECT * FROM users WHERE role IN ('User', 'Kasir', 'Staff')");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM users WHERE role IN ('User', 'Kasir', 'Staff')")[0]['total'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SuperAdmin | Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
  <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-10 h-10 ml-5 sm:ml-0" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
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
          <button type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group bg-orange-400 text-white">
            <ion-icon name="people-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Users</span>
            <svg class="w-5 h-5" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="48" height="48" fill="white" fill-opacity="0.01" />
              <path d="M13 30L25 18L37 30" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <ul class=" py-2 space-y-2">
            <li>
              <a href="Pelanggan.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Pelanggan</a>
            </li>
            <li>
              <a href="Kasir.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Kasir</a>
            </li>
            <li>
              <a href="Staff.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Staff</a>
            </li>
          </ul>
        </li>
        <li>
          <a href="Transaksi.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:text-white hover:bg-orange-400 group">
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

  <div class="sm:pl-8 py-5 mx-10 sm:ml-64 sm:mr-10">
    <h1 class="text-2xl">Users</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Users</a>
      <span class="text-gray-500">/</span>
    </div>
    <br>
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <form method="post" class="relative">
        <input placeholder="Search..." class="input shadow-lg focus:border-2 border-gray-300 pl-10 pr-5 py-3 rounded-xl w-full sm:w-56 transition-all focus:w-64 outline-none" name="keyword" id="keyword" type="search" value="<?= htmlspecialchars($keyword ?? '') ?>" />
        <svg class="size-6 absolute top-3 left-3 text-gray-500" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" stroke-linejoin="round" stroke-linecap="round"></path>
        </svg>
        <button type="submit" name="cari" class="sr-only">Search</button>
      </form>
      <button onclick="location.href = './Users/tambah.php'" class="relative overflow-hidden text-white py-3 px-6 font-semibold rounded-3xl shadow-xl transform transition-all duration-500">
        <span class="absolute top-0 left-0 w-full h-full bg-orange-400"></span>
        <div class="flex flex-row items-center gap-2">
          <ion-icon name="add-sharp" class="text-sm sm:text-2xl text-white font-extrabold"></ion-icon>
          <span class="relative z-10 text-[12px] sm:text-sm">Tambah User</span>
        </div>
      </button>
    </div>
    <br>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg ">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3">No</th>
            <th scope="col" class="px-6 py-3">Nama Pengguna</th>
            <th scope="col" class="px-6 py-3 text-center">Role</th>
            <th scope="col" class="px-6 py-3 text-center">Email</th>
            <th scope="col" class="px-6 py-3 text-center">Nomor Handphone</th>
            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; ?>
          <?php foreach ($user as $row) : ?>
            <tr class="bg-white border-b hover:bg-gray-50">
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap"><?= $i ?></th>
              <td class="px-6 py-4 text-black"><?= htmlspecialchars($row['nama']) ?></td>
              <td class="px-6 py-4 text-center"><?= htmlspecialchars($row['role']) ?></td>
              <td class="px-6 py-4 text-center"><?= htmlspecialchars($row['email']) ?></td>
              <td class="px-6 py-4 text-center"><?= htmlspecialchars($row['no_hp']) ?></td>
              <td class="px-6 py-4 text-center">
                <a href="Users/edit.php?id=<?= htmlspecialchars($row['id']) ?>" class="font-medium text-blue-600 hover:underline">Edit</a>
                <span>|</span>
                <a onclick="return confirm('Apakah kamu yakin ingin menghapus user ini?');" href="./Users/hapus.php?id=<?= htmlspecialchars($row['id']) ?>" class="font-medium text-red-600 hover:underline">Hapus</a>
              </td>
            </tr>
            <?php $i++ ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  </div>

  <footer class="bg-white w-full sm:pl-8 pl-10 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <script src="../assets/js/script.js"></script>
</body>

</html>