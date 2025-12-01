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
  <style>
.cursor-pointer { cursor: pointer; transition: all 0.2s; }
.hover-lift:hover { transform: translateY(-4px); }
.icon-circle {
  width: 70px; height: 70px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.border-3 { border-width: 3px !important; }
</style>
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
<!-- Modal Bayar - Versi Rapi & Profesional -->
<div class="modal fade" id="bayarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h4 class="modal-title fw-bold text-dark">
          Pembayaran Pesanan #<?= str_pad($id_pesanan, 4, '0', STR_PAD_LEFT) ?>
        </h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body pt-3">
        <!-- Total Tagihan -->
        <div class="text-center mb-4">
          <p class="text-muted mb-1">Total Tagihan</p>
          <h2 class="fw-bold text-primary">Rp <?= number_format($pesanan['total'], 0, ',', '.'); ?></h2>
          <small class="text-muted"><?= htmlspecialchars($pesanan['nama_pelanggan']) ?> 
            <?= $pesanan['no_meja'] ? " • Meja {$pesanan['no_meja']}" : '' ?>
          </small>
        </div>

        <hr class="my-4">

        <!-- Pilih Metode Pembayaran -->
        <p class="text-center text-muted mb-4 fw-500">Pilih metode pembayaran</p>

        <div class="row g-4">
          <!-- Manual (Cash/Transfer/E-Wallet) -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-light cursor-pointer hover-lift" 
                 onclick="pilihMetode('manual')" id="card-manual">
              <div class="card-body text-center py-5">
                <div class="icon-circle bg-primary bg-opacity-10 text-primary mb-3 mx-auto">
                  <i class="fas fa-money-bill-wave fa-2x"></i>
                </div>
                <h5 class="fw-bold">Tunai / Transfer</h5>
                <p class="text-muted small mb-0">Cash, Transfer Bank, E-Wallet</p>
              </div>
            </div>
          </div>

          <!-- QRIS Midtrans -->
          <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-light cursor-pointer hover-lift" 
                 onclick="pilihMetode('qris')" id="card-qris">
              <div class="card-body text-center py-5">
                <div class="icon-circle bg-success bg-opacity-10 text-success mb-3 mx-auto">
                  <i class="fas fa-qrcode fa-2x"></i>
                </div>
                <h5 class="fw-bold">QRIS (All Payment)</h5>
                <p class="text-muted small mb-0">GoPay, ShopeePay, DANA, OVO, dll</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Manual -->
        <div id="formManual" class="mt-4 p-4 bg-white rounded-3 border" style="display:none;">
          <form action="/server/controller/pesanan/pesananController.php" method="POST">
            <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
            <input type="hidden" name="proses_bayar" value="1">

            <div class="mb-3">
              <label class="form-label fw-bold">Metode Pembayaran</label>
              <select name="metode_bayar" class="form-select form-select-lg" required>
                <option value="Cash">Cash (Tunai)</option>
                <option value="Transfer">Transfer Bank</option>
                <option value="E-Wallet">E-Wallet</option>
              </select>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Jumlah Dibayar</label>
              <input type="text" name="jumlah_bayar" class="form-control form-control-lg text-end fw-bold rupiah" 
                     value="<?= number_format($pesanan['total'],0,',','.'); ?>" required>
            </div>

            <div class="d-grid">
              <button type="submit" class="btn btn-primary btn-lg rounded-pill">
                <i class="fas fa-check me-2"></i> Selesaikan Pembayaran
              </button>
            </div>
          </form>
        </div>

        <!-- Form QRIS -->
        <div id="formQRIS" class="mt-4 p-4 bg-white rounded-3 border text-center" style="display:none;">
          <form action="/server/controller/pesanan/midtrans_snap.php" method="POST">
            <input type="hidden" name="id_pesanan" value="<?= $id_pesanan ?>">
            <input type="hidden" name="total" value="<?= $pesanan['total'] ?>">
            <input type="hidden" name="nama_pelanggan" value="<?= htmlspecialchars($pesanan['nama_pelanggan']) ?>">
            <input type="hidden" name="no_meja" value="<?= $pesanan['no_meja'] ?>">

            <div class="mb-4">
              <i class="fas fa-qrcode fa-4x text-success"></i>
              <h5 class="mt-3 fw-bold">Bayar dengan QRIS</h5>
              <p class="text-muted">Semua e-wallet & mobile banking</p>
            </div>

            <button type="submit" class="btn btn-success btn-lg rounded-pill px-5">
              <i class="fas fa-arrow-right me-2"></i> Lanjut ke QRIS
            </button>
          </form>
        </div>
      </div>

      <div class="modal-footer border-0 justify-content-center pb-4">
        <button type="button" class="btn btn-link text-muted" data-bs-dismiss="modal">Batal</button>
      </div>
    </div>
  </div>
</div>

<!-- Script untuk pilih metode -->
<script>
function pilihMetode(metode) {
  // Reset border
  document.getElementById('card-manual').classList.remove('border-primary', 'shadow');
  document.getElementById('card-qris').classList.remove('border-success', 'shadow-lg');
  
  // Hide all forms
  document.getElementById('formManual').style.display = 'none';
  document.getElementById('formQRIS').style.display = 'none';

  if (metode === 'manual') {
    document.getElementById('card-manual').classList.add('border-primary', 'shadow');
    document.getElementById('formManual').style.display = 'block';
  } else if (metode === 'qris') {
    document.getElementById('card-qris').classList.add('border-success', 'shadow-lg');
    document.getElementById('formQRIS').style.display = 'block';
  }
}
</script>

<!-- CSS Tambahan (taruh di <head> atau file CSS) -->


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