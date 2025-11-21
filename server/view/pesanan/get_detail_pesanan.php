<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['admin']);
?>


<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
$id = (int)$_GET['id'];

$q = mysqli_query($koneksi, "SELECT p.*, py.metode_bayar FROM pesanan p
    LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
    WHERE p.id_pesanan = $id");
$pesanan = mysqli_fetch_assoc($q);

if (!$pesanan) {
    die('<p class="text-danger text-center">Pesanan tidak ditemukan.</p>');
}
?>

<div class="row mt-3">
  <div class="col-6">
    <table class="table table-sm table-borderless">
      <tr><td width="120"><strong>Pelanggan</strong></td><td>: <?= htmlspecialchars($pesanan['nama_pelanggan']) ?></td></tr>
      <tr><td><strong>No. Meja</strong></td><td>: <?= $pesanan['no_meja'] ?: '-' ?></td></tr>
      <tr><td><strong>Tanggal</strong></td><td>: <?= date('d/m/Y H:i', strtotime($pesanan['tanggal_pesan'])) ?></td></tr>
      <tr><td><strong>Status</strong></td>
          <td>: <span class="badge badge-sm <?= $pesanan['status']=='Selesai'?'bg-gradient-success':($pesanan['status']=='Dibatalkan'?'bg-gradient-danger':'bg-gradient-warning') ?>">
                <?= $pesanan['status'] ?>
                </span>
          </td>
      </tr>
      <?php if($pesanan['metode_bayar']): ?>
      <tr><td><strong>Metode Bayar</strong></td><td>: <?= ucwords(str_replace('_', ' ', $pesanan['metode_bayar'])) ?></td></tr>
      <?php endif; ?>
    </table>
  </div>
  <div class="col-6 text-end">
    <h4 class="mt-3">Total: <strong class="text-primary">Rp <?= number_format($pesanan['total'],0,',','.') ?></strong></h4>
  </div>
</div>

<hr class="my-4">

<h6 class="mb-3">Daftar Menu:</h6>
<table class="table table-bordered align-middle">
  <thead class="table-light">
    <tr>
      <th>Menu</th>
      <th width="100" class="text-center">Jumlah</th>
      <th width="140" class="text-end">Harga Satuan</th>
      <th width="140" class="text-end">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    <?php
    // Fix: Ambil harga dari tabel menu, bukan dari detail_pesanan
    $q2 = mysqli_query($koneksi, "SELECT m.nama_menu, dp.jumlah, dp.subtotal, m.harga as harga_satuan
        FROM detail_pesanan dp
        JOIN menu m ON dp.id_menu = m.id_menu
        WHERE dp.id_pesanan = $id");
    
    $total_detail = 0;
    while($d = mysqli_fetch_assoc($q2)):
      $total_detail += $d['subtotal'];
    ?>
    <tr>
      <td><?= htmlspecialchars($d['nama_menu']) ?></td>
      <td class="text-center"><?= $d['jumlah'] ?>x</td>
      <td class="text-end">Rp <?= number_format($d['harga_satuan'],0,',','.') ?></td>
      <td class="text-end">Rp <?= number_format($d['subtotal'],0,',','.') ?></td>
    </tr>
    <?php endwhile; ?>
  </tbody>
  <tfoot class="table-light">
    <tr>
      <th colspan="3" class="text-end">Total:</th>
      <th class="text-end">Rp <?= number_format($total_detail,0,',','.') ?></th>
    </tr>
  </tfoot>
</table>