<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require "./functions.php";

$success_msg = $email_err = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $email = filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);

  if (!$email) {
    $email_err = "Email tidak valid.";
  } else {
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    $expiry = date("Y-m-d H:i:s", time() + 60 * 5);

    global $conn;

    $stmt = $conn->prepare("UPDATE users
                SET reset_token_hash = ?,
                    reset_token_expires_at = ?
                WHERE email = ?");

    if ($stmt) {
      $stmt->bind_param("sss", $token_hash, $expiry, $email);
      $stmt->execute();

      if ($stmt->affected_rows > 0) {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@example.com", "BiKasir Support");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->isHTML(true); // Gunakan format HTML
        $mail->Body = <<<END
                    <p>Klik tautan berikut untuk mereset kata sandi Anda:</p>
                    <p><a href="http://localhost/kasir/reset-password.php?token={$token}">Reset Password</a></p>
                    <p>Jika Anda tidak meminta reset kata sandi, abaikan email ini.</p>
                END;

        try {
          $mail->send();
          $success_msg = "Email reset kata sandi telah dikirim. Silakan periksa kotak masuk Anda.";
        } catch (Exception $e) {
          $email_err = "Email gagal dikirim. Error: {$mail->ErrorInfo}";
        }
      } else {
        $email_err = "Email tidak ditemukan dalam sistem kami.";
      }
    } else {
      $email_err = "Terjadi kesalahan pada server. Silakan coba lagi.";
    }
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
        <form class="space-y-6" action="" method="POST">
          <div>
            <label for="email" class="block text-sm sm:text-md font-medium leading-6 text-gray-900">Email address</label>
            <div class="mt-2">
              <input id="email" name="email" type="email" autocomplete="email" placeholder="Masukkan Email anda" required class="block w-full rounded-md border-0 px-3 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-orange-400 text-sm sm:text-md sm:leading-6">
            </div>
            <span class="invalid-feedback text-red-500 text-sm"><?php echo htmlspecialchars($email_err); ?></span>
          </div>

          <div>
            <button id="submitButton" type="submit" class="flex w-full justify-center rounded-md bg-orange-400 px-3 py-1.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-orange-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-orange-400">
              Kirim Kode Melalui Gmail
            </button>

          </div>
          <div class="text-center">
            <span class="text-green-500"><?php echo htmlspecialchars($success_msg); ?></span>
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
  <script>
    const form = document.querySelector('form');
    const submitButton = document.getElementById('submitButton');

    form.addEventListener('submit', function() {
      submitButton.disabled = true; // Menonaktifkan tombol submit
    });

    // Jika berhasil mengirim email, aktifkan kembali tombol submit
    <?php if (!empty($success_msg)) { ?>
      submitButton.disabled = false; // Mengaktifkan tombol submit setelah pesan sukses
    <?php } ?>
  </script>

  <!-- <script src="assets/js/script.js"></script> -->
</body>

</html>