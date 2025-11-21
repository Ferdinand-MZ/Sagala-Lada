<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

// Semua yang sudah login boleh akses profile sendiri
// Tidak pakai role_required(['admin']) → owner juga bisa

if (!isset($_SESSION['user_id'])) {
    header("Location: /auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

$user = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $user_id"));
if (!$user) {
    session_destroy();
    header("Location: /auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Profile Saya</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'profile'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Profile Saya</h6>
              </div>
            </div>

            <div class="card-body px-4 pb-4">
              <!-- Notifikasi -->
              <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-<?= $_GET['msg']=='success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                  <?= $_GET['msg']=='success' ? 'Profile berhasil diperbarui!' : 'Gagal memperbarui profile.' ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
              <?php endif; ?>

              <form action="/server/controller/profile/profileController.php" method="POST">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label class="form-label">Username</label>
                      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Role</label>
                      <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled readonly>
                    </div>

                    <div class="mb-3">
                      <label class="form-label">Bergabung Sejak</label>
                      <input type="text" class="form-control" value="<?= date('d F Y H:i', strtotime($user['created_at'])) ?>" disabled readonly>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="mb-3">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="changePass" id="changePass">
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
                  </div>
                </div>

                <div class="text-end">
                  <button type="submit" class="btn bg-gradient-success">Simpan Perubahan</button>
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