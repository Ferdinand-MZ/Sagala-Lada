<?php
// WAJIB di paling atas sebelum HTML
if (session_status() === PHP_SESSION_NONE) session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

$role = $_SESSION['role'] ?? null;
$is_owner = $role === 'owner';
$is_admin = $role === 'admin';
?>

<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2" id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="navbar-brand px-4 py-3 m-0" href="/">
      <img src="/assets/img/logo.jpeg" class="navbar-brand-img" width="26" height="26" alt="logo">
      <span class="ms-1 text-sm text-dark fw-bold"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
    </a>
  </div>
  <hr class="horizontal dark mt-0 mb-2">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      <!-- DASHBOARD – SEMUA ROLE -->
      <li class="nav-item">
        <a class="nav-link <?= ($page == 'dashboard' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/dashboard/dashboard.php">
          <i class="material-symbols-rounded opacity-5">dashboard</i>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <!-- MENU – HANYA OWNER -->
      <?php if ($is_owner): ?>
      <li class="nav-item">
        <a class="nav-link <?= ($page == 'menu' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/menu/menu.php">
          <i class="material-symbols-rounded opacity-5">table_view</i>
          <span class="nav-link-text ms-1">Menu</span>
        </a>
      </li>
      <?php endif; ?>

      <!-- PESANAN, TRANSAKSI, PENGGUNA – HANYA ADMIN -->
      <?php if ($is_admin): ?>
      <li class="nav-item">
        <a class="nav-link <?= ($page == 'pesanan' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/pesanan/pesanan.php">
          <i class="material-symbols-rounded opacity-5">receipt_long</i>
          <span class="nav-link-text ms-1">Pesanan</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($page == 'transaksi' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/transaksi/transaksi.php">
          <i class="material-symbols-rounded opacity-5">view_in_ar</i>
          <span class="nav-link-text ms-1">Transaksi</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link <?= ($page == 'pengguna' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/pengguna/pengguna.php">
          <i class="material-symbols-rounded opacity-5">person</i>
          <span class="nav-link-text ms-1">Pengguna</span>
        </a>
      </li>
      <?php endif; ?>

      <!-- PENGATURAN AKUN – SEMUA ROLE -->
      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Pengaturan Akun</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link <?= ($page == 'profile' ? 'active bg-gradient-dark text-white' : 'text-dark') ?>" 
           href="/server/view/profile/profile.php">
          <i class="material-symbols-rounded opacity-5">person</i>
          <span class="nav-link-text ms-1">Profile</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-danger" href="/auth/logout.php">
          <i class="material-symbols-rounded opacity-5">logout</i>
          <span class="nav-link-text ms-1">Sign Out</span>
        </a>
      </li>

    </ul>
  </div>
</aside>