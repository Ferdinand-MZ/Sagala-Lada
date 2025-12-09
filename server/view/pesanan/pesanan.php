<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';
role_required(['admin']);

// =====================================
// AJAX HANDLER LIST DATA PESANAN
// =====================================
if (isset($_GET['ajax'])) {
    include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

    $page   = intval($_GET['page'] ?? 1);
    $limit  = 10;
    $offset = ($page - 1) * $limit;
    $search = mysqli_real_escape_string($koneksi, $_GET['search'] ?? '');

    // Ambil data
    $sql = "
        SELECT p.*, py.metode_bayar
        FROM pesanan p
        LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE 
            p.nama_pelanggan LIKE '%$search%' OR 
            p.no_meja LIKE '%$search%' OR 
            p.status LIKE '%$search%'
        ORDER BY p.tanggal_pesan DESC
        LIMIT $limit OFFSET $offset
    ";
    $q = mysqli_query($koneksi, $sql);

    $rows = [];
    while ($r = mysqli_fetch_assoc($q)) $rows[] = $r;

    // Hitung total data
    $count = mysqli_query($koneksi, "
        SELECT COUNT(*) AS total
        FROM pesanan p
        LEFT JOIN pembayaran py ON p.id_pesanan = py.id_pesanan
        WHERE 
            p.nama_pelanggan LIKE '%$search%' OR 
            p.no_meja LIKE '%$search%' OR 
            p.status LIKE '%$search%'
    ");
    $total = mysqli_fetch_assoc($count)['total'];

    echo json_encode([
        "data" => $rows,
        "total" => $total
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar Pesanan</title>

<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700">
<link rel="stylesheet" href="/assets/template/material/assets/css/nucleo-icons.css">
<link rel="stylesheet" href="/assets/template/material/assets/css/nucleo-svg.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded">
<link rel="stylesheet" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0">

<style>
#searchInput {
    background:#fff !important;
    border:1px solid #ccc !important;
    color:black !important;
    padding:8px 12px !important;
    border-radius:8px !important;
}
#resetBtn { margin-left:8px; }
.pagination-btn {
    min-width: 36px;
}
</style>

<script>
let page = 1;
let limit = 10;
let search = "";

// ========== LOAD DATA ==========  
function loadPesanan(reset = false) {
    if (reset) {
        document.getElementById("tableBody").innerHTML = "";
    }

    fetch(`pesanan.php?ajax=1&page=${page}&search=${search}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data;
            const total = res.total;

            if (data.length === 0) {
                document.getElementById("tableBody").innerHTML =
                    `<tr><td colspan="8" class="text-center py-4 text-secondary">Tidak ada data ditemukan.</td></tr>`;
                document.getElementById("pagination").innerHTML = "";
                return;
            }

            data.forEach((p, i) => {
                const badge =
                    p.status === "Selesai" ? "bg-gradient-success" :
                    p.status === "Dibatalkan" ? "bg-gradient-danger" : "bg-gradient-warning";

                document.getElementById("tableBody").innerHTML += `
                    <tr>
                        <td class="ps-4">${(page-1)*limit + i + 1}</td>
                        <td>#${String(p.id_pesanan).padStart(4,'0')}</td>
                        <td>${p.nama_pelanggan}</td>
                        <td>${p.no_meja ?? '-'}</td>
                        <td>Rp ${parseInt(p.total).toLocaleString()}</td>
                        <td><span class="badge badge-sm ${badge}">${p.status}</span></td>
                        <td class="text-center">
                            <a href="javascript:;" onclick="showDetail(${p.id_pesanan})" class="text-info me-2">
                                <i class="material-symbols-rounded">visibility</i>
                            </a>
                        </td>
                    </tr>
                `;
            });

            renderPagination(total);
        });
}

document.addEventListener("DOMContentLoaded", () => loadPesanan(true));

// ========== SEARCH ==========  
function handleSearch() {
    search = document.getElementById("searchInput").value;
    page = 1;
    loadPesanan(true);
}

function resetSearch() {
    search = "";
    document.getElementById("searchInput").value = "";
    page = 1;
    loadPesanan(true);
}

// ========== PAGINATION NUMERIC ==========  
function renderPagination(total) {
    const totalPage = Math.ceil(total / limit);
    let html = "";

    for (let i = 1; i <= totalPage; i++) {
        html += `
            <button 
                class="btn btn-sm mx-1 pagination-btn ${i === page ? 'btn-dark' : 'btn-outline-dark'}"
                onclick="goToPage(${i})"
            >
                ${i}
            </button>
        `;
    }

    document.getElementById("pagination").innerHTML = html;
}

function goToPage(p) {
    page = p;
    loadPesanan(true);
}
</script>

</head>
<body class="g-sidenav-show bg-gray-100">

<?php $page='pesanan'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>
<?php include $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php'; ?>

<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

<!-- NAVBAR -->
<nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl">
    <div class="container-fluid py-1 px-3">

        <ol class="breadcrumb bg-transparent mb-0 pt-1 px-0">
            <li class="breadcrumb-item"><a class="text-dark opacity-5">Halaman</a></li>
            <li class="breadcrumb-item text-dark active">Pesanan</li>
        </ol>

        <!-- Search bar -->
        <div class="ms-auto d-flex align-items-center">
            <input id="searchInput" type="text" placeholder="Cari pesanan..." oninput="handleSearch()">
            <button id="resetBtn" class="btn btn-secondary btn-sm" onclick="resetSearch()">Reset</button>
        </div>

        <a href="pesanan_tambah.php" class="btn bg-gradient-primary btn-sm ms-3">+ Tambah Pesanan</a>
    </div>
</nav>

<!-- TABLE -->
<div class="container-fluid py-4">
<div class="card my-4">
    <div class="card-header p-0 mx-3 mt-n4 z-index-2">
        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
            <h6 class="text-white ps-3">Daftar Semua Pesanan</h6>
        </div>
    </div>

    <div class="card-body px-0 pb-2">
        <div class="table-responsive p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Meja</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data di-load via AJAX -->
                </tbody>
            </table>
        </div>
        
        <!-- PAGINATION NUMERIC -->
        <div id="pagination" class="d-flex justify-content-center my-3"></div>

    </div>
</div>
</div>

</main>

<!-- MODAL DETAIL -->
<div class="modal fade" id="modalDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title">Detail Pesanan #<span id="idPesanan"></span></h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detailContent">
        <div class="text-center py-4">
            <div class="spinner-border text-primary"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showDetail(id) {
    const modal = new bootstrap.Modal(document.getElementById("modalDetail"));
    document.getElementById("idPesanan").textContent = String(id).padStart(4, "0");

    fetch(`get_detail_pesanan.php?id=${id}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById("detailContent").innerHTML = html;
            modal.show();
        });
}
</script>

<script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
</body>
</html>
