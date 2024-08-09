<?php
$conn = mysqli_connect('localhost', 'root', '', 'kasir');

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

function ubah($data)
{
  global $conn;

  $nama_siswa = htmlspecialchars($data["nama_siswa"]);
  $nisn = htmlspecialchars($data["nisn"]);
  $nama_jurusan = htmlspecialchars($data["nama_jurusan"]);
  $kelas = htmlspecialchars($data["kelas"]);
  $gambarLama = htmlspecialchars($data["gambarLama"]); // Nama gambar lama dari form input hidden
  $id = intval($data["id"]); // Mendapatkan nilai id dari $data dan pastikan tipe datanya integer

  // Cek apakah user memilih gambar baru atau tidak
  if (!empty($_FILES['gambar']) && $_FILES['gambar']['error'] !== 4) {
    $gambar = upload();
    if ($gambar === false) {
      // Upload gagal, handle error atau return false
      return false;
    }
  } else {
    $gambar = $gambarLama;
  }

  // Menggunakan prepared statements untuk mencegah SQL injection
  $stmt = $conn->prepare("UPDATE siswa SET nama_siswa = ?, nisn = ?, nama_jurusan = ?, kelas = ?, gambar = ? WHERE id = ?");
  if (!$stmt) {
    return false;
  }
  // Menggunakan tipe data yang sesuai untuk bind_param
  $stmt->bind_param("sssssi", $nama_siswa, $nisn, $nama_jurusan, $kelas, $gambar, $id);

  $stmt->execute();
  $affected_rows = $stmt->affected_rows;

  $stmt->close();

  return $affected_rows;
}

function editProduk($data, $id)
{
  global $conn;

  $kode_barang = htmlspecialchars($data["kode_barang"]);
  $nama_barang = htmlspecialchars($data["nama_barang"]);
  $expired = htmlspecialchars($data["expired"]);
  $harga = htmlspecialchars($data["harga"]);
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
                expired = '$expired',
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
  $expired = htmlspecialchars($data["expired"]);
  $harga = htmlspecialchars($data["harga"]);
  $stok = htmlspecialchars($data["stok"]);
  $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan nama file

  if (!$gambar) {
    return false;
  }

  $query = "INSERT INTO barang (kode_barang, nama_barang, expired, harga, stok, gambar) 
              VALUES ('$kode_barang', '$nama_barang', '$expired', '$harga', '$stok', '$gambar')";

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
                        alert('User berhasil ditambahkan');
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
      $sql = "SELECT id, nama, email, password, role FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_email);
        $param_email = $email;

        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);

          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nama, $email, $hashed_password, $role);
            if (mysqli_stmt_fetch($stmt)) {
              if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION["login"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["nama"] = $nama;
                $_SESSION["role"] = $role;
                setcookie("nama", $nama, time() + (86400 * 2), "/"); // Cookie berlaku selama 2 hari

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
