<?php
require 'functions.php';

$login_data = login($conn);
$email = $login_data['email'];
$password = $login_data['password'];
$email_err = $login_data['email_err'];
$password_err = $login_data['password_err'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <title>Halaman Login</title>
</head>

<body>
  <div class="flex h-screen items-center flex-col justify-center px-6 py-12 lg:px-8">
    <div class="border-2 w-[100%] sm:w-[50%] py-10 px-6 rounded-xl">

      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="flex flex-row items-end justify-center">
          <span class="text-orange-400 font-extrabold text-xl sm:text-3xl">Bi</span>
          <span class="text-md sm:text-2xl font-bold">Kasir</span>
        </div>
        <h2 class="mt-10 text-center text-md sm:text-xl font-medium leading-9 tracking-tight text-gray-900">Masuk ke akun anda</h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="#" method="POST">
          <div>
            <label for="email" class="block text-sm sm:text-md font-medium leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
              <input id="email" name="email" type="email" autocomplete="email" placeholder="Masukkan Email anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?> value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <span class="invalid-feedback text-red-500 text-sm"><?php echo $email_err; ?></span>
          </div>

          <div>
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm font-medium leading-6 text-gray-900 text-[12px] sm:text-md">Password</label>
            </div>
            <div class="mt-2 relative">
              <input id="password" name="password" type="password" autocomplete="current-password" placeholder="Masukkan Password anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>>
              <span class="toggle-password absolute inset-y-0 right-0 flex items-center pr-3">
                <ion-icon id="togglePassword" name="eye-off-outline" class="text-gray-500 text-xl"></ion-icon>
              </span>
            </div>
            <span class="invalid-feedback text-red-500 text-sm"><?php echo $password_err; ?></span>
          </div>
          <div class="text-sm w-full text-end">
            <a href="lupa_pass.php" class="font-semibold text-orange-400 hover:text-orange-500 text-[12px] sm:text-md">Lupa password?</a>
          </div>
          <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-orange-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400">Masuk</button>
          </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
          Tidak punya akun?
          <a href="registrasi.php" class="font-semibold leading-6 text-orange-400 hover:text-orange-500">Registrasi</a>
        </p>
      </div>
    </div>
  </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>