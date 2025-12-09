<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// ====================================
// AJAX HANDLER
// ====================================
if (isset($_GET['ajax'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

    $page = intval($_GET['page'] ?? 1);
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');

    // count total rows
    $countSql = "
        SELECT COUNT(*) AS total
        FROM pesanan p
        JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE p.status='Selesai'
        AND (
            p.nama_pelanggan LIKE '%$search%' OR
            p.no_meja LIKE '%$search%' OR
            py.metode_bayar LIKE '%$search%'
        )
    ";
    $total = mysqli_fetch_assoc(mysqli_query($koneksi, $countSql))['total'];
    $pages = ceil($total / $limit);

    // data transaksi
    $sql = "
        SELECT p.*, py.metode_bayar, py.tanggal_bayar
        FROM pesanan p
        JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE p.status='Selesai'
        AND (
            p.nama_pelanggan LIKE '%$search%' OR
            p.no_meja LIKE '%$search%' OR
            py.metode_bayar LIKE '%$search%'
        )
        ORDER BY py.tanggal_bayar DESC
        LIMIT $limit OFFSET $offset
    ";
    $q = mysqli_query($koneksi, $sql);

    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;

    echo json_encode([
        "data" => $rows,
        "pages" => $pages,
        "current" => $page
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Daftar Transaksi</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
<link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
<link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
<script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded" />
<link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />

<style>
#searchInput {
    background:#fff!important;
    border:1px solid #ccc!important;
    color:black!important;
    padding:8px 12px!important;
    border-radius:8px!important;
    min-width:240px;
}
.no-results { text-align:center; padding:15px; color:#777; }
#pagination button { border-radius:6px; }
</style>

<script>
let currentPage = 1;
let search = "";

// helper escape
function esc(s){return (s+"").replace(/[&<>"']/g,c=>({ "&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;","'":"&#39;" }[c]));}

// ================= LOAD DATA =================
function loadTransaksi(page = 1) {
    currentPage = page;

    fetch("transaksi.php?ajax=1&page="+page+"&search="+encodeURIComponent(search))
        .then(r => r.json())
        .then(res => {
            const body = document.getElementById("tableBody");
            body.innerHTML = "";

            if (res.data.length === 0) {
                body.innerHTML = `
                    <tr>
                        <td colspan="8" class="no-results">Belum ada transaksi ditemukan.</td>
                    </tr>
                `;
                renderPagination(1);
                return;
            }

            res.data.forEach((t,i)=>{
                const no = (res.current-1)*10 + (i+1);

                body.innerHTML += `
                <tr>
                    <td class="ps-4">${no}</td>
                    <td>#${String(t.id_pesanan).padStart(4,'0')}</td>
                    <td>${t.tanggal_bayar}</td>
                    <td>${esc(t.nama_pelanggan)}</td>
                    <td>${t.no_meja ?? '-'}</td>
                    <td>Rp ${parseInt(t.total).toLocaleString()}</td>
                    <td>
                        <span class="badge ${
                            t.metode_bayar=='Cash' ? 'bg-gradient-success'
                            : t.metode_bayar=='Transfer' ? 'bg-gradient-info'
                            : 'bg-gradient-warning'
                        }">${t.metode_bayar}</span>
                    </td>
                    <td class="text-center">
                        <a href="javascript:;" onclick="showDetail(${t.id_pesanan})" class="text-info">
                            <i class="material-symbols-rounded">visibility</i>
                        </a>
                    </td>
                </tr>`;
            });

            renderPagination(res.pages);
        });
}

// ================= SEARCH =================
function handleSearch() {
    search = document.getElementById("searchInput").value.trim();
    loadTransaksi(1);
}

function resetSearch() {
    search = "";
    document.getElementById("searchInput").value = "";
    loadTransaksi(1);
}

// ================= PAGINATION =================
function renderPagination(totalPages){
    const pag = document.getElementById("pagination");
    pag.innerHTML = "";

    if (totalPages <= 1) return;

    let html = "";
    for(let i=1; i<=totalPages; i++){
        html += `
            <button class="btn btn-sm ${i===currentPage?'bg-gradient-dark text-white':'btn-outline-dark'} mx-1"
                    onclick="loadTransaksi(${i})">${i}</button>
        `;
    }
    pag.innerHTML = html;
}

document.addEventListener("DOMContentLoaded",()=>loadTransaksi());
</script>

</head>

<body class="g-sidenav-show bg-gray-100">

<?php 
$page = 'transaksi'; 
include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; 
include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php'; 
?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

<!-- NAVBAR -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl">
  <div class="container-fluid py-1 px-3">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-dark active">Transaksi</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center ms-auto">
        <input type="text" id="searchInput" placeholder="Cari transaksi..." oninput="handleSearch()">
        <button class="btn btn-secondary btn-sm ms-2" onclick="resetSearch()">Reset</button>
    </div>

  </div>
</nav>

<!-- MAIN CONTENT -->
<div class="container-fluid py-4">
  <div class="row">
    <div class="col-12">

      <div class="card my-4">
        <div class="card-header p-0 mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white ps-3">Daftar Transaksi Selesai</h6>
          </div>
        </div>

        <div class="card-body px-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table mb-0">
              <thead>
                <tr>
                  <th>No</th>
                  <th>ID Transaksi</th>
                  <th>Tanggal</th>
                  <th>Pelanggan</th>
                  <th>Meja</th>
                  <th>Total</th>
                  <th>Metode</th>
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

<!-- MODAL DETAIL -->
<div class="modal fade" id="modalTransaksiDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Detail Transaksi #<span id="idTransaksi"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body pt-0" id="detailTransaksiContent">
        <div class="text-center py-4">
          <div class="spinner-border text-primary"></div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
function showDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById('modalTransaksiDetail'));
    document.getElementById('idTransaksi').textContent = String(id).padStart(4,'0');

    document.getElementById('detailTransaksiContent').innerHTML =
        `<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>`;

    fetch(`/server/view/pesanan/get_detail_pesanan.php?id=${id}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById('detailTransaksiContent').innerHTML = html;
            modal.show();
        })
        .catch(() => {
            document.getElementById('detailTransaksiContent').innerHTML =
                `<p class="text-danger text-center">Gagal memuat detail.</p>`;
        });
}
</script>

<script src="/assets/template/material/assets/js/core/popper.min.js"></script>
<script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>

</body>
</html>
