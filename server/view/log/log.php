<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';
role_required(['owner', 'admin']);
$page = 'log';

// =================== SEARCH ===================
$search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? "");

// =================== PAGINATION ===================
$limit = 20;
$pageNow = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($pageNow - 1) * $limit;

// Hitung total log
$countSql = "
    SELECT COUNT(*) AS total 
    FROM log l
    JOIN user u ON l.id_user = u.id_user
    WHERE 
        u.username LIKE '%$search%' OR
        u.role LIKE '%$search%' OR
        l.keterangan LIKE '%$search%' OR
        l.created_at LIKE '%$search%'
";
$countQ = mysqli_query($koneksi, $countSql);
$totalData = mysqli_fetch_assoc($countQ)['total'];
$totalPage = ceil($totalData / $limit);

// Query data log
$sql = "
    SELECT u.username, u.role, l.keterangan, l.created_at 
    FROM log l 
    JOIN user u ON l.id_user = u.id_user 
    WHERE 
        u.username LIKE '%$search%' OR
        u.role LIKE '%$search%' OR
        l.keterangan LIKE '%$search%' OR
        l.created_at LIKE '%$search%'
    ORDER BY l.created_at DESC
    LIMIT $limit OFFSET $offset
";
$result = mysqli_query($koneksi, $sql);

$no = ($pageNow - 1) * $limit + 1;
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Log Aktivitas - Dashboard</title>

  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

  <style>
    .table thead { position: sticky; top: 0; z-index: 10; background: #fff; }
    .badge-owner { background: linear-gradient(90deg, #c62828, #d32f2f); }
    .badge-admin { background: linear-gradient(90deg, #2e7d32, #388e3c); }
    .icon-login   { color: #43A047; }
    .icon-logout  { color: #E91E63; }
    .icon-create  { color: #2196F3; }
    .icon-update  { color: #FF9800; }
    .icon-delete  { color: #F44336; }
    .icon-other   { color: #9E9E9E; }

    /* Search bar putih */
    #searchInput {
        background: #fff !important;
        border: 1px solid #ccc !important;
        color: #555 !important;
        padding: 8px 12px !important;
        border-radius: 8px !important;
        outline: none;
    }

    #searchInput::placeholder {
        color: #999 !important;
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/navbar.php'; ?>

    <div class="container-fluid py-4">

      <!-- SEARCH BAR -->
      <div class="mb-3 px-2">
        <form method="GET">
          <input 
            type="text" 
            id="searchInput" 
            name="search" 
            value="<?= htmlspecialchars($search) ?>" 
            placeholder="Cari log aktivitas..." 
            oninput="this.form.submit()"
          >
        </form>
      </div>

      <div class="row">
        <div class="col-12">
          <div class="card my-4">

            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                <h6 class="text-white ps-3">
                  <i class="material-symbols-rounded me-2">history</i> Log Aktivitas Sistem
                </h6>
              </div>
            </div>

            <div class="card-body px-0 pb-2">
              <div class="table-responsive p-0">

                <table class="table align-items-center mb-0">
                  <thead class="bg-light">
                    <tr>
                      <th class="ps-4 text-secondary text-xxs font-weight-bolder">No</th>
                      <th class="text-secondary text-xxs font-weight-bolder">User</th>
                      <th class="text-secondary text-xxs font-weight-bolder">Role</th>
                      <th class="ps-3 text-secondary text-xxs font-weight-bolder">Aktivitas</th>
                      <th class="text-secondary text-xxs font-weight-bolder">Keterangan</th>
                      <th class="pe-4 text-end text-secondary text-xxs font-weight-bolder">Waktu</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php if (mysqli_num_rows($result) == 0): ?>
                      <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Tidak ada log ditemukan</td>
                      </tr>
                    <?php else: ?>
                      <?php while ($log = mysqli_fetch_assoc($result)): 
                        $ket = strtolower($log['keterangan']);
                        
                        if (strpos($ket, 'login') !== false) $icon = 'login';
                        elseif (strpos($ket, 'logout') !== false) $icon = 'logout';
                        elseif (strpos($ket, 'tambah') !== false) $icon = 'create';
                        elseif (strpos($ket, 'edit') !== false) $icon = 'update';
                        elseif (strpos($ket, 'hapus') !== false) $icon = 'delete';
                        else $icon = 'other';
                      ?>
                      <tr>
                        <td class="ps-4"><?= $no++ ?></td>

                        <td><h6 class="mb-0 text-sm"><?= htmlspecialchars($log['username']) ?></h6></td>

                        <td>
                          <span class="badge badge-sm <?= $log['role'] == 'owner' ? 'badge-owner' : 'badge-admin' ?>">
                            <?= ucfirst($log['role']) ?>
                          </span>
                        </td>

                        <td class="ps-3">
                          <i class="material-symbols-rounded icon-<?= $icon ?> me-2">
                            <?= $icon == 'login' ? 'login' : ($icon == 'logout' ? 'logout' : ($icon == 'create' ? 'add' : ($icon == 'update' ? 'edit' : ($icon == 'delete' ? 'delete' : 'info')))) ?>
                          </i>
                        </td>

                        <td><?= htmlspecialchars($log['keterangan']) ?></td>

                        <td class="text-end pe-4 text-muted"><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?></td>
                      </tr>
                      <?php endwhile; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- PAGINATION -->
              <div class="d-flex justify-content-center mt-4 mb-2">
                <nav>
                  <ul class="pagination pagination-sm">
                  

                    <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                      <li class="page-item <?= $i == $pageNow ? 'active' : '' ?>">
                        <a class="page-link" href="?search=<?= $search ?>&page=<?= $i ?>"><?= $i ?></a>
                      </li>
                    <?php endfor; ?>


                  </ul>
                </nav>
              </div>

            </div>
          </div>
        </div>
      </div>

    </div>
  </main>

</body>
</html>
