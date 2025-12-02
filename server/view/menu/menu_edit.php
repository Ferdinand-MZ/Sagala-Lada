<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

// Halaman khusus owner
role_required('owner');
?>

<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: menu.php");
    exit;
}
$id = (int)$_GET['id'];
$q = mysqli_query($koneksi, "SELECT * FROM menu WHERE id_menu = $id");
if (mysqli_num_rows($q) == 0) {
    header("Location: menu.php");
    exit;
}
$data = mysqli_fetch_assoc($q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Edit Menu</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .preview-img {
      max-height: 220px;
      max-width: 100%;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.12);
      margin-top: 15px;
    }
    .preview-container {
      text-align: center;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 12px;
      border: 2px dashed #ced4da;
    }
    .current-img {
      font-size: 0.9rem;
      color: #6c757d;
      margin-top: 8px;
    }
  </style>
</head>
<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'menu'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="menu.php">Menu</a></li>
            <li class="breadcrumb-item text-sm text-dark active">Edit Menu</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Edit Menu: <?= htmlspecialchars($data['nama_menu']) ?></h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Form Edit Menu</h6>
              </div>
            </div>

            <div class="card-body p-4">
              <form action="/server/controller/menu/menuController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="edit" value="1">
                <input type="hidden" name="id_menu" value="<?= $data['id_menu'] ?>">
                <input type="hidden" name="gambar_lama" value="<?= $data['gambar'] ?>">

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-4">
                      <label class="form-label fw-bold">Nama Menu</label>
                      <input type="text" name="nama_menu" class="form-control form-control-lg" 
                             value="<?= htmlspecialchars($data['nama_menu']) ?>" required autofocus>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Jenis</label>
                      <select name="jenis" class="form-control form-control-lg" required>
                        <option value="Makanan" <?= $data['jenis']=='Makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= $data['jenis']=='Minuman' ? 'selected' : '' ?>>Minuman</option>
                      </select>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Harga (Rp)</label>
                      <input type="text" name="harga" class="form-control form-control-lg rupiah" 
                             value="<?= number_format($data['harga'], 0, ',', '.') ?>" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="mb-4">
                      <label class="form-label fw-bold">Gambar Saat Ini</label>
                      <?php if ($data['gambar']): ?>
                        <div class="text-center">
                          <img src="/assets/img/menu/<?= $data['gambar'] ?>" class="preview-img" alt="Gambar saat ini">
                          <p class="current-img mt-2">Gambar saat ini</p>
                        </div>
                      <?php else: ?>
                        <p class="text-muted text-center">Belum ada gambar</p>
                      <?php endif; ?>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Ganti Gambar (opsional)</label>
                      <input type="file" name="gambar" id="inputGambar" class="form-control form-control-lg" accept="image/*">
                      <small class="text-muted">Maksimal 2MB • JPG, PNG, GIF</small>
                    </div>

                    <!-- Preview gambar baru -->
                    <div class="preview-container" id="previewContainer" style="display:none;">
                      <p class="text-secondary mb-3"><strong>Preview gambar baru:</strong></p>
                      <img id="previewImg" class="preview-img" src="" alt="Preview">
                    </div>
                  </div>
                </div>

                <hr class="horizontal dark my-4">

                <div class="text-end">
                  <a href="menu.php" class="btn btn-light btn-lg me-3">Batal</a>
                  <button type="submit" class="btn bg-gradient-success btn-lg px-5">
                    <i class="material-symbols-rounded me-2">save</i>
                    Update Menu
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/fixed-plugin.php'; ?>

  <!-- Script Preview Gambar Baru -->
  <script>
    document.getElementById('inputGambar').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('previewImg');
      const container = document.getElementById('previewContainer');

      if (file) {
        if (file.size > 2 * 1024 * 1024) {
          alert('Ukuran gambar maksimal 2MB!');
          e.target.value = '';
          container.style.display = 'none';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(ev) {
          preview.src = ev.target.result;
          container.style.display = 'block';
        }
        reader.readAsDataURL(file);
      } else {
        container.style.display = 'none';
      }
    });
  </script>

  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>