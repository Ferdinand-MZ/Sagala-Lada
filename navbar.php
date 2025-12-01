<?php
// Deteksi halaman aktif (otomatis)
$current_page = basename($_SERVER['PHP_SELF']); // contoh: pesan.php, index.php, kontak.php
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4 px-lg-5 py-3 py-lg-0">
    <a href="index.php" class="navbar-brand p-0">
        <h1 class="text-primary m-0">Sagala Lada</h1>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="fa fa-bars"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto py-0 pe-4">
            <a href="index.php" 
               class="nav-item nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">
               Beranda
            </a>
            <a href="pesan.php" 
               class="nav-item nav-link <?= $current_page == 'pesan.php' ? 'active' : '' ?>">
               Pesan
            </a>
            <a href="kontak.php" 
               class="nav-item nav-link <?= $current_page == 'kontak.php' ? 'active' : '' ?>">
               Kontak
            </a>
        </div>
        <a href="/auth/login.php" class="btn btn-primary py-2 px-4">Login</a>
    </div>
</nav>