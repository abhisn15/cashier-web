<?php
require '../functions.php';
session_start();
$username = $_SESSION['nama'];
$role = $_SESSION['role'];

$cartCount = 0;

if (isset($_SESSION['keranjang'])) {
  $cartCount = count($_SESSION['keranjang']); // Menghitung jumlah produk unik
}


if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Kasir') {
  header('Location: ../login.php');
  exit();
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
  <title>Kasir | Toko</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="https://unpkg.com/html5-qrcode@2.0.9/dist/html5-qrcode.min.js"></script>
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
    <h1 class="text-2xl">Kasir</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="#" class="text-orange-500">Kasir</a>
      <span class="text-gray-500">/</span>
    </div>
    <br>
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="relative" onclick="location.href = 'Kasir/Keranjang.php'">
        <button id="basket-button" class="absolute right-0 sm:relative">
          <ion-icon name="cart-sharp" class="text-orange-400 w-10 h-10"></ion-icon>
        </button>
        <!-- Notifikasi jumlah item dalam keranjang -->
        <span id="cart-count" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full">
          <?php echo htmlspecialchars($cartCount); ?>
        </span>
      </div>
      <br>
      <br>
    </div>
    <div class="flex justify-center">
      <div style="width: 500px" id="reader"></div>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <script>
    var html5QrcodeScanner = new Html5QrcodeScanner(
      "reader", {
        fps: 60,
        qrbox: 250
      });

    function onScanSuccess(decodedText, decodedResult) {
      console.log(`Scan result: ${decodedText}`, decodedResult);

      // Setelah barcode terdeteksi, cari produk berdasarkan barcode
      searchProduct(decodedText);
      html5QrcodeScanner.clear(); // Hentikan scanner setelah sukses
    }

    html5QrcodeScanner.render(onScanSuccess);

    function searchProduct(barcode) {
      fetch(`search_product.php?barcode=${barcode}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Tambahkan produk ke keranjang secara otomatis
            addToCart(data.product_id);
          } else {
            alert('Produk tidak ditemukan');
          }
        })
        .catch(error => console.error('Error:', error));
    }

    function addToCart(productId) {
      fetch(`Kasir/Keranjang.php?id=${productId}`)
        .then(response => response.text())
        .then(data => {
          console.log('Product added to cart:', data);

          // Update jumlah item dalam keranjang
          const cartCount = document.getElementById('cart-count');
          cartCount.textContent = parseInt(cartCount.textContent) + 1;

          // Reload halaman setelah produk berhasil ditambahkan ke keranjang
          location.reload();
        })
        .catch(error => console.error('Error:', error));
    }
  </script>

</body>

</html>