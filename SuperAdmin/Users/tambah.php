<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../../functions.php';
session_start();

// Cek apakah user sudah login dan memiliki role SuperAdmin
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $_SESSION['role'] !== 'SuperAdmin') {
  header('Location: ../login.php');
  exit;
}

$register_data = tambahUser($conn);
$nama = $register_data['nama'];
$no_hp = $register_data['no_hp'];
$role = $register_data['role'];
$email = $register_data['email'];
$password = $register_data['password'];
$confirm_password = $register_data['confirm_password'];
$nama_err = $register_data['nama_err'];
$no_hp_err = $register_data['no_hp_err'];
$role_err = $register_data['role_err'];
$email_err = $register_data['email_err'];
$password_err = $register_data['password_err'];
$confirm_password_err = $register_data['confirm_password_err'];

// Daftar role yang tersedia
$roles = ['User', 'Kasir', 'Staff'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SuperAdmin | Tambah User</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
  <link rel="stylesheet" href="../assets/css/style.css" />
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</head>

<body>
  <!-- Sidebar dan Header -->
  <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar" type="button" class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200">
    <span class="sr-only">Open sidebar</span>
    <svg class="w-10 h-10 ml-5 sm:ml-0" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
      <path clip-rule="evenodd" fill-rule="evenodd" d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z"></path>
    </svg>
  </button>

  <!-- Sidebar -->
  <aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-[90dvh] transition-transform -translate-x-full bg-white rounded-r-2xl shadow-xl sm:translate-x-0 border-r-2" aria-label="Sidebar">
    <div class="flex flex-row py-5 items-end pl-5">
      <span class="text-3xl font-bold text-orange-400">Bi</span>
      <span class="text-2xl font-medium">Kasir</span>
    </div>
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white rounded-b-2xl bg-gray-800 border-r-2">
      <ul class="space-y-2 font-medium">
        <li>
          <a href="../Dashboard.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="home-sharp" class="text-2xl"></ion-icon>
            <span class="ms-3">Dashboard</span>
          </a>
        </li>
        <li>
          <a href="../Produk.php" class="flex items-center p-2 rounded-lg text-gray-900 hover:text-white hover:bg-orange-400 group">
            <ion-icon name="cube" class="text-2xl"></ion-icon>
            <span class=" flex-1 ms-3 whitespace-nowrap">Produk</span>
          </a>
        </li>
        <li>
          <button type="button" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group bg-orange-400 text-white" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
            <ion-icon name="people-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Users</span>
            <svg class="w-5 h-5" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
              <rect width="48" height="48" fill="white" fill-opacity="0.01" />
              <path d="M13 30L25 18L37 30" stroke="#FFFFFF" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
          <ul class=" py-2 space-y-2">
            <li>
              <a href="../Pelanggan.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Pelanggan</a>
            </li>
            <li>
              <a href="../Kasir.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Kasir</a>
            </li>
            <li>
              <a href="../Staff.php" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-orange-400 hover:text-white">Staff</a>
            </li>
          </ul>
        </li>
        <li>
          <a href="../Transaksi.php" class="flex items-center p-2 text-gray-900 rounded-lg hover:text-white hover:bg-orange-400 group">
            <ion-icon name="wallet-sharp" class="text-2xl"></ion-icon>
            <span class="flex-1 ms-3 whitespace-nowrap">Transaksi</span>
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
    <h1 class="text-2xl">Tambah User Baru</h1>
    <br>
    <div class="text-xl flex flex-row items-center gap-4">
      <a href="../Users.php" class="text-gray-500">Users</a>
      <span class="text-gray-500">/</span>
      <a href="#" class="text-orange-500">Tambah User Baru</a>
    </div>

    <!-- Form untuk menambahkan user baru -->
    <div class="min-h-screen p-6 flex items-center justify-center">
      <div class="container max-w-screen-lg mx-auto">
        <div>
          <br>
          <div class="bg-white rounded shadow-lg p-4 px-4 md:p-8 mb-6">
            <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 lg:grid-cols-3">
              <div class="text-gray-600">
                <p class="font-medium text-lg">Tambah User Baru</p>
                <p>Tolong isi semua formnya!</p>
              </div>

              <div class="lg:col-span-2">
                <form method="POST" action="">
                  <div class="grid gap-4 gap-y-2 text-sm grid-cols-1 md:grid-cols-5">

                    <div class="md:col-span-5">
                      <label for="nama">Nama Lengkap</label>
                      <input type="text" name="nama" id="nama" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo htmlspecialchars($nama); ?>" placeholder="Masukkan Nama Lengkap Anda" />
                      <span class="text-red-500 text-xs"><?php echo $nama_err; ?></span>
                    </div>

                    <div class="md:col-span-5">
                      <label for="email">Email Address</label>
                      <input type="text" name="email" id="email" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo htmlspecialchars($email); ?>" placeholder="user@example.com" />
                      <span class="text-red-500 text-xs"><?php echo $email_err; ?></span>
                    </div>

                    <div class="md:col-span-3">
                      <label for="no_hp">Nomor Telepon</label>
                      <input type="text" name="no_hp" id="no_hp" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50" value="<?php echo htmlspecialchars($no_hp); ?>" oninput="validatePhoneNumber(this)" placeholder="08XXXX" />
                      <span class="text-red-500 text-xs"><?php echo $no_hp_err; ?></span>
                    </div>

                    <div class="md:col-span-2">
                      <label for="role">Role</label>
                      <select name="role" id="role" class="h-10 border mt-1 rounded px-4 w-full bg-gray-50">
                        <option value="">Pilih Role</option>
                        <?php foreach ($roles as $option_role) : ?>
                          <option value="<?php echo $option_role; ?>" <?php echo ($option_role == $role) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($option_role); ?>
                          </option>
                        <?php endforeach; ?>
                      </select>

                      <span class="text-red-500 text-xs"><?php echo $role_err; ?></span>
                    </div>

                    <div class="md:col-span-5">
                      <label for="password">Password</label>
                      <div class="mt-2 relative">
                        <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan Password anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
                        <span class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3">
                          <ion-icon id="togglePassword" name="eye-off-outline" class="text-gray-500 text-xl"></ion-icon>
                        </span>
                      </div>
                      <span class="text-red-500 text-xs"><?php echo $password_err; ?></span>
                    </div>

                    <div class="md:col-span-5">
                      <label for="confirm_password">Konfirmasi Password</label>
                      <div class="mt-2 relative">
                        <input id="confirm_password" name="confirm_password" type="password" autocomplete="current-password" placeholder="Konfirmasi Password anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>>
                        <span class="toggle-confirm_password absolute inset-y-0 right-0 flex items-center pr-3">
                          <ion-icon id="toggleConfirmPassword" name="eye-off-outline" class="text-gray-500 text-xl"></ion-icon>
                        </span>
                      </div>
                      <span class="text-red-500 text-xs"><?php echo $confirm_password_err; ?></span>
                    </div>

                    <div class="md:col-span-5 text-right">
                      <div class="inline-flex items-end">
                        <button type="submit" class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">Submit</button>
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
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="../../assets/js/script.js"></script>
</body>

</html>