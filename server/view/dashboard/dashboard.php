<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/middleware.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/auth/auth.php';

role_required(['owner', 'admin']);
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/koneksi.php';

// Hitung statistik
$hari_ini = date('Y-m-d');
$bulan_ini = date('Y-m');

$total_penjualan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(total),0) as total FROM pesanan WHERE DATE(tanggal_pesan) = '$hari_ini' AND status='Selesai'"))['total'];

$total_pelanggan_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT COUNT(*) as jml FROM pesanan WHERE DATE(tanggal_pesan) = '$hari_ini'"))['jml'];

$semua_penjualan = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT COALESCE(SUM(total),0) as total FROM pesanan WHERE status='Selesai'"))['total'];

$menu_terlaris = mysqli_fetch_assoc(mysqli_query($koneksi, 
    "SELECT m.nama_menu, SUM(dp.jumlah) as terjual 
     FROM detail_pesanan dp 
     JOIN menu m ON dp.id_menu = m.id_menu 
     JOIN pesanan p ON dp.id_pesanan = p.id_pesanan 
     WHERE p.status='Selesai' 
     GROUP BY dp.id_menu 
     ORDER BY terjual DESC LIMIT 1"));

// Ambil revenue 7 hari terakhir
$revenue = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $hari = date('D', strtotime($tgl));
    $q = mysqli_query($koneksi, "SELECT COALESCE(SUM(total),0) as rev FROM pesanan WHERE DATE(tanggal_pesan)='$tgl' AND status='Selesai'");
    $row = mysqli_fetch_assoc($q);
    $revenue[] = [
        'hari' => substr($hari, 0, 1),
        'nilai' => (float)$row['rev']
    ];
}
$labels = array_column($revenue, 'hari');
$values = array_column($revenue, 'nilai');

// Jumlah item terjual 7 hari terakhir
$items = [];
for ($i = 6; $i >= 0; $i--) {
    $tgl = date('Y-m-d', strtotime("-$i days"));
    $q = mysqli_query($koneksi, 
        "SELECT COALESCE(SUM(dp.jumlah),0) as jml 
         FROM detail_pesanan dp
         JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
         WHERE DATE(p.tanggal_pesan)='$tgl' AND p.status='Selesai'");
    $row = mysqli_fetch_assoc($q);
    $items[] = (int)$row['jml'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="/assets/template/material/assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="/assets/template/material/assets/img/favicon.png">
  <title>Dashboard</title>
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
  <link href="/assets/template/material/assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="/assets/template/material/assets/css/nucleo-svg.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link id="pagestyle" href="/assets/template/material/assets/css/material-dashboard.css?v=3.2.0" rel="stylesheet" />
  <style>
    .chart-container {
      position: relative;
      width: 100%;
      height: 300px; /* Tinggi seragam */
    }
    .card-body {
      padding: 1.5rem !important;
    }
    .chart-canvas {
      max-height: 300px !important;
    }
    @media (max-width: 768px) {
      .chart-container {
        height: 250px;
      }
    }
  </style>
</head>

<body class="g-sidenav-show bg-gray-100">
  <?php $page = 'dashboard'; ?>
  <?php include $_SERVER['DOCUMENT_ROOT'] . '/components/sidebar.php'; ?>

  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-3 shadow-none border-radius-xl" id="navbarBlur" data-scroll="true">
      <div class="container-fluid py-1 px-3">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Dashboard</li>
          </ol>
        </nav>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
          <ul class="navbar-nav d-flex align-items-center justify-content-end">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
              <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                <div class="sidenav-toggler-inner">
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                  <i class="sidenav-toggler-line"></i>
                </div>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid py-2">
      <div class="row">
        <div class="ms-3">
          <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
          <p class="mb-4">Ringkasan penjualan dan performa restoran</p>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Penjualan Hari Ini</p>
                  <h4 class="mb-0">Rp <?= number_format($total_penjualan_hari_ini, 0, ',', '.'); ?></h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">monetization_on</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
           
            <!-- 1. Penjualan Hari Ini vs Minggu Lalu -->
            <?php
            $minggu_lalu = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT COALESCE(SUM(total),0) as t FROM pesanan 
                WHERE tanggal_pesan BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 WEEK) AND DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
                AND status='Selesai'"))['t'];

            $persen_minggu = $minggu_lalu > 0 
                ? round((($total_penjualan_hari_ini - $minggu_lalu) / $minggu_lalu) * 100) 
                : ($total_penjualan_hari_ini > 0 ? 100 : 0);

            $warna_minggu = $persen_minggu >= 0 ? 'text-success' : 'text-danger';
            $ikon_minggu  = $persen_minggu >= 0 ? '↑' : '↓';
            ?>
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm">
                <span class="<?= $warna_minggu ?> font-weight-bolder">
                  <?= $ikon_minggu ?> <?= abs($persen_minggu) ?>%
                </span> dari minggu lalu
              </p>
            </div>
            
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Pelanggan Hari Ini</p>
                  <h4 class="mb-0"><?= $total_pelanggan_hari_ini; ?></h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">person</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            
            <!-- 2. Pelanggan Hari Ini vs Bulan Lalu -->
            <?php
            $bulan_lalu = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT COUNT(*) as jml FROM pesanan 
                WHERE MONTH(tanggal_pesan) = MONTH(CURDATE() - INTERVAL 1 MONTH)
                AND YEAR(tanggal_pesan) = YEAR(CURDATE() - INTERVAL 1 MONTH)"))['jml'];

            $persen_pelanggan = $bulan_lalu > 0 
                ? round((($total_pelanggan_hari_ini - $bulan_lalu) / $bulan_lalu) * 100) 
                : ($total_pelanggan_hari_ini > 0 ? 100 : 0);

            $warna_pelanggan = $persen_pelanggan >= 0 ? 'text-success' : 'text-danger';
            $ikon_pelanggan  = $persen_pelanggan >= 0 ? '↑' : '↓';
            ?>
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm">
                <span class="<?= $warna_pelanggan ?> font-weight-bolder">
                  <?= $ikon_pelanggan ?> <?= abs($persen_pelanggan) ?>%
                </span> dari bulan lalu
              </p>
            </div>
            
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card  card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Menu Terlaris</p>
                  <h4 class="mb-0"><?= $menu_terlaris['nama_menu'] ?? '-'; ?></h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">star</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">

           <!-- 3. Menu Terlaris vs Kemarin (SUDAH AMAN & BENAR) -->
<?php
// Ambil id_menu terlaris secara keseluruhan (yang sudah ada di $menu_terlaris)
$id_menu_terlaris = $menu_terlaris['id_menu'] ?? 0;

// Terjual hari ini (menu terlaris)
$terjual_hari_ini = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(dp.jumlah), 0) as jml
     FROM detail_pesanan dp
     JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
     WHERE dp.id_menu = '$id_menu_terlaris'
       AND DATE(p.tanggal_pesan) = CURDATE()
       AND p.status = 'Selesai'"))['jml'];

// Terjual kemarin (menu yang sama)
$terjual_kemarin = mysqli_fetch_assoc(mysqli_query($koneksi,
    "SELECT COALESCE(SUM(dp.jumlah), 0) as jml
     FROM detail_pesanan dp
     JOIN pesanan p ON dp.id_pesanan = p.id_pesanan
     WHERE dp.id_menu = '$id_menu_terlaris'
       AND DATE(p.tanggal_pesan) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
       AND p.status = 'Selesai'"))['jml'];

// Hitung persentase perubahan
$persen_menu = $terjual_kemarin > 0 
    ? round((($terjual_hari_ini - $terjual_kemarin) / $terjual_kemarin) * 100)
    : ($terjual_hari_ini > 0 ? 100 : 0);

$warna_menu = $persen_menu >= 0 ? 'text-success' : 'text-danger';
$panah_menu = $persen_menu >= 0 ? '↑' : '↓';
?>

<div class="card-footer p-2 ps-3">
  <p class="mb-0 text-sm">
    <span class="<?= $warna_menu ?> font-weight-bolder">
      <?= $panah_menu ?> <?= abs($persen_menu) ?>%
    </span> dari kemarin
  </p>
</div>
          
          
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-2 ps-3">
              <div class="d-flex justify-content-between">
                <div>
                  <p class="text-sm mb-0 text-capitalize">Total Penjualan</p>
                  <h4 class="mb-0">Rp <?= number_format($semua_penjualan, 0, ',', '.'); ?></h4>
                </div>
                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                  <i class="material-symbols-rounded opacity-10">savings</i>
                </div>
              </div>
            </div>
            <hr class="dark horizontal my-0">

            <!-- 4. Total Penjualan vs Kemarin (DINAMIS) -->
            <?php
            $kemarin_total = mysqli_fetch_assoc(mysqli_query($koneksi,
                "SELECT COALESCE(SUM(total),0) as t FROM pesanan 
                WHERE DATE(tanggal_pesan) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)
                AND status='Selesai'"))['t'];

            $persen_total = $kemarin_total > 0 
                ? round((($total_penjualan_hari_ini - $kemarin_total) / $kemarin_total) * 100)
                : ($total_penjualan_hari_ini > 0 ? 100 : 0);

            $warna4 = $persen_total >= 0 ? 'text-success' : 'text-danger';
            $arrow4 = $persen_total >= 0 ? '↑' : '↓';
            ?>
            <div class="card-footer p-2 ps-3">
              <p class="mb-0 text-sm">
                <span class="<?= $warna4 ?> font-weight-bolder"><?= $arrow4 ?> <?= abs($persen_total) ?>%</span> dari kemarin
              </p>
            </div>
          
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <!-- Revenue 7 Hari -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3">
              <h6 class="mb-1">Revenue 7 Hari Terakhir</h6>
              <p class="text-sm text-secondary mb-3">Penjualan harian (Selesai)</p>
              <div class="chart-container">
                <canvas id="chart-revenue"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Daily Sales -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3">
              <h6 class="mb-0">Daily Sales (7 Hari)</h6>
              <p class="text-sm mb-3">
                <span class="font-weight-bolder text-success">
                  <?php
                  $kemarin = mysqli_fetch_assoc(mysqli_query($koneksi, 
                    "SELECT COALESCE(SUM(total),0) as t FROM pesanan WHERE DATE(tanggal_pesan)=DATE_SUB(CURDATE(), INTERVAL 1 DAY) AND status='Selesai'"))['t'];
                  $persen = $kemarin > 0 ? round((($total_penjualan_hari_ini - $kemarin)/$kemarin)*100) : 100;
                  echo $persen >= 0 ? "+$persen%" : "$persen%";
                  ?>
                </span> dari kemarin
              </p>
              <div class="chart-container">
                <canvas id="chart-daily-sales"></canvas>
              </div>
              <hr class="dark horizontal my-3">
              <div class="d-flex align-items-center">
                <i class="material-symbols-rounded text-sm me-1">schedule</i>
                <small>Update otomatis</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Jumlah Item Terjual -->
        <div class="col-lg-4 col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-body p-3">
              <h6 class="mb-1">Item Terjual (7 Hari)</h6>
              <p class="text-sm text-info mb-3">Total porsi/menu per hari</p>
              <div class="chart-container">
                <canvas id="chart-items"></canvas>
              </div>
              <hr class="dark horizontal my-3">
              <div class="d-flex align-items-center">
                <i class="material-symbols-rounded text-sm me-1">schedule</i>
                <small>Update otomatis</small>
              </div>
            </div>
          </div>
        </div>

      <!-- LOG AKTIVITAS & AI INSIGHT -->
          <div class="row mt-4">
          <div class="col-12">
            <div class="card border-0 text-dark shadow-lg" 
                style="background: linear-gradient(135deg, #FDF4E0 0%, #FEF3C7 100%); border-radius: 1.2rem;">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                  <div class="d-flex align-items-center">
                    <i class="material-symbols-rounded me-3 text-warning" style="font-size: 38px;">smart_toy</i>
                    <h5 class="mb-0 fw-bold text-dark">AI Insight Hari Ini</h5>
                  </div>

                  <!-- Tombol Download Laporan -->
                  <a href="/server/controller/laporan_bulanan.php" target="_blank" 
                    class="btn btn-warning shadow-sm d-flex align-items-center px-3">
                      <i class="material-symbols-rounded me-1" style="font-size:20px;">download</i>
                      <span class="d-none d-sm-inline">Download Laporan Bulanan</span>
                  </a>
                </div>
                <?php
                $cache_file = __DIR__.'/cache/ai_insight.json';
                $cache_time = 600;

                if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time && filesize($cache_file) > 10) {
                    $insight = file_get_contents($cache_file);
                } else {
                    $insight = "Hari ini penjualan Rp ".number_format($total_penjualan_hari_ini,0,',','.')." dari $total_pelanggan_hari_ini pelanggan. Menu terlaris: ".($menu_terlaris['nama_menu']??'–').".";

                    if (!empty($_ENV['GROQ_API_KEY'])) {
                        $data = json_encode([
                            "model" => "llama3-70b-8192",
                            "messages" => [[
                                "role" => "user",
                                "content" => "Dalam 2 kalimat santai bahasa Indonesia: Hari $hari_ini_nama, penjualan Rp ".number_format($total_penjualan_hari_ini,0,',','.').", $total_pelanggan_hari_ini pelanggan, menu terlaris ".($menu_terlaris['nama_menu']??'tidak ada')."."
                            ]],
                            "max_tokens" => 120
                        ]);

                        $ch = curl_init("https://api.groq.com/openai/v1/chat/completions");
                        curl_setopt_array($ch, [
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_POST => true,
                            CURLOPT_POSTFIELDS => $data,
                            CURLOPT_HTTPHEADER => [
                                "Authorization: Bearer ".$_ENV['GROQ_API_KEY'],
                                "Content-Type: application/json"
                            ],
                            CURLOPT_TIMEOUT => 10
                        ]);

                        $resp = curl_exec($ch);
                        if ($resp && ($json = json_decode($resp, true)) && isset($json['choices'][0]['message']['content'])) {
                            $insight = trim($json['choices'][0]['message']['content']);
                        }
                        curl_close($ch);

                        @mkdir(dirname($cache_file), 0755, true);
                        file_put_contents($cache_file, $insight);
                    }
                }
                ?>

                <p class="mb-0 text-dark" style="font-size: 1.05rem; line-height: 1.7;">
                  <?= nl2br(htmlspecialchars($insight)) ?>
                </p>

                <hr class="horizontal my-4" style="border-color: #EAB308; opacity: 0.4;">

                <div class="d-flex justify-content-between align-items-end text-dark opacity-9">
                  <div>
                    <small class="text-muted">Prediksi besok</small><br>
                    <strong class="text-lg">Rp <?= number_format(round(array_sum($values)/7 * 1.1), 0, ',', '.') ?></strong>
                  </div>
                  <div class="text-end">
                    <small class="text-muted">Hari ini</small><br>
                    <strong><?= $hari_ini_nama ?>, <?= date('d/m/Y') ?></strong>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
     </main>
      </div>
    </div>
  </main>

  <!-- Core JS Files -->
  <script src="/assets/template/material/assets/js/core/popper.min.js"></script>
  <script src="/assets/template/material/assets/js/core/bootstrap.min.js"></script>
  <script src="/assets/template/material/assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="/assets/template/material/assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Revenue 7 Hari Terakhir
      new Chart(document.getElementById('chart-revenue'), {
        type: 'bar',
        data: {
          labels: <?= json_encode($labels) ?>,
          datasets: [{
            label: 'Revenue',
            data: <?= json_encode($values) ?>,
            backgroundColor: 'rgba(67, 160, 71, 0.8)',
            borderColor: '#43A047',
            borderWidth: 1,
            borderRadius: 8,
            maxBarThickness: 40
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => 'Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID')
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => 'Rp ' + Number(value).toLocaleString('id-ID'),
                font: { size: 12 }
              },
              grid: { color: '#e5e5e5' }
            },
            x: {
              ticks: { font: { size: 12 } },
              grid: { display: false }
            }
          }
        }
      });

      // Daily Sales
      new Chart(document.getElementById('chart-daily-sales'), {
        type: 'line',
        data: {
          labels: <?= json_encode($labels) ?>,
          datasets: [{
            label: 'Penjualan Harian',
            data: <?= json_encode($values) ?>,
            borderColor: '#43A047',
            backgroundColor: 'rgba(67, 160, 71, 0.2)',
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#43A047'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => 'Rp ' + Number(ctx.parsed.y).toLocaleString('id-ID')
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: value => 'Rp ' + Number(value).toLocaleString('id-ID'),
                font: { size: 12 }
              },
              grid: { color: '#e5e5e5' }
            },
            x: {
              ticks: { font: { size: 12 } },
              grid: { display: false }
            }
          }
        }
      });

      // Item Terjual
      new Chart(document.getElementById('chart-items'), {
        type: 'bar',
        data: {
          labels: <?= json_encode($labels) ?>,
          datasets: [{
            label: 'Item Terjual',
            data: <?= json_encode($items) ?>,
            backgroundColor: 'rgba(23, 162, 184, 0.8)',
            borderColor: '#17a2b8',
            borderWidth: 1,
            borderRadius: 8,
            maxBarThickness: 40
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: ctx => ctx.parsed.y + ' porsi'
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 1,
                font: { size: 12 }
              },
              grid: { color: '#e5e5e5' }
            },
            x: {
              ticks: { font: { size: 12 } },
              grid: { display: false }
            }
          }
        }
      });
    });

    // Scrollbar untuk Windows
    if (navigator.platform.indexOf('Win') > -1 && document.querySelector('#sidenav-scrollbar')) {
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
    }
  </script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="/assets/template/material/assets/js/material-dashboard.min.js?v=3.2.0"></script>
</body>
</html>