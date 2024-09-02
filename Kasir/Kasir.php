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

if (isset($_POST['tambah_keranjang'])) {
  $kode_barang = trim($_POST['kode_barang']);

  // Cari barang berdasarkan kode_barang
  $stmt = $conn->prepare("SELECT * FROM barang WHERE kode_barang = ?");
  $stmt->bind_param('s', $kode_barang);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    $id = $product['id'];

    // Tambahkan barang ke keranjang
    if (!isset($_SESSION['keranjang'])) {
      $_SESSION['keranjang'] = [];
    }

    if (isset($_SESSION['keranjang'][$id])) {
      $_SESSION['keranjang'][$id] += 1;
    } else {
      $_SESSION['keranjang'][$id] = 1;
    }

    // Redirect atau reload halaman untuk update tampilan
    echo "
        <script>
            alert('Barang berhasil ditambahkan ke keranjang!');
            document.location.href = 'Kasir.php';
        </script>
        ";
  } else {
    echo "
        <script>
            alert('Kode barang tidak ditemukan!');
            document.location.href = 'Kasir.php';
        </script>
        ";
  }
}

// Menambahkan item ke keranjang

if (isset($_GET['id'])) {
  $id = intval($_GET['id']);

  if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
  }

  if (isset($_SESSION['keranjang'][$id])) {
    $_SESSION['keranjang'][$id] += 1;
  } else {
    $_SESSION['keranjang'][$id] = 1;
  }
  echo "Item added to cart";
}
// Menghapus item dari keranjang
if (isset($_GET['hapus'])) {
  $id = intval($_GET['hapus']);
  unset($_SESSION['keranjang'][$id]);
}

// Mengambil detail produk dari keranjang
$keranjang_kosong = !isset($_SESSION['keranjang']) || empty($_SESSION['keranjang']);
$totalHarga = 0;
$pelanggan = query('SELECT * FROM users WHERE role IN ("User")');

$produk = [];
if (!$keranjang_kosong) {
  $produk = query("SELECT * FROM barang WHERE id IN (" . implode(',', array_keys($_SESSION['keranjang'])) . ")");
  foreach ($produk as $row) {
    $totalHarga += $row['harga'] * $_SESSION['keranjang'][$row['id']];
  }
}

date_default_timezone_set('Asia/Jakarta');

// Checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
  //Jika keranjang kosong maka tidak bisa melakukan transaksi
  if ($keranjang_kosong) {
    echo '<script>alert("Keranjang anda kosong, silahkan masukkan barang terlebih dahulu kedalam keranjang!")</script>';
    echo '<script>document.location.href = "Keranjang.php";</script>';
    exit();
  }

  $tanggal_transaksi = date('Y-m-d H:i:s');
  $email_user = isset($_POST['email_user']) && !empty($_POST['email_user']) ? $_POST['email_user'] : null;
  $id_kasir = $_SESSION['id'];
  $total_harga = isset($_POST['total_harga']) ? intval(str_replace('.', '', $_POST['total_harga'])) : 0;
  $tunai = isset($_POST['tunai']) ? intval(str_replace('.', '', $_POST['tunai'])) : 0;

  // Jika tunai kurang dari total harga, tampilkan pesan error dan hentikan proses checkout
  if ($tunai < $total_harga) {
    echo "
        <script>
            alert('Checkout gagal! Uang tunai tidak mencukupi. Harap masukkan tunai lebih dari total harga.');
            document.location.href = 'Keranjang.php';
        </script>
        ";
    exit(); // Hentikan eksekusi skrip lebih lanjut
  }

  // Cek apakah email pelanggan terdaftar
  $id_user = null;
  if ($email_user) {
    $user_query = "SELECT id FROM users WHERE email = ? AND role = 'User'";
    $stmt = $conn->prepare($user_query);
    $stmt->bind_param('s', $email_user);
    $stmt->execute();
    $stmt->bind_result($id_user);
    $stmt->fetch();
    $stmt->close();

    if (!$id_user) {
      echo "
            <script>
                alert('Email pelanggan tidak ditemukan. Silakan masukkan email yang valid.');
                document.location.href = 'Keranjang.php';
            </script>
            ";
      exit(); // Hentikan eksekusi skrip lebih lanjut
    }
  }

  // Insert transaksi (jika id_user null, berarti pelanggan tidak terdaftar)
  $stmt = $conn->prepare("INSERT INTO transaksi (tanggal_transaksi, id_user, id_kasir, total_harga, tunai) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param('siiii', $tanggal_transaksi, $id_user, $id_kasir, $total_harga, $tunai);
  $stmt->execute();
  $id_transaksi = $stmt->insert_id;

  // Insert detail transaksi dan update stok barang
  foreach ($produk as $row) {
    $kuantitas = $_SESSION['keranjang'][$row['id']];
    $total_harga_barang = $kuantitas * $row['harga'];

    $stmt = $conn->prepare("INSERT INTO detail_transaksi (id_transaksi, id_barang, kuantitas, harga_satuan, total_harga, tunai) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param('iiiiii', $id_transaksi, $row['id'], $kuantitas, $row['harga'], $total_harga_barang, $tunai);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE barang SET stok = stok - ? WHERE id = ?");
    $stmt->bind_param('ii', $kuantitas, $row['id']);
    $stmt->execute();
  }

  // Clear keranjang
  unset($_SESSION['keranjang']);

  // Redirect ke halaman transaksi
  echo "
    <script>
        alert('Checkout telah berhasil!');
        document.location.href = 'Transaksi.php';
    </script>
    ";
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

  <style>
    #reader__dashboard_section_swaplink {
      color: blue;
    }

    #reader__dashboard_section_csr,
    #reader__status_span {
      display: none;
    }

    /* Menyembunyikan pilihan kamera */
    #reader__camera_selection {
      display: none;
    }

    #reader>div>span {
      text-align: center;
    }

    #reader>div {
      display: flex;
      justify-content: center;
    }

    #reader__dashboard_section_csr>div>button {
      padding: 10px 15px 10px 15px;
      border: 2px solid;
      border-radius: 10px;
      background-color: #FB923C;
      color: white !important;
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
    <div class='flex flex-col justify-center lg:justify-around lg:flex-row items-center'>
      <div class="flex justify-center">
        <div style="width: 500px" id="reader"></div>
      </div>
      <br>
      <br>
      <div class='flex flex-col items-center gap-10'>
        <h1 class='text-2xl text-center'>
          Masukkan Kode Barang Manual
        </h1>
        <div class="flex justify-center mt-4">
          <form action="" method="POST" class="flex flex-col items-center gap-4 w-full">
            <input type="text" name="kode_barang" placeholder="Masukkan Kode Barang" class="border p-2 rounded-lg w-[150%] lg:w-[100%] xl:w-[150%]" required>
            <button type="submit" name="tambah_keranjang" class="bg-orange-400 text-white p-2 rounded-lg w-[150%] lg:w-[100%] xl:w-[150%]">Tambah ke Keranjang</button>
          </form>
        </div>

      </div>
    </div>
    <br>
    <br>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
      <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3">Nama Barang</th>
            <th scope="col" class="px-6 py-3 text-center">Harga Satuan</th>
            <th scope="col" class="px-6 py-3 text-center">Kuantitas</th>
            <th scope="col" class="px-6 py-3 text-center">Total Harga</th>
            <th scope="col" class="px-6 py-3 text-center">Aksi</th>
          </tr>
        </thead>
        <!-- Loop untuk menampilkan item di keranjang -->
        <tbody>
          <?php if ($keranjang_kosong): ?>
            <tr>
              <td colspan="6" class="py-4 px-6 text-center">Keranjang belanja Anda kosong.</td>
            </tr>
          <?php else: ?>
            <?php $i = 1; ?>
            <?php foreach ($produk as $row) : ?>
              <?php $totalharga = $row['harga'] * $_SESSION['keranjang'][$row['id']] ?>
              <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4 text-black flex flex-row items-center gap-2 w-80">
                  <img src="../../assets/img/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>" width="100">
                  <span><?= htmlspecialchars($row['nama_barang']) ?></span>
                </td>
                <td class="px-6 py-4 text-center harga-satuan" data-harga="<?= $row['harga'] ?>">
                  <?php echo 'Rp ' . number_format($row['harga'], 0, ',', '.'); ?>
                </td>
                <!-- <td class="px-6 py-4 text-center">
                    <div class="relative lg:flex items-center max-w-[20rem]">
                      <button type="button" class="decrement-button bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-s-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                        </svg>
                      </button>
                      <input type="text" class="quantity-input bg-gray-100 border-2 border-x-0 border-gray-300 h-11 text-center text-gray-900 text-sm focus:ring-blue-500 focus:border-blue-500 block w-full py-2.5" value="1" required />
                      <button type="button" class="increment-button bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-e-lg p-3 h-11 focus:ring-gray-100 focus:ring-2 focus:outline-none">
                        <svg class="w-3 h-3 text-gray-900" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                        </svg>
                      </button>
                    </div>
                  </td> -->
                <td class="py-4 px-6 text-center"><?php echo htmlspecialchars($_SESSION['keranjang'][$row['id']]); ?></td>

                <td class="px-6 py-4 text-center total-harga">
                  <?php echo number_format($totalharga, 0, ',', '.'); ?>
                </td>
                <td class="px-6 py-4 text-center">
                  <a onclick="return confirm('Apakah kamu yakin ingin menghapus barang ini di keranjang?');" href="?hapus=<?php echo $row['id']; ?>" class="font-medium text-red-600 hover:underline">Hapus</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>

      </table>
    </div>
    <br>
    <form method="post" class="bg-white shadow-md border-2 rounded-xl py-5 px-10 flex flex-wrap items-center justify-between gap-5">
      <div class="w-80">
        <label for="id_user">Jika pelanggan belum terdaftar abaikan!</label>
        <input type="email" name="email_user" id="email_user" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" placeholder="Masukkan email pelanggan" />
      </div>
      <span>Total Produk(<?php echo htmlspecialchars($cartCount) ?>)</span>
      <div class="md:col-span-5">
        <label for="tunai">Tunai</label>
        Rp <input type="text" name="tunai" id="tunai" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" placeholder="Masukkan Tunai" value="<?php echo number_format($totalHarga, 0, ',', '.'); ?>" required />
      </div>
      <span id="">Total Harga: Rp <input type="text" id="" name="total_harga" value="<?php echo number_format($totalHarga, 0, ',', '.'); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-orange-500 focus:ring-orange-500 sm:text-sm" readonly>
      </span>
      <span id="kembalian">Kembalian: Rp 0</span>
      <button name="checkout" class="bg-orange-400 hover:bg-orange-600 py-3 px-10 text-center rounded-md text-white">
        Checkout
      </button>
    </form>
  </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      var imgElement = document.querySelector("#reader__scan_region img");

      if (imgElement) {
        imgElement.src = "../images/scanner-image.png";
        imgElement.style.opacity = 1;
      }
    });
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
            alert(`Produk dengan kode produk ${barcode} tidak ditemukan`);
            location.reload();
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

    const tunaiInput = document.getElementById('tunai');
    let totalHarga = <?= $totalHarga ?>; // Initial total price


    // Format angka ke Rupiah
    function formatRupiah(angka) {
      return `${angka.toLocaleString('id-ID')}`;
    }


    tunaiInput.addEventListener('input', function() {
      const rawValue = tunaiInput.value.replace(/[^0-9]/g, ''); // Menghapus format Rupiah
      if (rawValue === '') {
        tunaiInput.value = '0';
      } else {
        tunaiInput.value = formatRupiah(parseInt(rawValue));
      }
      updateKembalian();
    });

    function updateTotalHarga() {
      totalHarga = 0; // Reset totalHarga
      document.querySelectorAll('.total-harga').forEach(function(element) {
        const harga = parseInt(element.getAttribute('data-total'));
        totalHarga += harga;
      });
      document.getElementById('total-harga-keseluruhan').textContent = `Total Harga: ${formatRupiah(totalHarga)}`;
      updateKembalian();
    }

    document.querySelectorAll('.decrement-button').forEach((button, index) => {
      button.addEventListener('click', function() {
        let quantity = parseInt(document.querySelectorAll('.quantity-input')[index].value);
        if (quantity > 1) {
          quantity -= 1;
          document.querySelectorAll('.quantity-input')[index].value = quantity;
          const hargaSatuan = parseInt(document.querySelectorAll('.harga-satuan')[index].getAttribute('data-harga'));
          const totalHargaBarang = quantity * hargaSatuan;
          document.querySelectorAll('.total-harga')[index].textContent = formatRupiah(totalHargaBarang);
          document.querySelectorAll('.total-harga')[index].setAttribute('data-total', totalHargaBarang);
          updateTotalHarga();
        }
      });
    });

    document.querySelectorAll('.increment-button').forEach((button, index) => {
      button.addEventListener('click', function() {
        let quantity = parseInt(document.querySelectorAll('.quantity-input')[index].value);
        quantity += 1;
        document.querySelectorAll('.quantity-input')[index].value = quantity;
        const hargaSatuan = parseInt(document.querySelectorAll('.harga-satuan')[index].getAttribute('data-harga'));
        const totalHargaBarang = quantity * hargaSatuan;
        document.querySelectorAll('.total-harga')[index].textContent = formatRupiah(totalHargaBarang);
        document.querySelectorAll('.total-harga')[index].setAttribute('data-total', totalHargaBarang);
        updateTotalHarga();
      });
    });

    // Format default untuk tunai adalah Rp 0
    tunaiInput.value = formatRupiah(0);

    const kembalianElement = document.getElementById('kembalian');

    function updateKembalian() {
      const tunai = parseInt(tunaiInput.value.replace(/[^0-9]/g, '')) || 0; // Menghapus format Rupiah
      const kembalian = tunai - totalHarga;
      kembalianElement.textContent = `Kembalian: Rp ${formatRupiah(kembalian)}`;
    }
  </script>

</body>

</html>