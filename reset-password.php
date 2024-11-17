<?php
require './functions.php';

// Pastikan token ada
if (!isset($_GET["token"])) {
  die("Token tidak ditemukan");
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

global $conn;

// Query untuk mengambil user berdasarkan token hash
$stmt = $conn->prepare("SELECT * FROM users WHERE reset_token_hash = ?");
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
  die("Token tidak ditemukan");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
  die("Token sudah kedaluwarsa");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <title>Lupa Kata Sandi</title>
</head>

<body>
  <div class="flex h-screen items-center flex-col justify-center px-6 py-12 lg:px-8">
    <div class="border-2 w-[100%] sm:w-[50%] py-10 px-6 rounded-xl">
      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="flex flex-row items-end justify-center">
          <span class="text-orange-400 font-extrabold text-xl sm:text-3xl">Bi</span>
          <span class="text-md sm:text-2xl font-bold">Kasir</span>
        </div>
        <h2 class="mt-10 text-center text-md sm:text-xl font-medium leading-9 tracking-tight text-gray-900">Lupa Kata Sandi</h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="process-reset-password.php" method="POST">
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <div>
            <label for="password" class="block text-sm sm:text-md font-medium leading-6 text-gray-900">Password Baru</label>
            <div class="mt-2">
              <input id="password" name="password" type="password" autocomplete="password" placeholder="Masukkan password baru anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6">
            </div>
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm sm:text-md font-medium leading-6 text-gray-900">Konfirmasi Password</label>
            <div class="mt-2">
              <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="password_confirmation" placeholder="Konfirmasi password baru anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6">
            </div>
          </div>

          <div>
            <button id="submitButton" type="submit" class="flex w-full justify-center rounded-md bg-orange-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400">
              Perbarui Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', function() {
      submitButton.disabled = true; // Menonaktifkan tombol submit saat form dikirim
    });
  </script>
</body>

</html>