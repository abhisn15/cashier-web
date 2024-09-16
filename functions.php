<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Sesuaikan dengan lokasi autoload.php jika menggunakan Composer

$conn = mysqli_connect('localhost', 'root', '', 'kasir');

date_default_timezone_set('Asia/Jakarta');

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
function query($query)
{
  global $conn;
  $result = mysqli_query($conn, $query);
  if (!$result) {
    die("Query failed: " . mysqli_error($conn));
  }
  return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function upload()
{
  $namaFile = $_FILES['gambar']['name'];
  $ukuranFile = $_FILES['gambar']['size'];
  $error = $_FILES['gambar']['error'];
  $tmpName = $_FILES['gambar']['tmp_name'];

  // Cek apakah tidak ada gambar yang diupload?
  if ($error === 4) {
    echo "<sctip>
    alert('Pilih gambar terlebih dahulu!');
    </sctip>";
    return false;
  }

  // cek apakah yang diupload adalah gambar
  $mime = getimagesize($tmpName);
  if ($mime === false && $_FILES['gambar']['type'] !== 'image/heic') {
    echo "<script>
            alert('file yang Anda upload bukan gambar!');
            </script>";
    var_dump($_FILES['gambar']); // Tambahkan var_dump untuk debugging
    return false;
  }

  // cek jika ukurannya terlalu besar
  if ($ukuranFile > 5000000) {
    echo "<script>
               alert('ukuran gambar terlalu besar!');
               </script>";
    return false;
  }

  // generate nama gambar baru
  $ekstensiGambar = pathinfo($namaFile, PATHINFO_EXTENSION);
  $namaFileBaru = uniqid() . '' . $ekstensiGambar;

  // tentukan alamat penyimpanan file secara absolut
  $alamatSimpan = __DIR__ . '/assets/img/' . $namaFileBaru;

  if (move_uploaded_file($tmpName, $alamatSimpan)) {
    return $namaFileBaru; // mengembalikan nama file baru
  } else {
    echo "<script>
               alert('gagal mengunggah gambar!');
               </script>";
    error_log("Failed to move uploaded file. Check permissions or path. tmpName: $tmpName, alamatSimpan: $alamatSimpan");
    return false;
  }
}

function tambahUser($conn)
{
  $nama = $no_hp = $role = $email = $password = $confirm_password = "";
  $nama_err = $no_hp_err = $role_err = $email_err = $password_err = $confirm_password_err = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi nama
    if (empty(input($_POST["nama"]))) {
      $nama_err = "Please enter a name.";
    } else {
      $nama = input($_POST["nama"]);
    }

    // Validasi nomor handphone
    if (empty(input($_POST["no_hp"]))) {
      $no_hp_err = "Please enter a phone number.";
    } else {
      $no_hp = input($_POST["no_hp"]);
      if (!ctype_digit($no_hp)) {
        $no_hp_err = "Please enter a valid phone number.";
      }
    }

    // Validasi Role
    if (empty(input($_POST["role"]))) {
      $role_err = "Please enter a role value.";
    } else {
      $role = input($_POST["role"]);
      // Validasi jika role termasuk dalam daftar yang valid
      $valid_roles = ['User', 'Kasir', 'Staff']; // Daftar role yang valid
      if (!in_array($role, $valid_roles)) {
        $role_err = "Invalid role selected.";
      }
    }

    // Validasi email
    if (empty(input($_POST["email"]))) {
      $email_err = "Please enter an email.";
    } else {
      $email = input($_POST["email"]);
      // Cek jika email sudah digunakan di database
      $sql = "SELECT id FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email is already taken.";
          }
        } else {
          echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }

    // Validasi password
    if (empty(input($_POST["password"]))) {
      $password_err = "Please enter a password.";
    } elseif (strlen(input($_POST["password"])) < 6) {
      $password_err = "Password must have at least 6 characters.";
    } else {
      $password = input($_POST["password"]);
    }

    // Validasi confirm password
    if (empty(input($_POST["confirm_password"]))) {
      $confirm_password_err = "Please confirm password.";
    } else {
      $confirm_password = input($_POST["confirm_password"]);
      if (empty($password_err) && ($password != $confirm_password)) {
        $confirm_password_err = "Password did not match.";
      }
    }

    if (empty($nama_err) && empty($no_hp_err) && empty($role_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
      $sql = "INSERT INTO users (nama, no_hp, role, email, password) VALUES (?, ?, ?, ?, ?)";

      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sssss", $nama, $no_hp, $role, $email, $param_password);
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        if (mysqli_stmt_execute($stmt)) {
          echo "<script>
                        alert('User berhasil ditambahkan');
                        window.location.href = '../Users.php';
                    </script>";
        } else {
          echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }

  return [
    'nama' => $nama,
    'no_hp' => $no_hp,
    'role' => $role,
    'email' => $email,
    'password' => $password,
    'confirm_password' => $confirm_password,
    'nama_err' => $nama_err,
    'no_hp_err' => $no_hp_err,
    'role_err' => $role_err,
    'email_err' => $email_err,
    'password_err' => $password_err,
    'confirm_password_err' => $confirm_password_err
  ];
}


function hapus($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM users WHERE id = $id");

  return mysqli_affected_rows($conn);
}

function hapusBarang($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM barang WHERE id = $id");

  return mysqli_affected_rows($conn);
}

function editProduk($data, $id)
{
  global $conn;

  $kode_barang = htmlspecialchars($data["kode_barang"]);
  $nama_barang = htmlspecialchars($data["nama_barang"]);
  $expired = !empty($data["expired"]) ? "'" . htmlspecialchars($data["expired"]) . "'" : 'NULL';
  $harga = floatval(str_replace(',', '', $data["harga"]));
  $stok = htmlspecialchars($data["stok"]);
  $gambarLama = htmlspecialchars($data["gambarLama"]);

  // Cek apakah user memilih gambar baru atau tidak
  if ($_FILES['gambar']['error'] === 4) {
    $gambar = $gambarLama;
  } else {
    $gambar = upload();
    if (!$gambar) {
      return false;
    }

    // Hapus gambar lama jika gambar baru berhasil diupload
    if ($gambarLama && file_exists('../../assets/img/' . $gambarLama)) {
      unlink('../../assets/img/' . $gambarLama);
    }
  }

  $query = "UPDATE barang SET
                kode_barang = '$kode_barang',
                nama_barang = '$nama_barang',
                expired = $expired,
                harga = '$harga',
                stok = '$stok',
                gambar = '$gambar'
              WHERE id = $id";

  mysqli_query($conn, $query);

  return mysqli_affected_rows($conn);
}


function generateKodeBarang()
{
  global $conn;

  // Ambil kode barang terakhir dari database
  $query = "SELECT kode_barang FROM barang ORDER BY id DESC LIMIT 1";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  if ($row) {
    // Ambil angka terakhir dari kode barang dan tambahkan 1
    $lastKode = $row['kode_barang'];
    $lastNumber = (int)substr($lastKode, -4); // Ambil 4 digit terakhir dari kode
    $newNumber = str_pad($lastNumber + 1, 4, "0", STR_PAD_LEFT); // Tambah 1 dan padding 0
  } else {
    $newNumber = "0001"; // Jika belum ada data, mulai dari 0001
  }

  // Gabungkan awalan "PRD-" dengan nomor urut baru
  $newKodeBarang = "BRG-" . $newNumber;

  return $newKodeBarang;
}

function tambahProduk($data)
{
  global $conn;

  // Generate kode barang baru
  $kode_barang = generateKodeBarang();
  $nama_barang = htmlspecialchars($data["nama_barang"]);
  $expired = !empty($data["expired"]) ? htmlspecialchars($data["expired"]) : 'NULL';
  $harga = floatval(str_replace(',', '', $data["harga"]));
  $stok = htmlspecialchars($data["stok"]);
  $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan nama file

  if (!$gambar) {
    return false;
  }

  $query = "INSERT INTO barang (kode_barang, nama_barang, expired, harga, stok, gambar) 
              VALUES ('$kode_barang', '$nama_barang', $expired, '$harga', '$stok', '$gambar')";

  mysqli_query($conn, $query);

  return mysqli_affected_rows($conn);
}

function input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function register($conn)
{
  $nama = $no_hp = $email = $password = $confirm_password = "";
  $nama_err = $no_hp_err = $email_err = $password_err = $confirm_password_err = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // Validasi nama
    if (empty(input($_POST["nama"]))) {
      $nama_err = "Please enter a name.";
    } else {
      $nama = input($_POST["nama"]);
    }

    // Validasi nomor handphone
    if (empty(input($_POST["no_hp"]))) {
      $no_hp_err = "Please enter a phone number.";
    } else {
      $no_hp = input($_POST["no_hp"]);
      if (!ctype_digit($no_hp)) {
        $no_hp_err = "Please enter a valid phone number.";
      }
    }

    // Validasi email
    if (empty(input($_POST["email"]))) {
      $email_err = "Please enter an email.";
    } else {
      $email = input($_POST["email"]);
      // Cek jika email sudah digunakan di database
      $sql = "SELECT id FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "This email is already taken.";
          }
        } else {
          echo "Oops! Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
      }
    }

    // Validasi password
    if (empty(input($_POST["password"]))) {
      $password_err = "Please enter a password.";
    } elseif (strlen(input($_POST["password"])) < 6) {
      $password_err = "Password must have at least 6 characters.";
    } else {
      $password = input($_POST["password"]);
    }

    // Validasi confirm password
    if (empty(input($_POST["confirm_password"]))) {
      $confirm_password_err = "Please confirm password.";
    } else {
      $confirm_password = input($_POST["confirm_password"]);
      if (empty($password_err) && ($password != $confirm_password)) {
        $confirm_password_err = "Password did not match.";
      }
    }

    if (empty($nama_err) && empty($no_hp_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
      $sql = "INSERT INTO users (nama, no_hp, email, password) VALUES (?, ?, ?, ?)";


      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssss", $nama, $no_hp, $email, $param_password);
        $param_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        if (mysqli_stmt_execute($stmt)) {
          echo
          "<script>
                        alert('Registrasi berhasil silahkan login');
                        window.location.href = 'login.php';
                    </script>";
        } else {
          echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }

  return [
    'nama' => $nama,
    'no_hp' => $no_hp,
    'email' => $email,
    'password' => $password,
    'confirm_password' => $confirm_password,
    'nama_err' => $nama_err,
    'no_hp_err' => $no_hp_err,
    'email_err' => $email_err,
    'password_err' => $password_err,
    'confirm_password_err' => $confirm_password_err
  ];
}

function login($conn)
{
  session_start();

  $email = $password = "";
  $email_err = $password_err = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
      $email_err = "Tolong isi email.";
    } else {
      $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
      $password_err = "Tolong isi password.";
    } else {
      $password = trim($_POST["password"]);
    }

    if (empty($email_err) && empty($password_err)) {
      $sql = "SELECT id, nama, email, password, role, session_id FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;

        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);

          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nama, $email, $hashed_password, $role, $session_id);
            if (mysqli_stmt_fetch($stmt)) {
              if (password_verify($password, $hashed_password)) {
                // Generate a new session ID
                $new_session_id = session_id();

                // Check if the user has an active session
                if ($session_id && $session_id !== $new_session_id) {
                  // Optionally, you might want to log out the previous session
                  // Or inform the user
                  echo "<script>alert('Akun Anda sudah login di perangkat lain.')</script>";
                  return;
                }

                // Update the session ID in the database
                $update_sql = "UPDATE users SET session_id = ? WHERE id = ?";
                if ($update_stmt = mysqli_prepare($conn, $update_sql)) {
                  mysqli_stmt_bind_param($update_stmt, "si", $new_session_id, $id);
                  mysqli_stmt_execute($update_stmt);
                  mysqli_stmt_close($update_stmt);
                }

                // Set session variables
                $_SESSION["login"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["nama"] = $nama;
                $_SESSION["role"] = $role;
                $_SESSION["session_id"] = $new_session_id; // Store new session ID

                setcookie("nama", $nama, time() + (86400 * 2), "/"); // Cookie berlaku selama 2 hari

                // Redirect based on user role
                if ($role == 'SuperAdmin') {
                  header("Location: SuperAdmin/Dashboard.php");
                } elseif ($role == 'Staff') {
                  header("Location: Staff/Dashboard.php");
                } elseif ($role == 'Kasir') {
                  header("Location: Kasir/Dashboard.php");
                } elseif ($role == 'User') {
                  header("Location: Pelanggan/Dashboard.php");
                } else {
                  header("Location: ../index.php");
                }
                exit();
              } else {
                $password_err = "Password yang kamu isi tidak valid.";
              }
            }
          } else {
            $email_err = "Tidak menemukan akun dengan email $email.";
          }
        } else {
          echo "<script>alert('Oops, tampaknya ada yang salah, tolong login kembali!')</script>";
        }
        mysqli_stmt_close($stmt);
      }
    }
  }

  return [
    'email' => $email,
    'password' => $password,
    'email_err' => $email_err,
    'password_err' => $password_err
  ];
}

function logout($conn)
{
  session_start();

  // Clear session variables
  $_SESSION = [];
  session_destroy();

  // Update session_id in database to null
  if (isset($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $sql = "UPDATE users SET session_id = NULL WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "i", $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_close($stmt);
    }
  }

  // Redirect to login page or home page
  header("Location: login.php");
  exit();
}

function checkSessionValidity($conn)
{
  session_start();
  if (isset($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $current_session_id = session_id();

    // Ambil session_id dari database
    $sql = "SELECT session_id FROM users WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
      mysqli_stmt_bind_param($stmt, "i", $id);
      mysqli_stmt_execute($stmt);
      mysqli_stmt_bind_result($stmt, $session_id);
      mysqli_stmt_fetch($stmt);
      mysqli_stmt_close($stmt);

      // Periksa apakah session_id cocok
      if ($session_id !== $current_session_id) {
        // Jika tidak cocok, logout pengguna
        session_unset();
        session_destroy();
        header('Location: ./login.php');
        exit();
      }
    }
  }
}
// Fungsi untuk mendapatkan total pelanggan
function getTotalPelanggan()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'User'");
  $row = mysqli_fetch_assoc($result);
  return $row['total'];
}

// Fungsi untuk mendapatkan total kasir
function getTotalKasir()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'Kasir'");
  $row = mysqli_fetch_assoc($result);
  return $row['total'];
}

// Fungsi untuk mendapatkan total staff
function getTotalStaff()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role = 'Staff'");
  $row = mysqli_fetch_assoc($result);
  return $row['total'];
}

// Fungsi untuk mendapatkan total produk
function getTotalProduk()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM barang");
  $row = mysqli_fetch_assoc($result);
  return $row['total'];
}

// Fungsi untuk mendapatkan total transaksi
function getTotalTransaksi()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
  $row = mysqli_fetch_assoc($result);
  return $row['total'];
}

// Fungsi untuk mendapatkan total penghasilan
function getTotalPenghasilan()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT SUM(total_harga) as total_penghasilan FROM transaksi");
  $row = mysqli_fetch_assoc($result);
  return $row['total_penghasilan'];
}

// Fungsi untuk mendapatkan produk terlaris
function getProdukTerlaris()
{
  global $conn;
  $query = "SELECT b.nama_barang, SUM(dt.kuantitas) AS total_terjual
                          FROM detail_transaksi dt
                          JOIN barang b ON dt.id_barang = b.id
                          GROUP BY b.id
                          ORDER BY total_terjual DESC
                          LIMIT 3";
  return query($query);
}

// Fungsi untuk mendapatkan kasir teraktif
function getKasirTeraktif()
{
  global $conn;
  $query = "SELECT u.nama AS nama_kasir, COUNT(*) as total_transaksi 
              FROM transaksi t
              JOIN users u ON t.id_kasir = u.id
              WHERE u.role = 'Kasir'
              GROUP BY u.id
              ORDER BY total_transaksi DESC 
              LIMIT 4";
  return query($query);
}

function getPelangganTeraktif()
{
  global $conn;
  $query = "SELECT u.nama AS nama_pelanggan, COUNT(*) as total_transaksi 
              FROM transaksi t
              JOIN users u ON t.id_user = u.id
              WHERE u.role = 'User'
              GROUP BY u.id
              ORDER BY total_transaksi DESC 
              LIMIT 4";
  return query($query);
}

function sendVerificationEmail($email, $code)
{
  $mail = new PHPMailer(true);
  try {
    //Server settings
    $mail->isSMTP();                                            // Set mailer to use SMTP
    $mail->Host       = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
    $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
    $mail->Username   = 'abhisuryanugroho0@gmail.com';          // SMTP username
    $mail->Password   = 'secret';                             // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption, `ssl` also accepted
    $mail->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mail->setFrom('abhisuryanugroho0@gmail.com', 'BiShop');
    $mail->addAddress($email);                                  // Add a recipient

    // Content
    $mail->isHTML(true);                                        // Set email format to HTML
    $mail->Subject = 'Kode Verifikasi Reset Password';
    $mail->Body    = "Kode verifikasi Anda adalah: <strong>$code</strong>";
    $mail->AltBody = "Kode verifikasi Anda adalah: $code";

    $mail->send();
    return true;  // Email berhasil dikirim
  } catch (Exception $e) {
    // Log error or handle it as needed
    return false; // Gagal mengirim email
  }
}



function getUserByEmail($conn, $email)
{
  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
  }
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();
  return $result->fetch_assoc();
}

function saveVerificationCode($conn, $email, $code)
{
  $sql = "INSERT INTO password_reset_codes (email, verification_code) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE verification_code = VALUES(verification_code), created_at = NOW(), is_used = 0";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
  }
  $stmt->bind_param("ss", $email, $code);
  $stmt->execute();
}

function getVerificationCode($conn, $email)
{
  $sql = "SELECT verification_code FROM password_reset_codes 
            WHERE email = ? AND is_used = 0 AND created_at >= (NOW() - INTERVAL 15 MINUTE)";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
  }
  $stmt->bind_param("s", $email);
  $stmt->execute();

  // Inisialisasi variabel $code
  $code = null;

  // Bind hasil query ke variabel $code
  $stmt->bind_result($code);
  $stmt->fetch();
  return $code;
}

function deleteVerificationCode($conn, $email)
{
  $sql = "UPDATE password_reset_codes SET is_used = 1 WHERE email = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
  }
  $stmt->bind_param("s", $email);
  $stmt->execute();
}

function updatePassword($conn, $email, $password)
{
  $hashed_password = password_hash($password, PASSWORD_DEFAULT);
  $sql = "UPDATE users SET password = ? WHERE email = ?";
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
  }
  $stmt->bind_param("ss", $hashed_password, $email);
  $stmt->execute();
}
function getJadwalKaryawan()
{
  global $conn;
  $result = mysqli_query($conn, "SELECT users.nama AS nama_karyawan, users.role AS role, tanggal, jam_masuk, jam_keluar, jadwal_karyawan.*
                                    FROM jadwal_karyawan 
                                    JOIN users ON jadwal_karyawan.id_karyawan = users.id");
  $rows = [];
  while ($row = mysqli_fetch_assoc($result)) {
    $rows[] = $row;
  }
  return $rows;
}

function getKaryawanByRole($roles)
{
  global $conn;
  $rolePlaceholders = implode(",", array_fill(0, count($roles), '?'));
  $stmt = $conn->prepare("
        SELECT id, nama AS nama_karyawan, role AS role_name
        FROM users
        WHERE role IN ($rolePlaceholders)
    ");
  $stmt->bind_param(str_repeat('s', count($roles)), ...$roles);
  $stmt->execute();
  $result = $stmt->get_result();
  $rows = [];
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  return $rows;
}

function tambahJadwalKaryawan($data)
{
  global $conn;
  $id_karyawan = htmlspecialchars($data["id_karyawan"]);
  $tanggal = htmlspecialchars($data["tanggal"]);
  $jam_masuk = htmlspecialchars($data["jam_masuk"]);
  $jam_keluar = htmlspecialchars($data["jam_keluar"]);

  $query = "INSERT INTO jadwal_karyawan (id_karyawan, tanggal, jam_masuk, jam_keluar) 
              VALUES ('$id_karyawan', '$tanggal', '$jam_masuk', '$jam_keluar')";
  mysqli_query($conn, $query);

  return mysqli_affected_rows($conn);
}
