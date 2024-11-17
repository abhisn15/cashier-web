<?php
require '../../functions.php';

session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'User') {
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
  <style>
    .struk {
      font-family: 'Courier New', Courier, monospace;
      width: 58mm;
      /* Sesuaikan dengan lebar kertas printer */
    }

    .receipt {
      width: 100%;
    }

    .center {
      text-align: center;
    }

    .right {
      text-align: right;
    }

    .line {
      border-top: 1px dashed #000;
      margin: 5px 0;
    }

    .bold {
      font-weight: bold;
    }

    .small {
      font-size: 12px;
    }

    .total,
    .tunai,
    .kembalian {
      margin-top: 10px;
    }
  </style>
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
      <span class="text-2xl font-medium"><strong>S</strong>hop</span>
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
      <button onclick="location.href = 'Cetak.php?id=<?php echo htmlspecialchars($transaksi[0]['id']) ?>'" class="relative overflow-hidden text-white py-3 px-6 font-semibold rounded-3xl shadow-xl transform transition-all duration-500">
        <span class="absolute top-0 left-0 w-full h-full bg-orange-400"></span>
        <div class="flex flex-row items-center gap-2">
          <ion-icon name="receipt-sharp" class="text-sm sm:text-2xl text-white font-extrabold"></ion-icon>
          <span class="relative z-10 text-[12px] sm:text-sm">Cetak Detail Transaksi</span>
        </div>
      </button>
    </div>
    <br>
    <br>
    <div class="w-[320px] flex justify-center text-gray-500">
      <div class="receipt">
        <div class="center bold">
          BiShop<br>
          <span class="small">Jalan Sigma No. 666</span><br>
          <span class="small">Telp: 085314818119</span>
        </div>
        <div class="line"></div>
        <div class="small">
          Tanggal: <?= $transaksi[0]['tanggal_transaksi'] ?><br>
          ID Transaksi: <?= $transaksi[0]['id'] ?><br>
          Kasir: <?= $transaksi[0]['nama_kasir'] ?>
        </div>
        <div class="line text-gray-300"></div>
        <?php foreach ($transaksi as $detail) : ?>
          <div class="small">
            <?= $detail['nama_barang'] ?><br>
            <?= $detail['kuantitas'] ?> x Rp <?= number_format($detail['harga'], 0, ',', '.') ?> =
            <span class="right">Rp <?= number_format($detail['kuantitas'] * $detail['harga'], 0, ',', '.') ?></span>
          </div>
        <?php endforeach; ?>
        <div class="line"></div>
        <div class="small total">
          <span class="bold">Total</span>
          <span class="right">Rp <?= number_format($transaksi[0]['total_harga'], 0, ',', '.') ?></span>
        </div>
        <div class="small tunai">
          <span class="bold">Tunai</span>
          <span class="right">Rp <?= number_format($transaksi[0]['tunai'], 0, ',', '.') ?></span>
        </div>
        <div class="small kembalian">
          <span class="bold">Kembalian</span>
          <span class="right">Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
        </div>
        <div class="line"></div>
        <div class="center small">
          Terima Kasih<br>
          Selamat Belanja Kembali
        </div>
        <div class="line"></div>
        <div class="center small">
          Layanan Konsumen:<br>
          kontak@bishop.my.id
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