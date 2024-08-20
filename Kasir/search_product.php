<?php
require '../functions.php';
header('Content-Type: application/json');

if (isset($_GET['barcode'])) {
  $barcode = $_GET['barcode'];

  // Query untuk mencari produk berdasarkan barcode
  $stmt = $conn->prepare("SELECT * FROM barang WHERE kode_barang = ?");
  $stmt->bind_param('s', $barcode);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    echo json_encode([
      'success' => true,
      'product_id' => $product['id']
    ]);
  } else {
    echo json_encode([
      'success' => false
    ]);
  }
} else {
  echo json_encode([
    'success' => false
  ]);
}
