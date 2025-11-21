<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/template/material/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/template/material/assets/img/favicon.png">
  <title>
    Daftar Pesanan
  </title>
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <!-- Nucleo Icons -->
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <!-- CSS Files -->
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
</head>

<body class="g-sidenav-show  bg-gray-100">
  
  <?php $page = 'pesanan'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php'; ?>


  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Pesanan</li>
          </ol>
        </nav>

        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          <a href="pesanan_tambah.php" class="btn bg-gradient-primary btn-sm mb-0">+ Tambah Pesanan</a>
        </div>
      </div>
    </nav>
    <!-- End Navbar -->
   <div class="container-fluid py-4">
      <div class="row">
        <div class="col-12">
          <div class="card my-4">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Daftar Semua Pesanan</h6>
              </div>
            </div>

            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ID Pesanan</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Pelanggan</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Meja</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Total</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                $no = 1;
                $sql = "SELECT p.*, py.metode_bayar 
                        FROM pesanan p 
                        LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan 
                        ORDER BY p.tanggal_pesan DESC";
                $query = mysqli_query($koneksi, $sql);
                while ($p = mysqli_fetch_array($query)) :
                ?>
                <tr>
                <td class="ps-4"><span class="text-secondary text-xs font-weight-bold"><?= $no++; ?></span></td>
                <td><span class="text-xs font-weight-bold">#<?= str_pad($p['id_pesanan'], 4, '0', STR_PAD_LEFT); ?></span></td>
                <td><h6 class="mb-0 text-sm"><?= htmlspecialchars($p['nama_pelanggan']); ?></h6></td>
                <td><span class="text-secondary text-xs"><?= $p['no_meja'] ?: '-'; ?></span></td>
                <td><span class="text-secondary font-weight-bold">Rp <?= number_format($p['total'], 0, ',', '.'); ?></span></td>
                <td>
                    <?php 
                    $status = $p['status'];
                    $badge = $status == 'Selesai' ? 'bg-gradient-success' : 
                            ($status == 'Dibatalkan' ? 'bg-gradient-danger' : 'bg-gradient-warning');
                    ?>
                    <span class="badge badge-sm <?= $badge; ?>"><?= $status; ?></span>
                </td>

              <td class="align-middle text-center">
  <!-- Detail -->
  <a href="javascript:;" class="text-info me-2" title="Detail" onclick="showDetail(<?= $p['id_pesanan'] ?>)">
    <i class="material-symbols-rounded">visibility</i>
  </a>

  <!-- Edit (hanya jika Pending) -->
  <?php if ($p['status'] == 'Pending'): ?>
  <a href="pesanan_edit.php?id=<?= $p['id_pesanan'] ?>" class="text-warning me-2" title="Edit">
    <i class="material-symbols-rounded">edit</i>
  </a>

  <!-- Hapus (hanya jika Pending atau Dibatalkan) -->
  <a href="pesanan_hapus.php?id=<?= $p['id_pesanan'] ?>" 
     class="text-danger" title="Hapus" 
     onclick="return confirm('Yakin hapus pesanan #<?= str_pad($p['id_pesanan'], 4, '0', STR_PAD_LEFT); ?>?')">
    <i class="material-symbols-rounded">delete</i>
  </a>
  <?php endif; ?>

  <!-- Bayar (hanya jika Pending dan total > 0) -->
  <?php if ($p['status'] == 'Pending' && $p['total'] > 0): ?>
  <a href="tambah_item.php?id=<?= $p['id_pesanan'] ?>" class="text-success ms-2" title="Lanjutkan & Bayar">
    <i class="material-symbols-rounded">payment</i>
  </a>
  <?php endif; ?>
</td>
                </tr>
                <?php endwhile; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Detail Pesanan -->
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Detail Pesanan #<span id="idPesanan"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-0" id="detailContent">
        <!-- Isi detail akan di-load via JS -->
      </div>
    </div>
  </div>
</div>

<script>
// Fungsi buka modal + ambil detail via AJAX
function showDetail(id) {
  const modal = new bootstrap.Modal(document.getElementById('modalDetail'));
  document.getElementById('idPesanan').textContent = id.toString().padStart(4, '0');
  
  fetch(`get_detail_pesanan.php?id=${id}`)
    .then(r => r.text())
    .then(html => {
      document.getElementById('detailContent').innerHTML = html;
      modal.show();
    });
}
</script>
  </main>
  <div class="fixed-plugin">
    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">
      <i class="material-symbols-rounded py-2">settings</i>
    </a>
    <div class="card shadow-lg">
      <div class="card-header pb-0 pt-3">
        <div class="float-start">
          <h5 class="mt-3 mb-0">Material UI Configurator</h5>
          <p>See our dashboard options.</p>
        </div>
        <div class="float-end mt-4">
          <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">
            <i class="material-symbols-rounded">clear</i>
          </button>
        </div>
        <!-- End Toggle Button -->
      </div>
      <hr class="horizontal dark my-1">
      <div class="card-body pt-sm-3 pt-0">
        <!-- Sidebar Backgrounds -->
        <div>
          <h6 class="mb-0">Sidebar Colors</h6>
        </div>
        <a href="javascript:void(0)" class="switch-trigger background-color">
          <div class="badge-colors my-2 text-start">
            <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>
            <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>
          </div>
        </a>
        <!-- Sidenav Type -->
        <div class="mt-3">
          <h6 class="mb-0">Sidenav Type</h6>
          <p class="text-sm">Choose between different sidenav types.</p>
        </div>
        <div class="d-flex">
          <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>
          <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>
          <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>
        </div>
        <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>
        <!-- Navbar Fixed -->
        <div class="mt-3 d-flex">
          <h6 class="mb-0">Navbar Fixed</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">
          </div>
        </div>
        <hr class="horizontal dark my-3">
        <div class="mt-2 d-flex">
          <h6 class="mb-0">Light / Dark</h6>
          <div class="form-check form-switch ps-0 ms-auto my-auto">
            <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">
          </div>
        </div>
        <hr class="horizontal dark my-sm-4">
        <a class="btn bg-gradient-info w-100" href="https://www.creative-tim.com/product/material-dashboard-pro">Free Download</a>
        <a class="btn btn-outline-dark w-100" href="https://www.creative-tim.com/learning-lab/bootstrap/overview/material-dashboard">View documentation</a>
        <div class="w-100 text-center">
          <a class="github-button" href="https://github.com/creativetimofficial/material-dashboard" data-icon="octicon-star" data-size="large" data-show-count="true" aria-label="Star creativetimofficial/material-dashboard on GitHub">Star</a>
          <h6 class="mt-3">Thank you for sharing!</h6>
          <a href="https://twitter.com/intent/tweet?text=Check%20Material%20UI%20Dashboard%20made%20by%20%40CreativeTim%20%23webdesign%20%23dashboard%20%23bootstrap5&amp;url=https%3A%2F%2Fwww.creative-tim.com%2Fproduct%2Fsoft-ui-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-twitter me-1" aria-hidden="true"></i> Tweet
          </a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.creative-tim.com/product/material-dashboard" class="btn btn-dark mb-0 me-2" target="_blank">
            <i class="fab fa-facebook-square me-1" aria-hidden="true"></i> Share
          </a>
        </div>
      </div>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/template/material/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>

</html>