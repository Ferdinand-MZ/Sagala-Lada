<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['admin']);
?>


<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
if (!isset($_GET['id'])) { header("Location: pesanan.php"); exit; }
$id = (int)$_GET['id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id_pesanan = $id"));
if (!$pesanan || $pesanan['status'] != 'Pending') { header("Location: pesanan.php"); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><title>Edit Pesanan #<?= str_pad($id,4,'0',STR_PAD_LEFT) ?></title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'pesanan'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="pesanan.php">Pesanan</a></li>
            <li class="breadcrumb-item text-sm text-dark active">Edit</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Edit Pesanan #<?= str_pad($id,4,'0',STR_PAD_LEFT) ?></h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Edit Data Pesanan</h6>
              </div>
            </div>
            <div class="card-body px-4 pb-4">
              <form action="/server/controller/pesanan/pesananController.php" method="POST">
                <input type="hidden" name="edit_pesanan" value="1">
                <input type="hidden" name="id_pesanan" value="<?= $id ?>">
                <div class="mb-3">
                  <label class="form-label">Nama Pelanggan</label>
                  <input type="text" name="nama_pelanggan" class="form-control" value="<?= htmlspecialchars($pesanan['nama_pelanggan']) ?>" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">No. Meja</label>
                  <input type="text" name="no_meja" class="form-control" value="<?= $pesanan['no_meja'] ?>" placeholder="Kosongkan jika tidak ada">
                </div>
                <div class="text-end">
                  <a href="pesanan.php" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn bg-gradient-success">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/fixed-plugin.php'; ?>
  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>