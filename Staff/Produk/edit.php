<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../../functions.php';
session_start();

$role = $_SESSION['role'];

// Cek apakah user sudah login dan memiliki role Staff
if (
  !isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Staff'
) {
  header('Location: ../../login.php');
  exit();
}
// Ambil ID produk dari URL
$id = $_GET['id'];

// Query produk berdasarkan ID
$produk = mysqli_query($conn, "SELECT * FROM barang WHERE id = $id");

// Jika produk tidak ditemukan, alihkan kembali ke halaman produk
if (mysqli_num_rows($produk) === 0) {
  echo "
        <script>
            alert('Produk tidak ditemukan!');
            document.location.href = '../Produk.php';
        </script>
    ";
  exit;
}

$produk = mysqli_fetch_assoc($produk);

// Cek apakah tombol submit sudah ditekan
if (isset($_POST['submit'])) {
  if (editProduk($_POST, $id) > 0) {
    echo "
            <script>
                alert('Data produk berhasil diubah!');
                document.location.href = '../Produk.php';
            </script>
        ";
  } else {
    echo "
            <script>
                alert('Data produk gagal diubah!');
                document.location.href = '../Produk.php';
            </script>
        ";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Staff | Edit Produk</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- <link rel="stylesheet" href="../../assets/css/style.css" /> -->
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

  <!-- Konten Sidebar disini -->
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-[90dvh] transition-transform -translate-x-full bg-white rounded-r-2xl shadow-xl sm:translate-x-0 border-r-2" aria-label="Sidebar">
    <div class="flex flex-row py-5 items-end pl-5">
      <span class="text-3xl font-bold text-orange-400">Bi</span>
      <span class="text-2xl font-medium">Kasir</span>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white rounded-b-2xl bg-gray-800 border-r-2">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="../Dashboard.php" class="flex items-center p-2 rounded-lg text-grayy-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-2xl"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="#" class="flex items-center p-2 rounded-lg text-white bg-orange-400 group">
            <ion-icon name="cube" class="text-gray-900 text-2xl text-white"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Produk</span>
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

  <div class="sm:pl-8 py-5 mx-10 sm:ml-64 sm:mr-10">
    <h1 class="text-2xl">Edit Produk</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="../Produk.php" class="text-gray-500">Produk</a>
      <span class="text-gray-500">/</span>
      <a href="#" class="text-orange-500">Edit Produk</a>
    </div>

    <!-- Form untuk mengedit produk -->
    <div class="min-h-screen p-6 flex items-center justify-center">
      <div class="container max-w-screen-lg mx-auto">
        <div>
          <br>
          <div class="bg-white rounded shadow-lg p-4 px-4 md:p-8 mb-6">
            <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 lg:grid-cols-3">
              <div class="text-gray-600">
                <p class="font-medium text-lg">Edit Produk</p>
                <p>Tolong ubah form yang diperlukan!</p>
              </div>

              <div class="lg:col-span-2">
                <form method="POST" action="" enctype="multipart/form-data">
                  <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 md:grid-cols-5">

                    <div class="md:col-span-5">
                      <label for="kode_barang">Kode Barang</label>
                      <input type="text" name="kode_barang" id="kode_barang" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo $produk['kode_barang']; ?>" readonly />
                    </div>

                    <div class="md:col-span-5">
                      <label for="nama_barang">Nama Barang</label>
                      <input type="text" name="nama_barang" id="nama_barang" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo $produk['nama_barang']; ?>" placeholder="Masukkan Nama Barang" required />
                    </div>

                    <div class="md:col-span-5">
                      <label for="expired">Tanggal Expired</label>
                      <input type="date" name="expired" id="expired" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo $produk['expired'] ?? '' ?>" />
                    </div>

                    <div class="md:col-span-5">
                      <label for="harga">Harga</label>
                      <input type="number" name="harga" id="harga" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo $produk['harga']; ?>" placeholder="Masukkan Harga" required />
                    </div>

                    <div class="md:col-span-5">
                      <label for="stok">Stok</label>
                      <input type="number" name="stok" id="stok" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" placeholder="Masukkan Stok" value="<?php echo $produk['stok']; ?>" required />
                    </div>

                    <div class="md:col-span-5">
                      <br>
                      <img id="previewImg" src="../../assets/img/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama_barang'] ?>" width="100"> <br>
                      <label class="block mb-2 text-sm font-medium text-white" for="file_input">Upload file</label>
                      <input class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer text-gray-400 bgborderplaceholder-gray-400" aria-describedby="file_input_help" id="gambar" name="gambar" onchange="previewImage(event)" type="file">
                      <input type="hidden" name="gambarLama" value="<?php echo $produk['gambar']; ?>">
                      <p class="mt-1 text-sm text-gray-300" id="file_input_help">SVG, PNG, or JPG (MAX. 800x400px).</p>
                    </div>

                    <div class="md:col-span-5 text-right">
                      <div class="inline-flex items-end">
                        <button type="submit" name="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Edit Produk</button>
                      </div>
                    </div>

                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-white w-full sm:pl-8 pl-10 py-5">
    <span class="sm:ml-64">&copy Created by Abhi Surya Nugroho 2024</span>
  </footer>

  <!-- <script src="../../assets/js/script.js"></script> -->
  <script>
    var hargaInput = document.getElementById('harga');
    document.querySelector('form').addEventListener('submit', function(event) {
      const hargaInput = document.getElementById('harga');
      // Menghapus format Rupiah menjadi hanya angka
      const rawValue = hargaInput.value.replace(/[^0-9]/g, '');
      hargaInput.value = parseFloat(rawValue);
    });
    // Format angka ke Rupiah
    function formatRupiah(angka) {
      return `${angka.toLocaleString('id-ID')}`;
    }
    hargaInput.addEventListener('input', function() {
      const rawValue = hargaInput.value.replace(/[^0-9]/g, ''); // Menghapus format Rupiah
      if (rawValue === '') {
        hargaInput.value = '';
      } else {
        hargaInput.value = formatRupiah(parseInt(rawValue));
      }
    });
    hargaInput.value = formatRupiah(<?= $produk['harga'] ?>);

    function previewImage(event) {
      const reader = new FileReader();
      reader.onload = function() {
        const output = document.getElementById('previewImg');
        output.src = reader.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</body>

</html>