<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';
role_required('owner');

// ==============================
// AJAX HANDLER
// ==============================
if (isset($_GET['ajax'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

    $page = max(1, intval($_GET['page'] ?? 1));
    $limit = 10;
    $offset = ($page - 1) * $limit;

    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');
    $jenis = mysqli_real_escape_string($koneksi, $_GET['jenis'] ?? '');

    $where = "WHERE 1=1";

    if ($search !== '') $where .= " AND nama_menu LIKE '%$search%'";
    if ($jenis === 'Makanan' || $jenis === 'Minuman') $where .= " AND jenis='$jenis'";

    // Count total
    $count = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM menu $where"));
    $total = intval($count['total']);
    $pages = ceil($total / $limit);

    // Data
    $sql = "SELECT * FROM menu $where ORDER BY jenis, nama_menu LIMIT $limit OFFSET $offset";
    $q = mysqli_query($koneksi, $sql);

    $data = [];
    while ($r = mysqli_fetch_assoc($q)) $data[] = $r;

    echo json_encode([
        "data" => $data,
        "pages" => $pages,
        "current" => $page
    ]);
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1">

<title>Daftar Menu</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900">
<link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet">
<link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet">
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded">
<link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet">

<style>
#searchInput {
  background:#fff!important;
  border:1px solid #ccc!important;
  color:#000!important;
  padding:8px 12px!important;
  border-radius:8px;
  min-width:220px;
}
.menu-img {
  width:60px; height:60px; object-fit:cover; border-radius:6px;
}
.no-results { text-align:center; padding:15px; color:#777; }
#pagination button {
  border-radius:6px;
}
</style>

<script>
let currentPage = 1;
let search = "";
let jenisFilter = "";

// Escape HTML
function esc(s){return (s+"").replace(/[&<>"']/g,c=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[c]));}

// ================= LOAD DATA =================
function loadMenu(page = 1) {
  currentPage = page;

  const params = new URLSearchParams({
    ajax: 1,
    page: page,
    search: search,
    jenis: jenisFilter
  });

  fetch(window.location.pathname + "?" + params.toString())
    .then(r=>r.json())
    .then(res=>{
        const body = document.getElementById("tableBody");
        body.innerHTML = "";

        if (!res.data || res.data.length === 0){
            body.innerHTML = `<tr><td colspan="6" class="no-results">Tidak ada menu ditemukan.</td></tr>`;
            renderPagination(1);
            return;
        }

        res.data.forEach((m,i)=>{
            const no = (res.current-1)*10 + (i+1);

            const gambar = m.gambar 
              ? `<img src="/assets/img/menu/${esc(m.gambar)}" class="menu-img">`
              : `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:60px;">
                    <i class="material-symbols-rounded text-secondary">image</i>
                 </div>`;

            const badge = m.jenis === "Makanan" ? "bg-gradient-success" : "bg-gradient-info";

            body.innerHTML += `
            <tr>
              <td class="ps-4">${no}</td>
              <td>${gambar}</td>
              <td>
                <h6 class="mb-0 text-sm">${esc(m.nama_menu)}</h6>
              </td>
              <td><span class="badge badge-sm ${badge}">${esc(m.jenis)}</span></td>
              <td>Rp ${parseInt(m.harga).toLocaleString()}</td>
              <td class="text-center">
                <a href="menu_edit.php?id=${m.id_menu}" class="text-warning me-2">
                    <i class="material-symbols-rounded">edit</i>
                </a>
                <a href="menu_hapus.php?id=${m.id_menu}" class="text-danger" 
                   onclick="return confirm('Yakin hapus menu ${esc(m.nama_menu)}?')">
                    <i class="material-symbols-rounded">delete</i>
                </a>
              </td>
            </tr>`;
        });

        renderPagination(res.pages);
    });
}

// ================= SEARCH =================
function onSearchInput() {
  search = document.getElementById("searchInput").value.trim();
  loadMenu(1);
}

// ================= FILTER =================
function applyFilter(v){
  jenisFilter = v;
  loadMenu(1);
}

function resetAll(){
  search="";
  jenisFilter="";
  document.getElementById("searchInput").value="";
  loadMenu(1);
}

// ================= PAGINATION =================
function renderPagination(totalPages){
  const pag = document.getElementById("pagination");
  pag.innerHTML = "";

  if (totalPages <= 1) return;

  let html = "";
  for(let i=1; i<=totalPages; i++){
    html += `
      <button 
        class="btn btn-sm ${i===currentPage?'bg-gradient-dark text-white':'btn-outline-dark'} mx-1"
        onclick="loadMenu(${i})">${i}</button>
    `;
  }

  pag.innerHTML = html;
}

document.addEventListener("DOMContentLoaded",()=>loadMenu(1));
</script>

</head>

<body class="g-sidenav-show bg-gray-100">

<?php $page='menu'; include $_SERVER['DOCUMENT_ROOT'].'/components/sidebar.php'; include $_SERVER['DOCUMENT_ROOT'].'/koneksi.php'; ?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl">
  <div class="container-fluid py-1 px-3">

    <nav aria-label="breadcrumb">
      <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Halaman</a></li>
        <li class="breadcrumb-item text-sm text-dark active">Menu</li>
      </ol>
    </nav>

    <div class="d-flex align-items-center ms-auto">
      <input id="searchInput" type="text" placeholder="Cari nama menu..." oninput="onSearchInput()">
      <button class="btn btn-outline-dark btn-sm ms-2" onclick="applyFilter('Makanan')">Makanan</button>
      <button class="btn btn-outline-dark btn-sm ms-2" onclick="applyFilter('Minuman')">Minuman</button>
      <button class="btn btn-secondary btn-sm ms-2" onclick="resetAll()">Reset</button>
    </div>

    <div class="ms-md-3 d-flex align-items-center">
      <a href="menu_tambah.php" class="btn bg-gradient-primary btn-sm mb-0">+ Tambah Menu</a>
    </div>

  </div>
</nav>

<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      <div class="card my-4">
        <div class="card-header p-0 mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white ps-3">Daftar Menu Makanan & Minuman</h6>
          </div>
        </div>

        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Gambar</th>
                  <th>Nama Menu</th>
                  <th>Jenis</th>
                  <th>Harga</th>
                  <th class="text-center">Aksi</th>
                </tr>
              </thead>
              <tbody id="tableBody"></tbody>
            </table>
          </div>

          <!-- PAGINATION -->
          <div id="pagination" class="d-flex justify-content-center mt-3 mb-4"></div>

        </div>
      </div>

    </div>
  </div>
</div>

</main>

<script src="/assets/template/material/assets/js/core/popper.min.js"></script>
<script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/template/material/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>
</html>
