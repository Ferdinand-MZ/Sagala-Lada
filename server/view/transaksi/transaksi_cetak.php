<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';

if (!isset($_GET['id'])) {
    die("ID transaksi tidak ditemukan.");
}

$id_pesanan = (int)$_GET['id'];

// Ambil data pesanan + pembayaran
$sql = "SELECT p.*, py.metode_bayar, py.jumlah_bayar, py.tanggal_bayar
        FROM pesanan p
        JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE p.id_pesanan = ? AND p.status = 'Selesai'
        LIMIT 1";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Transaksi tidak ditemukan atau belum selesai.");
}

$pesanan = $result->fetch_assoc();
$no_nota = str_pad($pesanan['id_pesanan'], 6, '0', STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nota #<?= $no_nota ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 20px;
      background: #fff;
      color: #000;
    }
    .nota {
      max-width: 80mm;
      margin: 0 auto;
      border: 2px dashed #000;
      padding: 15px;
      background: #fff;
    }
    .header {
      text-align: center;
      margin-bottom: 15px;
    }
    .header h1 {
      font-size: 20px;
      margin: 0;
      font-weight: bold;
    }
    .header p {
      margin: 5px 0;
      font-size: 12px;
    }
    .info {
      font-size: 12px;
      margin-bottom: 15px;
    }
    .info table {
      width: 100%;
    }
    .info td {
      padding: 3px 0;
    }
    .items {
      width: 100%;
      border-collapse: collapse;
      margin: 10px 0;
      font-size: 12px;
    }
    .items th, .items td {
      text-align: left;
      padding: 5px 0;
    }
    .items th {
      border-bottom: 1px dashed #000;
    }
    .total {
      margin-top: 15px;
      font-size: 14px;
      text-align: right;
    }
    .total tr td {
      padding: 5px 0;
    }
    .footer {
      text-align: center;
      margin-top: 20px;
      font-size: 11px;
    }
    @media print {
      body { padding: 5mm; }
      .nota { border: none; }
      @page { margin: 0; }
    }
  </style>
</head>
<body onload="window.print()">

<div class="nota">
  <div class="header">
    <h1>SAGALA LADA</h1>
    <p>Jl. Contoh No. 123, Kota</p>
    <p>Telp: 0812-3456-7890</p>
  </div>

  <div class="info">
    <table>
      <tr>
        <td>Nota</td>
        <td>: #<?= $no_nota ?></td>
      </tr>
      <tr>
        <td>Tanggal</td>
        <td>: <?= date('d/m/Y H:i', strtotime($pesanan['tanggal_bayar'])) ?></td>
      </tr>
      <tr>
        <td>Kasir</td>
        <td>: <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></td>
      </tr>
      <tr>
        <td>Pelanggan</td>
        <td>: <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td>
      </tr>
      <?php if ($pesanan['no_meja']): ?>
      <tr>
        <td>Meja</td>
        <td>: <?= htmlspecialchars($pesanan['no_meja']) ?></td>
      </tr>
      <?php endif; ?>
    </table>
  </div>

  <hr style="border: 1px dashed #000; margin: 10px 0;">

  <table class="items">
    <thead>
      <tr>
        <th>Item</th>
        <th class="text-center">Qty</th>
        <th class="text-right">Harga</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $detail_sql = "SELECT m.nama_menu, d.jumlah, d.subtotal 
                     FROM detail_pesanan d 
                     JOIN menu m ON d.id_menu = m.id_menu 
                     WHERE d.id_pesanan = ?";
      $stmt2 = $koneksi->prepare($detail_sql);
      $stmt2->bind_param("i", $id_pesanan);
      $stmt2->execute();
      $detail_result = $stmt2->get_result();

      while ($item = $detail_result->fetch_assoc()): ?>
      <tr>
        <td><?= htmlspecialchars($item['nama_menu']) ?></td>
        <td class="text-center"><?= $item['jumlah'] ?></td>
        <td class="text-right">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <hr style="border: 1px dashed #000; margin: 10px 0;">

  <table class="total">
    <tr>
      <td><strong>Total</strong></td>
      <td><strong>Rp <?= number_format($pesanan['total'], 0, ',', '.') ?></strong></td>
    </tr>
    <tr>
      <td>Bayar (<?= $pesanan['metode_bayar'] ?>)</td>
      <td>Rp <?= number_format($pesanan['jumlah_bayar'], 0, ',', '.') ?></td>
    </tr>
    <?php if ($pesanan['jumlah_bayar'] > $pesanan['total']): ?>
    <tr>
      <td>Kembali</td>
      <td>Rp <?= number_format($pesanan['jumlah_bayar'] - $pesanan['total'], 0, ',', '.') ?></td>
    </tr>
    <?php endif; ?>
  </table>

  <div class="footer">
    <p>=== Terima Kasih ===</p>
    <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan</p>
  </div>
</div>

</body>
</html>