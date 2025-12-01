<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['admin']);
?>

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
$id_pesanan = (int)$_GET['id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id_pesanan"));
if (!$pesanan || $pesanan['status'] != 'Pending') {
    header("Location: pesanan.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Pesanan #<?= str_pad($id_pesanan, 4, '0', STR_PAD_LEFT); ?></title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-xxxxxxxxxxxxxxxx"></script>
</head>
<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'pesanan'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
      <div class="container-fluid py-1 px-3">
        <div>
          <h6 class="font-weight-bolder mb-0">
            Pesanan #<?= str_pad($id_pesanan, 4, '0', STR_PAD_LEFT); ?> - <?= htmlspecialchars($pesanan['nama_pelanggan']); ?>
            <?= $pesanan['no_meja'] ? " (Meja {$pesanan['no_meja']})" : ''; ?>
          </h6>
        </div>
        <a href="pesanan.php" class="btn btn-sm btn-secondary">Kembali</a>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <!-- Tambah Item -->
      <div class="card mb-4">
        <div class="card-body">
          <form action="/server/controller/pesanan/pesananController.php" method="POST" class="row g-3">
            <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
            <input type="hidden" name="tambah_item" value="1">
            <div class="col-md-7">
              <select name="id_menu" class="form-control" required>
                <option value="">Pilih Menu</option>
                <?php
                $menus = mysqli_query($koneksi, "SELECT * FROM menu ORDER BY nama_menu");
                while ($m = mysqli_fetch_assoc($menus)) {
                    echo "<option value='{$m['id_menu']}'>{$m['nama_menu']} - Rp " . number_format($m['harga'],0,',','.') . "</option>";
                }
                ?>
              </select>
            </div>
            <div class="col-md-3">
              <input type="number" name="jumlah" class="form-control" value="1" min="1" required>
            </div>
            <div class="col-md-2">
              <button type="submit" class="btn bg-gradient-success w-100">Tambah</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Daftar Item -->
      <div class="card">
        <div class="card-body">
          <h5>Total: <strong class="text-primary">Rp <?= number_format($pesanan['total'],0,',','.'); ?></strong></h5>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>Menu</th>
                  <th width="100" class="text-center">Jumlah</th>
                  <th width="150" class="text-end">Subtotal</th>
                  <th width="50"></th>
                </tr>
              </thead>
              <tbody>
                <?php
                $items = mysqli_query($koneksi, "SELECT dp.*, m.nama_menu FROM detail_pesanan dp JOIN menu m ON dp.id_menu = m.id_menu WHERE dp.id_pesanan = $id_pesanan");
                while ($i = mysqli_fetch_assoc($items)):
                ?>
                <tr>
                  <td><?= htmlspecialchars($i['nama_menu']) ?></td>
                  <td class="text-center"><?= $i['jumlah'] ?>x</td>
                  <td class="text-end">Rp <?= number_format($i['subtotal'],0,',','.') ?></td>
                  <td>
                    <a href="/server/controller/pesanan/pesananController.php?hapus_item=<?= $i['id_detail'] ?>&id_pesanan=<?= $id_pesanan ?>" 
                       class="text-danger" onclick="return confirm('Hapus item ini?')">
                      <i class="material-symbols-rounded">delete</i>
                    </a>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <div class="text-end mt-4">
  <button class="btn btn-danger me-2" onclick="if(confirm('Batalkan pesanan ini?')) location.href='/server/controller/pesanan/pesananController.php?batal=<?= $id_pesanan ?>'">
    Batalkan Pesanan
  </button>
  <?php if ($pesanan['total'] > 0): ?>
  <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#bayarModal">
    Bayar Sekarang
  </button>
  <?php endif; ?>
</div>
</div>
</div>

<!-- Modal Bayar (diperbarui) -->
<div class="modal fade" id="bayarModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Proses Pembayaran</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <h4>Total: Rp <?= number_format($pesanan['total'],0,',','.'); ?></h4>

        <div class="row mt-4">
          <div class="col-md-6">
            <div class="card h-100 border-primary cursor-pointer" onclick="pilihMetode('manual')">
              <div class="card-body text-center">
                <i class="fas fa-money-bill-wave fa-3x text-primary"></i>
                <h5 class="mt-3">Cash / Transfer / E-Wallet</h5>
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <div class="card h-100 border-success cursor-pointer" onclick="pilihMetode('qris')">
              <div class="card-body text-center">
                <i class="fas fa-qrcode fa-3x text-success"></i>
                <h5 class="mt-3">Bayar dengan QRIS (Midtrans)</h5>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Manual -->
        <div id="formManual" style="display:none;">
          <form action="/server/controller/pesanan/pesananController.php" method="POST">
            <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
            <input type="hidden" name="proses_bayar" value="1">
            <input type="hidden" name="metode_bayar" value="Cash">
            <div class="mt-3">
              <label>Jumlah Bayar</label>
              <input type="text" name="jumlah_bayar" class="form-control rupiah" value="<?= number_format($pesanan['total'],0,',','.'); ?>" required>
            </div>
            <div class="mt-3 text-end">
              <button type="submit" class="btn btn-primary">Selesaikan Pembayaran</button>
            </div>
          </form>
        </div>

        <!-- Form QRIS Midtrans -->
        <div id="formQRIS" style="display:none;">
          <form action="/server/controller/pesanan/midtrans_snap.php" method="POST">
            <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
            <input type="hidden" name="total" value="<?= $pesanan['total'] ?>">
            <input type="hidden" name="nama_pelanggan" value="<?= htmlspecialchars($pesanan['nama_pelanggan']) ?>">
            <div class="text-center">
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fas fa-qrcode"></i> Generate QRIS
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/fixed-plugin.php'; ?>
  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
  <script>
function pilihMetode(metode) {
  document.getElementById('formManual').style.display = 'none';
  document.getElementById('formQRIS').style.display = 'none';
  document.getElementById('form'+(metode==='qris'?'QRIS':'Manual')).style.display = 'block';
}
</script>
</body>
</html>