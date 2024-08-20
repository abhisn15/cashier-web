<?php
require 'functions.php';

$email = isset($_GET['email']) ? $_GET['email'] : '';
$code_err = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $input_code = trim($_POST['verification_code']);

  // Ambil kode verifikasi yang valid dari database
  $stored_code = getVerificationCode($conn, $email);

  // Verifikasi apakah kode yang dimasukkan sama dengan kode yang disimpan
  if ($input_code === $stored_code) {
    // Tandai kode verifikasi sebagai digunakan
    deleteVerificationCode($conn, $email);
    header("Location: reset_password.php?email=" . urlencode($email));
    exit();
  } else {
    $code_err = "Kode verifikasi salah atau sudah kadaluarsa.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="./assets/css/style.css" />
  <title>Verifikasi Kode</title>
</head>

<body>
  <div class="flex h-screen items-center flex-col justify-center px-6 py-12 lg:px-8">
    <div class="border-2 w-[100%] sm:w-[50%] py-10 px-6 rounded-xl">

      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <div class="flex flex-row items-end justify-center">
          <span class="text-orange-400 font-extrabold text-xl sm:text-3xl">Bi</span>
          <span class="text-md sm:text-2xl font-bold">Kasir</span>
        </div>
        <h2 class="mt-10 text-center text-md sm:text-xl font-medium leading-9 tracking-tight text-gray-900">Verifikasi Kode</h2>
      </div>

      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="" method="POST">
          <div>
            <label for="verification_code" class="block text-sm sm:text-md font-medium leading-6 text-gray-900">Kode Verifikasi</label>
            <div class="mt-2">
              <input id="verification_code" name="verification_code" type="text" placeholder="Masukkan kode verifikasi" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6" value="<?php echo htmlspecialchars($_POST['verification_code'] ?? ''); ?>">
            </div>
            <span class="invalid-feedback text-red-500 text-sm"><?php echo $code_err; ?></span>
          </div>

          <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-orange-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400">Verifikasi Kode</button>
          </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
          Kembali ke <a href="forgot_password.php" class="font-semibold leading-6 text-orange-400 hover:text-orange-500">halaman sebelumnya</a>.
        </p>
      </div>
    </div>
  </div>
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <script src="assets/js/script.js"></script>
</body>

</html>