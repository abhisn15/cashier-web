<?php
require '../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || isset($_SESSION['Kasir'])) {
  header('Location: ../login.php');
}

$keranjang = query('SELECT * FROM barang WHERE id = 1');
$pelanggan = query('SELECT * FROM users WHERE role IN ("User")');
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
    <h1 class="text-2xl">Keranjang</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="Toko.php" class="text-gray-500">Toko</a>
      <span class="text-gray-500">/</span>
      <a href="#" class="text-orange-500">Keranjang</a>
    </div>
    <br>

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg ">
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
        <tbody>
          <?php $i = 1; ?>
          <?php foreach ($keranjang as $row) : ?>
            <tr class="bg-white border-b hover:bg-gray-50">
              <td class="px-6 py-4 text-black flex flex-row items-center gap-2 w-80">
                <img src="../assets/img/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>" width="100">
                <span><?= htmlspecialchars($row['nama_barang']) ?></span>
              </td>
              <td class="px-6 py-4 text-center harga-satuan" data-harga="<?= $row['harga'] ?>"> <?php echo 'Rp ' . number_format($row['harga'], 0, ',', '.'); ?></td>

              <td class="px-6 py-4 text-center">
                <div class="relative flex items-center max-w-[20rem]">
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
              </td>

              <td class="px-6 py-4 text-center total-harga">
                <?php echo 'Rp ' . number_format($row['harga'], 0, ',', '.'); ?>
              </td>
              <td class="px-6 py-4 text-center">
                <a onclick="return confirm('Apakah kamu yakin ingin menghapus barang ini di keranjang?');" href="./Users/hapus.php?id=<?= htmlspecialchars($row['id']) ?>" class="font-medium text-red-600 hover:underline">Hapus</a>
              </td>
            </tr>
            <?php $i++ ?>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <br>
    <div class="bg-white shadow-md border-2 rounded-xl py-5 px-10 flex flex-row items-center justify-between gap-10">
      <div class="w-80">
        <label for="">Jika pelanggan belum terdaftar abaikan!</label>
        <select name="pelanggan" id="pelanggan" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50">
          <option value="">Pilih Pelanggan</option>
          <?php foreach ($pelanggan as $option_pelanggan) : ?>
            <option value="<?php echo $option_pelanggan; ?>">
              <?php echo htmlspecialchars($option_pelanggan['nama']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <span>Total Produk(1)</span>
      <span id="total-harga-keseluruhan">Total Harga: <?php echo 'Rp ' . number_format($row['harga'], 0, ',', '.'); ?></span>
      <button class="bg-orange-400 hover:bg-orange-600 py-3 px-10 text-center rounded-md text-white">
        Checkout
      </button>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      function updateTotalHargaKeseluruhan() {
        let totalHargaKeseluruhan = 0;

        // Iterate over all rows to sum up the total prices
        document.querySelectorAll('tbody tr').forEach(function(row) {
          const totalHargaElement = row.querySelector('.total-harga');
          const totalHarga = parseInt(totalHargaElement.textContent.replace(/[^0-9]/g, ''), 10);
          totalHargaKeseluruhan += totalHarga;
        });

        // Update the total harga keseluruhan
        document.getElementById('total-harga-keseluruhan').textContent = 'Total Harga: Rp ' + totalHargaKeseluruhan.toLocaleString('id-ID');
      }

      // Pilih semua baris pada tabel
      document.querySelectorAll('tbody tr').forEach(function(row) {
        const incrementButton = row.querySelector('.increment-button');
        const decrementButton = row.querySelector('.decrement-button');
        const quantityInput = row.querySelector('.quantity-input');
        const hargaSatuanElement = row.querySelector('.harga-satuan');
        const totalHargaElement = row.querySelector('.total-harga');

        const hargaSatuan = parseInt(hargaSatuanElement.dataset.harga, 10);

        function updateTotal() {
          let quantity = parseInt(quantityInput.value, 10);

          // Periksa apakah kuantitas kurang dari 1
          if (quantity < 1) {
            if (confirm('Apakah kamu ingin menghapus barang ini dari keranjang?')) {
              // Redirect untuk hapus item
              window.location.href = "./Users/hapus.php?id=<?= htmlspecialchars($row['id']) ?>";
              return;
            } else {
              quantity = 1;
              quantityInput.value = quantity;
            }
          }

          // Update total harga
          const total = hargaSatuan * quantity;
          totalHargaElement.textContent = 'Rp ' + total.toLocaleString('id-ID');

          // Update total harga keseluruhan
          updateTotalHargaKeseluruhan();
        }

        // Event listener untuk tombol increment
        incrementButton.addEventListener('click', function() {
          let quantity = parseInt(quantityInput.value, 10);
          quantity += 1;
          quantityInput.value = quantity;
          updateTotal();
        });

        // Event listener untuk tombol decrement
        decrementButton.addEventListener('click', function() {
          let quantity = parseInt(quantityInput.value, 10);
          quantity -= 1;
          quantityInput.value = quantity;
          updateTotal();
        });

        // Event listener untuk perubahan manual pada input quantity
        quantityInput.addEventListener('input', function() {
          let quantity = parseInt(quantityInput.value, 10);

          // Pastikan quantity tidak kurang dari 1
          if (quantity < 1 || isNaN(quantity)) {
            quantity = 1;
          }

          quantityInput.value = quantity;
          updateTotal();
        });

        // Inisialisasi total harga saat halaman pertama kali dimuat
        updateTotal();
      });

      // Inisialisasi total harga keseluruhan saat halaman pertama kali dimuat
      updateTotalHargaKeseluruhan();
    });
  </script>

</body>

</html>