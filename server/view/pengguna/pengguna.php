<?php
// ==============================
// AJAX HANDLER
// ==============================
if (isset($_GET['ajax'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

    $page = intval($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');

    $sql = "
        SELECT * FROM user
        WHERE username LIKE '%$search%'
        ORDER BY created_at DESC
        LIMIT $limit OFFSET $offset
    ";

    $q = mysqli_query($koneksi, $sql);

    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) {
        $rows[] = $r;
    }

    echo json_encode($rows);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <title>Daftar Pengguna</title>

  <!-- Styles -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

<style>
#searchInput {
    background: white !important;
    border: 1px solid #ccc !important;
    color: black !important;
    padding: 8px 12px !important;
    border-radius: 8px;
}
</style>

<script>
let page = 1;
let limit = 10;
let loading = false;
let finished = false;
let search = "";

// ================= LOAD DATA =================
function loadUsers(reset = false) {
    if (loading || finished) return;
    loading = true;

    if (reset) {
        page = 1;
        finished = false;
        document.getElementById("tableBody").innerHTML = "";
    }

    fetch(`pengguna.php?ajax=1&page=${page}&search=${search}`)
        .then(res => res.json())
        .then(data => {
            loading = false;

            if (data.length === 0) {
                finished = true;
                return;
            }

            data.forEach((u, i) => {
                document.getElementById("tableBody").innerHTML += `
                    <tr>
                        <td class="ps-4">${(page-1)*limit + i + 1}</td>
                        <td>${u.username}</td>
                        <td><span class="badge bg-gradient-${u.role=='owner'?'warning':'info'}">${u.role}</span></td>
                        <td>${new Date(u.created_at).toLocaleString()}</td>
                        <td class="text-center">
                          <a href="pengguna_edit.php?id=${u.id_user}" class="text-warning mx-1">
                            <i class="material-symbols-rounded">edit</i>
                          </a>
                          ${u.role !== 'owner' ? `
                          <a href="/server/view/pengguna/pengguna_hapus.php?id=${u.id_user}"
                             onclick="return confirm('Yakin hapus ${u.username}?')"
                             class="text-danger mx-1">
                             <i class="material-symbols-rounded">delete</i>
                          </a>` : ''}
                        </td>
                    </tr>
                `;
            });

            page++;
        });
}

document.addEventListener("DOMContentLoaded", loadUsers);

// ================= SEARCH =================
function handleSearch() {
    search = document.getElementById("searchInput").value;
    loadUsers(true);
}

// ================= RESET =================
function resetSearch() {
    search = "";
    document.getElementById("searchInput").value = "";
    loadUsers(true);
}

// ================= INFINITE SCROLL =================
window.addEventListener("scroll", () => {
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 400) {
        loadUsers();
    }
});
</script>

</head>

<body class="g-sidenav-show bg-gray-100">

<?php $page = 'pengguna'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

<!-- NAVBAR -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl">
  <div class="container-fluid py-1 px-3">
    
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Halaman</a></li>
        <li class="breadcrumb-item text-sm text-dark active">Pengguna</li>
      </ol>
    </nav>

    <div class="d-flex align-items-center ms-auto">
      <input type="text" id="searchInput" placeholder="Cari username..." oninput="handleSearch()">
      <button class="btn btn-secondary btn-sm ms-2" onclick="resetSearch()">Reset</button>
    </div>

    <div class="ms-md-3 d-flex align-items-center">
      <a href="/server/view/pengguna/pengguna_tambah.php" class="btn bg-gradient-primary btn-sm mb-0">+ Tambah Pengguna</a>
    </div>

  </div>
</nav>


<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">
      <div class="card my-4">

        <div class="card-header p-0 mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white ps-3">Daftar Pengguna</h6>
          </div>
        </div>

        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Tanggal</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody id="tableBody"></tbody>
            </table>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

</main>

<script src="/assets/template/material/assets/js/core/popper.min.js"></script>
<script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>
</html>
