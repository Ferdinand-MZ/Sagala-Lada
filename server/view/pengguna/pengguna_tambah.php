<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';
role_required(['admin']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Tambah Pengguna Baru</title>
  <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>
<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'pengguna'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="/server/view/pengguna/pengguna.php">Pengguna</a></li>
            <li class="breadcrumb-item text-sm text-dark active">Tambah</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Tambah Pengguna Baru</h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Tambah Data Pengguna</h6>
              </div>
            </div>
            <div class="card-body px-4 pb-4">
              <form action="/server/controller/pengguna/penggunaController.php" method="POST">
                <input type="hidden" name="action" value="tambah">

                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Password</label>
                  <input type="password" name="password" class="form-control" minlength="6" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Konfirmasi Password</label>
                  <input type="password" name="password_confirm" class="form-control" minlength="6" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <select name="role" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="owner">Owner</option>
                  </select>
                </div>

                <div class="text-end">
                  <a href="/server/view/pengguna/pengguna.php" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn bg-gradient-success">Simpan</button>
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