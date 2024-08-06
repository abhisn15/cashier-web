<?php

$conn = mysqli_connect('localhost', 'root', '', 'kasir');

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

function query($query, $params = [])
{
  global $conn;
  $stmt = mysqli_prepare($conn, $query);
  if (!$stmt) {
    die("Query Error: " . mysqli_error($conn));
  }

  if ($params) {
    $types = str_repeat('s', count($params));
    mysqli_stmt_bind_param($stmt, $types, ...$params);
  }

  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($result == false) {
    die("Execute Error: " . mysqli_stmt_error($stmt));
  }

  if (stripos($query, 'SELECT') === 0) {
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $rows[] = $row;
    }
    mysqli_stmt_close($stmt);
    return $rows;
  } else {
    $affacted_rows = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    return $affacted_rows;
  }
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
  $namaFileBaru = uniqid() . '.' . $ekstensiGambar;

  // tentukan alamat penyimpanan file secara absolut
  $alamatSimpan = '../img/' . $namaFileBaru;

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

function tambahPegawai($data)
{
  global $conn;
  $nama_siswa = htmlspecialchars($data["nama_siswa"]);
  $nisn = htmlspecialchars($data["nisn"]);
  $nama_jurusan = htmlspecialchars($data["nama_jurusan"]);
  $kelas = htmlspecialchars($data["kelas"]);
  $gambar = upload(); // Menggunakan fungsi upload untuk mendapatkan nama file

  if (!$gambar) {
    return false;
  }

  $query = "INSERT INTO siswa (nama_siswa, nisn, nama_jurusan, kelas, gambar) 
              VALUES ('$nama_siswa', '$nisn', '$nama_jurusan', '$kelas', '$gambar')";

  mysqli_query($conn, $query);

  return mysqli_affected_rows($conn);
}

function hapus($id)
{
  global $conn;
  mysqli_query($conn, "DELETE FROM siswa WHERE id = $id");

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


function cari($keyword)
{
  $query = "SELECT * FROM siswa
                WHERE 
            nama_siswa LIKE '%$keyword%' OR
            kelas LIKE '%$keyword%' OR
            nama_jurusan LIKE '%$keyword%' 
            
    ";
  return query($query);
}

function input($data) {
  return htmlspecialchars(stripslashes(trim($data)));
}

function register($conn)
{
  $email = $password = $confirm_password = "";
  $email_err = $password_err = $confirm_password_err = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validasi nama
    if (empty(input($_POST["nama"]))) {
      $email_err = "Please enter a nama.";
    }

    // Validasi nomor ho
    if (empty(input($_POST["no_hp"]))) {
      $email_err = "Please enter a nomor handphone.";
    }

    // Validasi email
    if (empty(input($_POST["email"]))) {
      $email_err = "Please enter a email.";
    } else {
      $email = input($_POST["email"]);
      // Cek jika user sudah digunakan di database
      $sql = "SELECT id FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);
          if (mysqli_stmt_num_rows($stmt) == 1) {
            $email_err = "Nama ini sudah ada yang punya.";
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
      $password_err = "Password harus berjumlah 6 karakter.";
    } else {
      $password = input($_POST["password"]);
    }

    // Validasi confirm password
    if (empty(input($_POST["confirm_password"]))) {
      $confirm_password_err = "Tolong konfirmasi password.";
    } else {
      $confirm_password = input($_POST["confirm_password"]);
      if (empty($password_err) && ($password != $confirm_password)) {
        $confirm_password_err = "Konfirmasi password belum sesuai.";
      }
    }

    if (empty($email_err) && empty($password_err) && empty($confirm_password_err)) {
      $sql = "INSERT INTO users (nama, no_hp, email, password) VALUES (?, ?, ?, ?)";

      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $nama, $no_hp, $email, $param_password);
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
    'email_err' => $email_err,
    'password_err' => $password_err,
    'confirm_password_err' => $confirm_password_err
  ];
}

function login($conn)
{
  session_start();

  $email = $password = "";
  $username_err = $password_err = "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty(trim($_POST["email"]))) {
      $username_err = "Tolong isi email.";
    } else {
      $email = trim($_POST["email"]);
    }

    if (empty(trim($_POST["password"]))) {
      $password_err = "Tolong isi password.";
    } else {
      $password = trim($_POST["password"]);
    }

    if (empty($username_err) && empty($password_err)) {
      $sql = "SELECT id, email, password, role FROM users WHERE email = ?";
      if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $param_username);
        $param_username = $email;

        if (mysqli_stmt_execute($stmt)) {
          mysqli_stmt_store_result($stmt);

          if (mysqli_stmt_num_rows($stmt) == 1) {
            mysqli_stmt_bind_result($stmt, $id, $nama, $email, $hashed_password, $role);
            if (mysqli_stmt_fetch($stmt)) {
              if (password_verify($password, $hashed_password)) {
                session_start();
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = $id;
                $_SESSION["nama"] = $nama;
                $_SESSION["role"] = $role;
                setcookie("nama", $nama, time() + (86400 * 2), "/"); // Cookie berlaku selama 2 hari

                if ($role == 'Admin') {
                  header("Location: ../library-php-native/admin/Dashboard.php");
                } elseif ($role == 'Anggota') {
                  header("Location: ../library-php-native/user/Dashboard.php");
                } else {
                  header("Location: ../index.php");
                }
                exit();
              } else {
                $password_err = "Password yang kamu isi tidak valid.";
              }
            }
          } else {
            $username_err = "Tidak menemukan akun dengan email $email.";
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
    'username_err' => $username_err,
    'password_err' => $password_err
  ];
}