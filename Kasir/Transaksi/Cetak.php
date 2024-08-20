<?php
require '../../functions.php';
session_start();

$username = $_SESSION['nama'];
$role = $_SESSION['role'];

if (!isset($_SESSION['login']) || $_SESSION['login'] !== true || $role !== 'Kasir') {
  header('Location: ../../login.php');
  exit();
}

$id_transaksi = isset($_GET['id']) ? $_GET['id'] : null;

if ($id_transaksi === null) {
  header('Location: ../Transaksi.php');
  exit();
}

$query = "SELECT transaksi.*, users.nama AS nama_kasir, detail_transaksi.kuantitas, barang.nama_barang, barang.harga 
          FROM transaksi 
          INNER JOIN users ON transaksi.id_kasir = users.id
          INNER JOIN detail_transaksi ON transaksi.id = detail_transaksi.id_transaksi
          INNER JOIN barang ON detail_transaksi.id_barang = barang.id
          WHERE transaksi.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id_transaksi);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  header('Location: ../Transaksi.php');
  exit();
}

$transaksi = $result->fetch_all(MYSQLI_ASSOC);
$kembalian = $transaksi[0]['tunai'] - $transaksi[0]['total_harga'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cetak Struk</title>
  <style>
    body {
      font-family: 'Courier New', Courier, monospace;
      width: 58mm;
      /* Sesuaikan dengan lebar kertas printer */
    }

    .receipt {
      width: 100%;
    }

    .center {
      text-align: center;
    }

    .right {
      text-align: right;
    }

    .line {
      border-top: 1px dashed #000;
      margin: 5px 0;
    }

    .bold {
      font-weight: bold;
    }

    .small {
      font-size: 12px;
    }

    .total,
    .tunai,
    .kembalian {
      margin-top: 10px;
    }
  </style>
</head>

<body onload="window.print()">
  <div class="receipt">
    <div class="center bold">
      BiShop<br>
      <span class="small">Jalan Sigma No. 666</span><br>
      <span class="small">Telp: 085314818119</span>
    </div>
    <div class="line"></div>
    <div class="small">
      Tanggal: <?= $transaksi[0]['tanggal_transaksi'] ?><br>
      ID Transaksi: <?= $transaksi[0]['id'] ?><br>
      Kasir: <?= $transaksi[0]['nama_kasir'] ?>
    </div>
    <div class="line"></div>
    <?php foreach ($transaksi as $detail) : ?>
      <div class="small">
        <?= $detail['nama_barang'] ?><br>
        <?= $detail['kuantitas'] ?> x Rp <?= number_format($detail['harga'], 0, ',', '.') ?>
        <span class="right">Rp <?= number_format($detail['kuantitas'] * $detail['harga'], 0, ',', '.') ?></span>
      </div>
    <?php endforeach; ?>
    <div class="line"></div>
    <div class="small total">
      <span class="bold">Total</span>
      <span class="right">Rp <?= number_format($transaksi[0]['total_harga'], 0, ',', '.') ?></span>
    </div>
    <div class="small tunai">
      <span class="bold">Tunai</span>
      <span class="right">Rp <?= number_format($transaksi[0]['tunai'], 0, ',', '.') ?></span>
    </div>
    <div class="small kembalian">
      <span class="bold">Kembalian</span>
      <span class="right">Rp <?= number_format($kembalian, 0, ',', '.') ?></span>
    </div>
    <div class="line"></div>
    <div class="center small">
      Terima Kasih<br>
      Selamat Belanja Kembali
    </div>
    <div class="line"></div>
    <div class="center small">
      Layanan Konsumen:<br>
      kontak@bishop.co.id
    </div>
  </div>
</body>

</html>