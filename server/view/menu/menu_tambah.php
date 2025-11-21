<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Tambah Menu Baru</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .preview-img {
      max-height: 200px;
      max-width: 100%;
      object-fit: cover;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      display: none;
      margin-top: 15px;
    }
    .preview-container {
      text-align: center;
      padding: 20px;
      background: #f8f9fa;
      border-radius: 12px;
      border: 2px dashed #dee2e6;
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
            <li class="breadcrumb-item text-sm text-dark active">Tambah Menu</li>
          </ol>
          <h6 class="font-weight-bolder mb-0">Tambah Menu Baru</h6>
        </nav>
      </div>
    </nav>

    <div class="container-fluid py-4">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Form Tambah Menu</h6>
              </div>
            </div>

            <div class="card-body p-4">
              <form action="/server/controller/menu/menuController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="tambah" value="1">

                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-4">
                      <label class="form-label fw-bold">Nama Menu</label>
                      <input type="text" name="nama_menu" class="form-control form-control-lg" required autofocus>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Jenis</label>
                      <select name="jenis" class="form-control form-control-lg" required>
                        <option value="" disabled selected>Pilih jenis</option>
                        <option value="Makanan">Makanan</option>
                        <option value="Minuman">Minuman</option>
                      </select>
                    </div>

                    <div class="mb-4">
                      <label class="form-label fw-bold">Harga (Rp)</label>
                      <input type="text" name="harga" class="form-control form-control-lg rupiah" 
                             placeholder="25.000" required>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="mb-4">
                      <label class="form-label fw-bold">Gambar Menu</label>
                      <input type="file" name="gambar" id="inputGambar" class="form-control form-control-lg" accept="image/*">
                      <small class="text-muted">Maksimal 2MB • JPG, PNG, GIF</small>
                    </div>

                    <!-- Preview Gambar -->
                    <div class="preview-container">
                      <p class="text-secondary mb-3"><em>Preview gambar akan muncul di sini</em></p>
                      <img id="previewImg" class="preview-img" src="" alt="Preview">
                    </div>
                  </div>
                </div>

                <hr class="horizontal dark my-4">

                <div class="text-end">
                  <a href="menu.php" class="btn btn-light btn-lg me-3">Batal</a>
                  <button type="submit" class="btn bg-gradient-primary btn-lg px-5">
                    <i class="material-symbols-rounded me-2">save</i>
                    Simpan Menu
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

  <!-- Script Preview Gambar -->
  <script>
    document.getElementById('inputGambar').addEventListener('change', function(e) {
      const file = e.target.files[0];
      const preview = document.getElementById('previewImg');
      const container = preview.parentElement;

      if (file) {
        // Validasi ukuran (maks 2MB)
        if (file.size > 2 * 1024 * 1024) {
          alert('Ukuran gambar maksimal 2MB!');
          e.target.value = '';
          preview.style.display = 'none';
          return;
        }

        const reader = new FileReader();
        reader.onload = function(event) {
          preview.src = event.target.result;
          preview.style.display = 'block';
          container.querySelector('p').style.display = 'none';
        }
        reader.readAsDataURL(file);
      } else {
        preview.src = '';
        preview.style.display = 'none';
        container.querySelector('p').style.display = 'block';
      }
    });
  </script>

  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>