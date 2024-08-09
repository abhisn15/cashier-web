<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || isset($_SESSION['Kasir'])) {
  header('Location: ../login.php');
}

// Fungsi untuk mencari data pengguna berdasarkan keyword
function cari($keyword)
{
  global $conn;
  $stmt = $conn->prepare("SELECT * FROM barang WHERE nama_barang LIKE ?");
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
    $produk = query("SELECT * FROM barang");
    $total = query("SELECT COUNT(*) AS total FROM barang")[0]['total'];
  } else {
    // Jika keyword tidak kosong, cari data yang sesuai
    $produk = cari($keyword);
    $total = count($produk);
  }
} else {
  // Query untuk mengambil data sesuai halaman
  $produk = query("SELECT * FROM barang");
  // Query untuk menghitung total data
  $total = query("SELECT COUNT(*) AS total FROM barang")[0]['total'];
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
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-100 bg-orange-400 group">
            <svg width="26px" height="26px" class="fill-white " viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path d="M22,5H2A1,1,0,0,0,1,6v4a3,3,0,0,0,2,2.82V22a1,1,0,0,0,1,1H20a1,1,0,0,0,1-1V12.82A3,3,0,0,0,23,10V6A1,1,0,0,0,22,5ZM15,7h2v3a1,1,0,0,1-2,0ZM11,7h2v3a1,1,0,0,1-2,0ZM7,7H9v3a1,1,0,0,1-2,0ZM4,11a1,1,0,0,1-1-1V7H5v3A1,1,0,0,1,4,11ZM14,21H10V19a2,2,0,0,1,4,0Zm5,0H16V19a4,4,0,0,0-8,0v2H5V12.82a3.17,3.17,0,0,0,1-.6,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3,3,0,0,0,4,0,3.17,3.17,0,0,0,1,.6Zm2-11a1,1,0,0,1-2,0V7h2ZM4.3,3H20a1,1,0,0,0,0-2H4.3a1,1,0,0,0,0,2Z" />
            </svg>
            <span class=" flex-1 ms-3 whitespace-nowrap">Toko</span>
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
    <h1 class="text-2xl">Toko</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Toko</a>
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
      <div class="relative" onclick="location.href = 'Keranjang.php'">
        <button id="basket-button" class="absolute right-0 sm:relative">
          <ion-icon name="basket-sharp" class="text-orange-400 w-10 h-10"></ion-icon>
        </button>
        <!-- Notifikasi jumlah item dalam keranjang -->
        <span id="cart-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">1</span>
      </div>
    </div>
    <br>
    <br>
    <div class="flex flex-wrap items-center justify-center gap-4">
      <?php foreach ($produk as $p) : ?>
        <div class="flex flex-col items-center justify-between bg-gray-50 shadow-md rounded-md py-8 px-5 gap-5">
          <div class="flex justify-center">
            <img src="../assets/img/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_barang']) ?>" width="200">
          </div>
          <div class="flex flex-col w-full gap-2">
            <span class="text-[12px] text-gray-400"><?= htmlspecialchars($p['kode_barang']) ?></span>
            <span class="text-md text-gray-600 font-medium w-60">
              <?= strlen($p['nama_barang']) > 45 ? htmlspecialchars(substr($p['nama_barang'], 0, 45)) . '...' : htmlspecialchars($p['nama_barang']) ?>
            </span>
            <span class="text-[12px] text-red-500">Expired: <?= htmlspecialchars($p['expired'] ?? '<span class="text-gray-300">Barang tidak ada kadaluarsa</span>') ?></span>
          </div>
          <div class="flex flex-row items-center w-full">
            <span class="text-gray-700 font-bold w-full"> <?php echo 'Rp ' . number_format($p['harga'], 0, ',', '.'); ?></span>
            <button class="flex flex-row items-center justify-end w-full">
              <ion-icon name="add-circle-sharp" class="w-8 h-8 text-orange-400 hover:text-orange-600"></ion-icon>
              <span class="text-sm">Add to cart</span>
            </button>
          </div>
        </div>
      <?php endforeach; ?>
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