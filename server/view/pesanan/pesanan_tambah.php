<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/template/material/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/template/material/assets/img/favicon.png">
  <title>Tambah Pesanan</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
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
            <li class="breadcrumb-item text-sm text-dark active">Tambah Pesanan</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Pesanan Baru</h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Form Pesanan Baru</h6>
              </div>
            </div>
            <div class="card-body px-4 pb-4">
              <form action="/server/controller/pesanan/pesananController.php" method="POST">
                <input type="hidden" name="tambah_pesanan" value="1">
                <div class="mb-3">
                  <label class="form-label">Nama Pelanggan</label>
                  <input type="text" name="nama_pelanggan" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                  <label class="form-label">No. Meja (opsional)</label>
                  <input type="text" name="no_meja" class="form-control" placeholder="Contoh: A1, B3">
                </div>
                <div class="text-end">
                  <a href="pesanan.php" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn bg-gradient-primary">Buat Pesanan</button>
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
  <script src="/assets/template/material/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/template/material/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>