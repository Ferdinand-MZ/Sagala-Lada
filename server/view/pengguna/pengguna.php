<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/template/material/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/template/material/assets/img/favicon.png">
  <title>
    Daftar Pengguna
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
  
  <?php $page = 'pengguna'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php'; ?>


  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Pengguna</li>
          </ol>
        </nav>

        <div class="ms-md-auto pe-md-3 d-flex align-items-center">
          <a href="/server/view/pengguna/pengguna_tambah.php" class="btn bg-gradient-primary btn-sm mb-0">+ Tambah Pengguna</a>
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
            <h6 class="text-white text-capitalize ps-3">Daftar Pengguna</h6>
          </div>
        </div>

        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">No</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Username</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Role</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tanggal</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Aksi</th>
                </tr>
              </thead>
              <tbody>
  <?php
  $no = 1;
  $sql = "SELECT * FROM user ORDER BY created_at DESC";
  $query = mysqli_query($koneksi, $sql);

  if (mysqli_num_rows($query) == 0) : ?>
    <tr>
      <td colspan="5" class="text-center py-4 text-secondary">Belum ada pengguna</td>
    </tr>
  <?php else :
    while ($u = mysqli_fetch_array($query)) : ?>
      <tr>
        <td class="ps-4"><span class="text-xs"><?= $no++ ?></span></td>
        <td><span class="text-xs font-weight-bold"><?= htmlspecialchars($u['username']) ?></span></td>
        <td><span class="badge badge-sm <?= $u['role']=='owner' ? 'bg-gradient-warning' : 'bg-gradient-info' ?>">
          <?= ucfirst($u['role']) ?></span></td>
        <td><span class="text-xs"><?= date('d-m-Y H:i', strtotime($u['created_at'])) ?></span></td>
        <td class="text-center">
          <a href="pengguna_edit.php?id=<?= $u['id_user'] ?>" class="text-warning mx-1">
            <i class="material-symbols-rounded">edit</i>
          </a>
          <?php if($u['role'] != 'owner'): ?>
          <a href="/server/view/pengguna/pengguna_hapus.php?id=<?= $u['id_user'] ?>"
            onclick="return confirm('Yakin hapus <?= htmlspecialchars($u['username']) ?>?')"
            class="text-danger mx-1">
            <i class="material-symbols-rounded">delete</i>
          </a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  <?php endif; ?>
</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


  </main>
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