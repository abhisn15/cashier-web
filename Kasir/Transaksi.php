<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

// Pastikan pengguna sudah login dan memiliki role yang sesuai
if (
  !isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Kasir'
) {
  header('Location: ../login.php');
  exit();
}

date_default_timezone_set('Asia/Jakarta');

// Ambil ID kasir dari session
$id_kasir = $_SESSION['id'];

// Fungsi untuk mencari data transaksi berdasarkan keyword
function cari($keyword, $id_kasir)
{
  global $conn;
  $stmt = $conn->prepare("SELECT transaksi.*, 
                                 pelanggan.nama AS nama_pelanggan, 
                                 kasir.nama AS nama_kasir
                          FROM transaksi 
                          LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
                          LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
                          WHERE (transaksi.id LIKE ? 
                                 OR transaksi.id_kasir LIKE ?)
                            AND transaksi.id_kasir = ?");
  $search = "%$keyword%";
  $stmt->bind_param('ssi', $search, $search, $id_kasir);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_all(MYSQLI_ASSOC);
}

// Mengecek apakah form pencarian telah disubmit
if (isset($_POST["cari"])) {
  $keyword = trim($_POST["keyword"]);
  if (empty($keyword)) {
    // Jika keyword kosong, ambil semua data dengan filter ID kasir
    $transaksi = query("SELECT transaksi.*, 
                                transaksi.tanggal_transaksi, 
                                transaksi.total_harga, 
                                pelanggan.nama AS nama_pelanggan, 
                                kasir.nama AS nama_kasir
                         FROM transaksi 
                         LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
                         LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
                         WHERE transaksi.id_kasir = $id_kasir");

    $total = query("SELECT COUNT(*) AS total FROM transaksi WHERE id_kasir = $id_kasir")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $transaksi = cari($keyword, $id_kasir);
    $total = count($transaksi);
  }
} else {
  // Query untuk mengambil data sesuai ID kasir
  $transaksi = query("SELECT transaksi.*, 
                              transaksi.tanggal_transaksi, 
                              transaksi.total_harga, 
                              pelanggan.nama AS nama_pelanggan, 
                              kasir.nama AS nama_kasir
                       FROM transaksi 
                       LEFT JOIN users AS pelanggan ON transaksi.id_user = pelanggan.id 
                       LEFT JOIN users AS kasir ON transaksi.id_kasir = kasir.id
                       WHERE transaksi.id_kasir = $id_kasir");

  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM transaksi WHERE id_kasir = $id_kasir")[0]['total'];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kasir | Transaksi</title>
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
          <a href="Dashboard.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-gray-900 group-hover:text-white text-2xl"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="Toko.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
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
        <?php $i = 1; ?>
        <?php foreach ($transaksi as $t) : ?>
          <tbody>
            <tr class="bg-white border-b hover:bg-gray-50">
              <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                <?= $i++ ?>
              </th>
              <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                <?= htmlspecialchars($t['id']) ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= !empty($t['nama_pelanggan']) ? htmlspecialchars($t['nama_pelanggan']) : 'Pelanggan belum terdaftar!' ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= htmlspecialchars($t['nama_kasir']) ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= htmlspecialchars($t['tanggal_transaksi']) ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= htmlspecialchars($t['total_harga']) ?>
              </td>
              <td class="px-6 py-4 text-center">
                <?= htmlspecialchars($t['tunai']) ?>
              </td>
              <td class="px-6 py-4 text-blue-400 text-center">
                <a href="Transaksi/DetailTransaksi.php?id=<?php echo htmlspecialchars($t['id']) ?>" class="hover:underline">Detail Transaksi</a>
              </td>
            </tr>
          </tbody>
        <?php endforeach; ?>
      </table>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho <?= date('Y') ?></span>
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