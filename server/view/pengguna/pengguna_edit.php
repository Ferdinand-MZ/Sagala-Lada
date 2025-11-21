<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';
role_required(['admin']);
?>

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

if (!isset($_GET['id'])) { 
    header("Location: /server/view/pengguna/pengguna.php"); 
    exit; 
}

$id = (int)$_GET['id'];
$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id"));

if (!$user) { 
    header("Location: /server/view/pengguna/pengguna.php"); 
    exit; 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Edit Pengguna #<?= str_pad($id, 4, '0', STR_PAD_LEFT) ?></title>
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
            <li class="breadcrumb-item text-sm text-dark active">Edit</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Edit Pengguna #<?= str_pad($id,4,'0',STR_PAD_LEFT) ?></h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-6">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Edit Data Pengguna</h6>
              </div>
            </div>
            <div class="card-body px-4 pb-4">
              <form action="/server/controller/pengguna/penggunaController.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id_user" value="<?= $id ?>">

                <div class="mb-3">
                  <label class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label">Role</label>
                  <select name="role" class="form-control" required>
                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="owner" <?= $user['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                  </select>
                </div>

                <div class="mb-3">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="changePass">
                    <label class="form-check-label" for="changePass">
                      Ubah Password
                    </label>
                  </div>
                </div>

                <div id="passwordFields" style="display:none;">
                  <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" minlength="6">
                  </div>
                  <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password_confirm" class="form-control" minlength="6">
                  </div>
                </div>

                <div class="text-end">
                  <a href="pengguna.php" class="btn btn-secondary">Batal</a>
                  <button type="submit" class="btn bg-gradient-success">Update</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.getElementById('changePass').addEventListener('change', function() {
      document.getElementById('passwordFields').style.display = this.checked ? 'block' : 'none';
    });
  </script>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/fixed-plugin.php'; ?>
  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>