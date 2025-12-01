<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/koneksi.php';

// Setting Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-xxxxxxxxxxxxxxxx'; // ganti
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$id_pesanan = $_POST['id_pesanan'];
$total      = $_POST['total'];
$pelanggan  = $_POST['nama_pelanggan'];

$transaction = [
    'transaction_details' => [
        'order_id' => 'ORD-'.$id_pesanan.'-'.time(),
        'gross_amount' => (int)$total,
    ],
    'customer_details' => [
        'first_name' => $pelanggan,
    ],
    'enabled_payments' => ['qris', 'gopay', 'shopeepay', 'bank_transfer'],
];

$snapToken = \Midtrans\Snap::getSnapToken($transaction);
?>

<!DOCTYPE html>
<html><head><title>Pembayaran QRIS</title></head><body>
<div style="text-align:center; margin-top:50px;">
  <h3>Scan QRIS untuk pesanan #<?= str_pad($id_pesanan,4,'0',STR_PAD_LEFT) ?></h3>
  <button id="pay-button" class="btn btn-success btn-lg">Tampilkan QRIS</button>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-xxxx"></script>
<script>
document.getElementById('pay-button').onclick = function(){
    snap.pay('<?= $snapToken ?>', {
        onSuccess: function(){ location.href='/server/view/pesanan/tambah_item.php?id=<?= $id_pesanan ?>&paid=1'; },
        onPending: function(){ location.href='/server/view/pesanan/tambah_item.php?id=<?= $id_pesanan ?>&pending=1'; },
        onClose: function(){ alert('Pembayaran dibatalkan'); }
    });
};
</script>
</body></html>