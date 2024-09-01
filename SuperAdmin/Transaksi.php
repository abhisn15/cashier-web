<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

$limit = 6;
$total_data = count(query("SELECT * FROM transaksi"));
// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Ambil halaman saat ini, default halaman 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Hitung offset
$offset = ($page - 1) * $limit;

// Cek apakah user sudah login dan memiliki role SuperAdmin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'SuperAdmin') {
  header('Location: ../../login.php');
  exit();
}

// Fungsi untuk mencari data transaksi berdasarkan keyword
function cari($keyword)
{
  global $conn;
  $keyword = "%$keyword%";
  $stmt = $conn->prepare("
        SELECT transaksi.*, 
               COALESCE(pelanggan.nama, 'Pelanggan belum terdaftar') AS nama_pelanggan,
               kasir.nama AS nama_kasir
        FROM transaksi 
        LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
        LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
        WHERE transaksi.id LIKE ? 
           OR pelanggan.nama LIKE ? 
           OR kasir.nama LIKE ?
    ");
  $stmt->bind_param('sss', $keyword, $keyword, $keyword);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

// Mengecek apakah form pencarian telah disubmit
if (isset($_POST["cari"])) {
  $keyword = trim($_POST["keyword"]);
  if (empty($keyword)) {
    // Jika keyword kosong, ambil semua data tanpa filter pencarian
    $transaksi = query("
            SELECT transaksi.*, 
                   transaksi.tanggal_transaksi, 
                   transaksi.total_harga, 
                   COALESCE(pelanggan.nama, 'Pelanggan belum terdaftar') AS nama_pelanggan, 
                   kasir.nama AS nama_kasir
            FROM transaksi 
            LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
            LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
            LIMIT $limit OFFSET $offset
        ");
    $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cari($keyword);
    $total = count($transaksi);
  }
} else {
  // Query untuk mengambil data tanpa pencarian
  $transaksi = query("
        SELECT transaksi.*, 
               transaksi.tanggal_transaksi, 
               transaksi.total_harga, 
               COALESCE(pelanggan.nama, 'Pelanggan belum terdaftar') AS nama_pelanggan, 
               kasir.nama AS nama_kasir
        FROM transaksi 
        LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
        LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
        LIMIT $limit OFFSET $offset
    ");
  $total = query("SELECT COUNT(*) AS total FROM transaksi")[0]['total'];
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SuperAdmin | Transaksi</title>
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
    <h1 class="text-2xl">Transaksi</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Transaksi</a>
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
    </div>
    <br>
    <br>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3">
              No
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Id Transaksi
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Pelanggan
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Kasir
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Tanggal Transaksi
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Total Harga
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Dibayar
            </th>
            <th scope="col" class="px-6 py-3 text-center">
              Detail
            </th>
          </tr>
        </thead>
        <?php $i = $page == 1 ? 1 : 7 + ($page - 2) * $limit; ?>
        <?php foreach ($transaksi as $t) : ?>
          <tbody>
            <tr class="bg-white border-b hover:bg-gray-50">
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                <?= $i++ ?>
              </th>
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                <?= $t['id'] ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= $t['nama_pelanggan'] ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= $t['nama_kasir'] ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= $t['tanggal_transaksi'] ?>
              </td>
              <td class="px-6 py-4 text-center w-40">
                <?php echo 'Rp ' . number_format($t['total_harga'], 0, ',', '.'); ?>
              </td>
              <td class="px-6 py-4 text-center w-40">
                <?php echo 'Rp ' . number_format($t['tunai'], 0, ',', '.'); ?>
              </td>
              <td class="px-6 py-4 text-blue-400 text-center">
                <a href="Transaksi/DetailTransaksi.php?id=<?= htmlspecialchars($t['id']) ?>" class="hover:underline">Detail Transaksi</a>
              </td>
            </tr>
          </tbody>
        <?php endforeach; ?>
      </table>
    </div>
    <div class="flex justify-center mt-5">
      <nav>
        <ul class="inline-flex -space-x-px">
          <?php if ($page > 1): ?>
            <li>
              <a href="?page=<?= $page - 1 ?>" class="px-3 py-2 ml-0 leading-tight !text-gray-500 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-700">Previous</a>
            </li>
          <?php endif; ?>

          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li>
              <a href="?page=<?= $i ?>" class="px-3 py-2 leading-tight <?= $i == $page ? 'bg-orange-400 text-white' : '!text-gray-500 bg-white' ?> border border-gray-300 hover:bg-gray-100 hover:text-gray-700"><?= $i ?></a>
            </li>
          <?php endfor; ?>


          <?php if ($page < $total_pages): ?>
            <li>
              <a href="?page=<?= $page + 1 ?>" class="px-3 py-2 leading-tight !text-gray-500 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-700">Next</a>
            </li>
          <?php endif; ?>
        </ul>
      </nav>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 pl-10 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>
</body>

</html>