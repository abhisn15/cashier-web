<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require 'functions.php';

$register_data = register($conn);
$nama = $register_data['nama'];
$no_hp = $register_data['no_hp'];
$email = $register_data['email'];
$password = $register_data['password'];
$confirm_password = $register_data['confirm_password'];
$nama_err = $register_data['nama_err'];
$no_hp_err = $register_data['no_hp_err'];
$email_err = $register_data['email_err'];
$password_err = $register_data['password_err'];
$confirm_password_err = $register_data['confirm_password_err'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <title>Halaman Register</title>
</head>

<body>
  <div class="flex h-screen items-center flex-col justify-center px-6 py-12 lg:px-8">
    <div class="border-2 w-[100%] sm:w-[60%] py-4 px-6 rounded-xl">

      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="flex flex-row items-end justify-center">
          <span class="text-orange-400 font-extrabold text-3xl">Bi</span>
          <span class="text-2xl font-bold">Kasir</span>
        </div>
        <h2 class="mt-10 text-center text-2xl font-medium leading-9 tracking-tight text-gray-900">Daftar akun anda</h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="#" method="POST">
          <div>
            <label for="nama" class="block text-sm font-medium leading-6 text-gray-900">Nama lengkap</label>
            <div class="mt-2">
              <input id="nama" name="nama" type="text" autocomplete="nama" placeholder="Masukkan Nama Lengkap anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 sm:text-sm sm:leading-6" <?php echo (!empty($nama_err)) ? 'is-invalid' : ''; ?> value="<?php echo htmlspecialchars($nama); ?>">
            </div>
            <span class="invalid-feedback text-sm text-red-500"><?php echo $nama_err ?></span>
          </div>

          <div>
            <label for="no_hp" class="block text-sm font-medium leading-6 text-gray-900">Nomor telepon</label>
            <div class="mt-2">
              <input id="no_hp" name="no_hp" type="number" maxlength="12" oninput="validatePhoneNumber(this)" autocomplete="no_hp" placeholder="Masukkan Nomor Telepon anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 sm:text-sm sm:leading-6" <?php echo (!empty($no_hp_err)) ? 'is-invalid' : ''; ?> value="<?php echo htmlspecialchars($no_hp); ?>">

            </div>
            <span class="invalid-feedback text-sm text-red-500"><?php echo $no_hp_err ?></span>
          </div>

          <div>
            <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
            <div class="mt-2">
              <input id="email" name="email" type="email" autocomplete="email" placeholder="Masukkan Email anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 sm:text-sm sm:leading-6" <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?> value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <span class="invalid-feedback text-sm text-red-500"><?php echo $email_err ?></span>
          </div>

          <div>
            <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
            <div class="mt-2 relative">
              <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan Password anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
              <span class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3">
                <ion-icon id="togglePassword" name="eye-off-outline" class="text-gray-500 text-xl"></ion-icon>
              </span>
            </div>
            <span class="invalid-feedback text-sm text-red-500"><?php echo $password_err ?></span>
          </div>

          <div>
            <label for="confirm_password" class="block text-sm font-medium leading-6 text-gray-900">Konfirmasi Password</label>
            <div class="mt-2 relative">
              <input id="confirm_password" name="confirm_password" type="password" autocomplete="current-password" placeholder="Konfirmasi Password anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>>
              <span class="toggle-confirm_password absolute inset-y-0 right-0 flex items-center pr-3">
                <ion-icon id="toggleConfirmPassword" name="eye-off-outline" class="text-gray-500 text-xl"></ion-icon>
              </span>
            </div>
            <span class="invalid-feedback text-sm text-red-500"><?php echo $confirm_password_err ?></span>
          </div>

          <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-orange-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400">Daftar</button>
          </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
          Sudah punya akun?
          <a href="login.php" class="font-semibold leading-6 text-orange-400 hover:text-orange-500">Masuk ke akun anda</a>
        </p>
      </div>
    </div>
  </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>