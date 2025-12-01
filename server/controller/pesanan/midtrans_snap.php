<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/koneksi.php';

// Midtrans Config
\Midtrans\Config::$serverKey = getenv('MIDTRANS_SERVER_KEY');
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized  = true;
\Midtrans\Config::$is3ds        = true;
\Midtrans\Config::$curlOptions = [CURLOPT_SSL_VERIFYHOST => 0, CURLOPT_SSL_VERIFYPEER => 0];

$id_pesanan = (int)$_POST['id_pesanan'];
$total      = (int)$_POST['total'];
$pelanggan  = $_POST['nama_pelanggan'];
$no_meja    = $_POST['no_meja'] ?? '';

$order_id = 'ORD-'.str_pad($id_pesanan,5,'0',STR_PAD_LEFT).'-'.time();

$transaction = [
    'transaction_details' => [
        'order_id'     => $order_id,
        'gross_amount' => $total,
    ],
    'customer_details' => ['first_name' => $pelanggan],
    'enabled_payments' => ['qris', 'gopay', 'shopeepay', 'dana'],
];

$snapToken = \Midtrans\Snap::getSnapToken($transaction);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QRIS - Pesanan #<?= str_pad($id_pesanan,4,'0',STR_PAD_LEFT) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #f8f9fa; padding: 20px 0; font-family: system-ui, sans-serif; }
    .card { max-width: 400px; margin: 0 auto; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .header { background: #198754; color: white; padding: 1.5rem; text-align: center; }
    .amount { font-size: 2.2rem; font-weight: 800; color: #198754; }
    .back-btn { border-radius: 12px; }
  </style>
</head>
<body>

<div class="card">
  <div class="header">
    <h4>Pesanan #<?= str_pad($id_pesanan,4,'0',STR_PAD_LEFT) ?></h4>
    <small><?= htmlspecialchars($pelanggan) ?> <?= $no_meja ? "• Meja $no_meja" : '' ?></small>
  </div>

  <div class="card-body text-center py-5">
    <div class="amount mb-3">Rp <?= number_format($total,0,',','.') ?></div>
    <p class="text-muted mb-4">Scan QRIS dengan e-wallet / mobile banking</p>

    <!-- QRIS langsung muncul dari Midtrans -->
    <div id="snap-container"></div>
  </div>

  <div class="card-footer bg-white border-0 text-center py-4">
    <a href="/server/view/pesanan/tambah_item.php?id=<?= $id_pesanan ?>" 
       class="btn btn-outline-secondary btn-lg back-btn">
       Kembali ke Pesanan
    </a>
  </div>
</div>

<!-- Midtrans Snap -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" 
        data-client-key="<?= $_ENV['MIDTRANS_CLIENT_KEY'] ?? 'Mid-client-QsPTs-AbFfZhH6Hw' ?>">
</script>
<script>
  snap.pay('<?= $snapToken ?>', {
    onSuccess: function() {
      alert('Pembayaran berhasil!');
      location.href = '/server/view/pesanan/tambah_item.php?id=<?= $id_pesanan ?>&paid=1';
    },
    onPending: function() {
      alert('Menunggu pembayaran...');
    },
    onClose: function() {
      // tombol kembali tetap ada
    }
  });
</script>

</body>
</html>