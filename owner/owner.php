<?php
//menanggil koneksi database
include 'config.php'
?>
<?php
session_start();
// Periksa apakah pengguna sudah login
if (empty($_SESSION['username']) || empty($_SESSION['role'])) {
    echo "<script>alert('Maaf, untuk mengakses halaman ini Anda harus login terlebih dahulu.'); document.location='login.php';</script>";
    exit();
}
// Ambil data username dan role dari sesi
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<?php
// Tentukan jumlah data per halaman (default: 5 data per halaman)
$limit = isset($_GET['limit']) ? ($_GET['limit'] == 'all' ? 'all' : (int)$_GET['limit']) : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Pastikan page minimal 1
$offset = ($page - 1) * ($limit === 'all' ? 0 : $limit);

// Fitur Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($config, $_GET['search']) : '';

// Query untuk mendapatkan data dengan pagination dan pencarian
$query = "SELECT * FROM tb_barang WHERE nama_barang LIKE '%$search%'";
if ($limit !== 'all') {
    $query .= " LIMIT $limit OFFSET $offset";
}
$result = mysqli_query($config, $query);

// Query untuk menghitung jumlah total data dari tabel tb_barangkeluar dengan pencarian
$query_count = "SELECT COUNT(*) AS total FROM tb_barangkeluar WHERE id_barang LIKE '%$search%'";
$count_result = mysqli_query($config, $query_count);
$total_data = mysqli_fetch_assoc($count_result)['total'];
$total_pages = $limit === 'all' ? 1 : ceil($total_data / $limit);
// barang masuk
$query = "SELECT COUNT(*) AS jumlah FROM tb_barangmasuk";
$result = $config->query($query);

// Inisialisasi nilai default jika query gagal
$jumlah_barangmasuk = 0;

// Ambil hasil query
if ($result && $row = $result->fetch_assoc()) {
    $jumlah_barangmasuk = $row['jumlah'];
}
//barang keluar
$query = "SELECT COUNT(*) AS jumlah FROM tb_barangkeluar";
$result = $config->query($query);

// Inisialisasi nilai default jika query gagal
$jumlah_barangkeluar = 0;

// Ambil hasil query
if ($result && $row = $result->fetch_assoc()) {
    $jumlah_barangkeluar = $row['jumlah'];
}
//barang keluar
$query = "SELECT COUNT(*) AS jumlah FROM tb_stok";
$result = $config->query($query);

// Inisialisasi nilai default jika query gagal
$jumlah_barang = 0;

// Ambil hasil query
if ($result && $row = $result->fetch_assoc()) {
    $jumlah_barang = $row['jumlah'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Aplikasi Inventori Bangunan</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="customm.css" rel="stylesheet">
</head>

<body id="page-top">
    <!-- Wrapper untuk seluruh halaman -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
            <!-- Isi sidebar -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-1">
                    <i class="fas fa-university"></i>
                </div>
                <div class="sidebar-brand-text mx-1"> REZA JAYA BANGUNAN </div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="owner.php">
                    <i class="fas fa-fw fa-th"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan"
                    aria-expanded="true" aria-controls="collapseLaporan">
                    <i class="fas fa-sticky-note"></i>
                    <span>Data Laporan</span>
                </a>
                <div id="collapseLaporan" class="collapse" aria-labelledby="headingLaporan" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Laporan:</h6>
                        <a class="collapse-item" href="laporanstok.php">Laporan Stok Barang</a>
                        <a class="collapse-item" href="laporanmasuk.php">Laporan Barang Masuk</a>
                        <a class="collapse-item" href="laporankeluar.php">Laporan Barang Keluar</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">
            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <!-- Main Content -->
            <div id="content">
                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Topbar isi -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <ul class="navbar-nav ml-auto">
                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>
                        <div class="topbar-divider d-none d-sm-block"></div>
                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                                    <?php echo ucfirst(string: $role); ?> <!-- untuk menamnpilkan role ucfirst untuk hurup awal agar kapital-->
                                </span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">
                    <!-- Konten Utama -->
                    <h1 class="fontuser">Dasboard</h1>
                    <br>
                    <div class="row">
                        <!-- Earnings (Monthly) Card Example -->
                        <div id="layoutSidenav_content">
                            <main>
                                <div class="container-fluid px-4">
                                    <section class="main">
                                        <div class="main-skills">
                                            <div class="card">
                                                <i class="fas fa-boxes"></i>
                                                <h3>Laporan Stok Barang</h3>
                                                <p><?php echo $jumlah_barang; ?> Barang</p>
                                                <button> <a href="laporanstok.php">Lihat Detail</a></button>
                                            </div>
                                            <div class="card">
                                                <i class="fas fa-cart-plus"></i>
                                                <h3>Laporan Barang Masuk</h3>
                                                <p><?php echo $jumlah_barangmasuk; ?> Barang</p>
                                                <button> <a href="laporanmasuk.php">Lihat Detail</a></button>
                                            </div>
                                            <div class="card">
                                                <i class="fas fa-cart-arrow-down"></i>
                                                <h3>Laporan Barang Keluar</h3>
                                                <p><?php echo $jumlah_barangkeluar; ?> Barang</p>
                                                <button> <a href="laporankeluar.php">Lihat Detail</a></button>
                                            </div>

                                        </div>
                                </div>
                            </main>
                            <?php
                            require_once 'config.php';

                            $tahun = date('Y');
                            $bulan = date('m');

                            // Fungsi untuk mendapatkan nama hari dalam bahasa Indonesia
                            function getNamaHari($tanggal)
                            {
                                $hari = [
                                    'Sunday'    => 'Minggu',
                                    'Monday'    => 'Senin',
                                    'Tuesday'   => 'Selasa',
                                    'Wednesday' => 'Rabu',
                                    'Thursday'  => 'Kamis',
                                    'Friday'    => 'Jumat',
                                    'Saturday'  => 'Sabtu'
                                ];
                                return $hari[date('l', strtotime($tanggal))] . ', ' . date('d F Y', strtotime($tanggal));
                            }

                            // Query Barang Masuk
                            $queryMasukBulan = "SELECT MONTH(tanggal_masuk) AS bulan, SUM(jumlah_masuk) AS total 
                     FROM tb_barangmasuk 
                     WHERE YEAR(tanggal_masuk) = '$tahun' 
                     GROUP BY MONTH(tanggal_masuk)";

                            $queryMasukMinggu = "SELECT YEARWEEK(tanggal_masuk, 1) AS minggu, 
                            MIN(tanggal_masuk) AS awal, 
                            MAX(tanggal_masuk) AS akhir, 
                            SUM(jumlah_masuk) AS total 
                      FROM tb_barangmasuk 
                      WHERE YEAR(tanggal_masuk) = '$tahun' 
                      GROUP BY YEARWEEK(tanggal_masuk, 1)";

                            $queryMasukHari = "SELECT tanggal_masuk AS hari, SUM(jumlah_masuk) AS total 
                    FROM tb_barangmasuk 
                    WHERE YEAR(tanggal_masuk) = '$tahun' 
                    GROUP BY tanggal_masuk";

                            $resultMasukBulan = mysqli_query($config, $queryMasukBulan);
                            $resultMasukMinggu = mysqli_query($config, $queryMasukMinggu);
                            $resultMasukHari = mysqli_query($config, $queryMasukHari);

                            $barangMasukBulan = array_fill(1, 12, 0);
                            $barangMasukMinggu = [];
                            $barangMasukHari = [];

                            while ($row = mysqli_fetch_assoc($resultMasukBulan)) {
                                $barangMasukBulan[(int)$row['bulan']] = $row['total'];
                            }
                            while ($row = mysqli_fetch_assoc($resultMasukMinggu)) {
                                $barangMasukMinggu[$row['awal'] . ' - ' . $row['akhir']] = $row['total'];
                            }
                            while ($row = mysqli_fetch_assoc($resultMasukHari)) {
                                $barangMasukHari[getNamaHari($row['hari'])] = $row['total'];
                            }

                            // Query Barang Keluar
                            $queryKeluarBulan = "SELECT MONTH(tanggal_keluar) AS bulan, SUM(jumlah_keluar) AS total 
                      FROM tb_barangkeluar 
                      WHERE YEAR(tanggal_keluar) = '$tahun' 
                      GROUP BY MONTH(tanggal_keluar)";

                            $queryKeluarMinggu = "SELECT YEARWEEK(tanggal_keluar, 1) AS minggu, 
                            MIN(tanggal_keluar) AS awal, 
                            MAX(tanggal_keluar) AS akhir, 
                            SUM(jumlah_keluar) AS total 
                       FROM tb_barangkeluar 
                       WHERE YEAR(tanggal_keluar) = '$tahun' 
                       GROUP BY YEARWEEK(tanggal_keluar, 1)";

                            $queryKeluarHari = "SELECT tanggal_keluar AS hari, SUM(jumlah_keluar) AS total 
                     FROM tb_barangkeluar 
                     WHERE YEAR(tanggal_keluar) = '$tahun' 
                     GROUP BY tanggal_keluar";

                            $resultKeluarBulan = mysqli_query($config, $queryKeluarBulan);
                            $resultKeluarMinggu = mysqli_query($config, $queryKeluarMinggu);
                            $resultKeluarHari = mysqli_query($config, $queryKeluarHari);

                            $barangKeluarBulan = array_fill(1, 12, 0);
                            $barangKeluarMinggu = [];
                            $barangKeluarHari = [];

                            while ($row = mysqli_fetch_assoc($resultKeluarBulan)) {
                                $barangKeluarBulan[(int)$row['bulan']] = $row['total'];
                            }
                            while ($row = mysqli_fetch_assoc($resultKeluarMinggu)) {
                                $barangKeluarMinggu[$row['awal'] . ' - ' . $row['akhir']] = $row['total'];
                            }
                            while ($row = mysqli_fetch_assoc($resultKeluarHari)) {
                                $barangKeluarHari[getNamaHari($row['hari'])] = $row['total'];
                            }
                            ?>

                            <!-- Statistik Barang Masuk & Keluar -->
                            <div class="row">
                                <div class="col-xl-12 col-lg-12">
                                    <div class="card shadow mb-4">
                                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                            <h6 class="m-0 font-weight-bold text-primary">Data Statistik Barang Masuk & Keluar</h6>
                                            <select id="filterData" class="form-control" style="width: 200px;">
                                                <option value="bulan">Bulanan</option>
                                                <option value="minggu">Mingguan</option>
                                                <option value="hari">Harian</option>
                                            </select>
                                        </div>
                                        <div class="card-body">
                                            <canvas id="chartBarang"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Load Chart.js -->
                            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                            <script>
                                const dataBulan = {
                                    labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                    datasets: [{
                                            label: 'Barang Masuk',
                                            backgroundColor: '#000080',
                                            data: <?= json_encode(array_values($barangMasukBulan)); ?>
                                        },
                                        {
                                            label: 'Barang Keluar',
                                            backgroundColor: '#FF0000',
                                            data: <?= json_encode(array_values($barangKeluarBulan)); ?>
                                        }
                                    ]
                                };

                                const dataMinggu = {
                                    labels: <?= json_encode(array_keys($barangMasukMinggu)); ?>,
                                    datasets: [{
                                            label: 'Barang Masuk',
                                            backgroundColor: '#000080',
                                            data: <?= json_encode(array_values($barangMasukMinggu)); ?>
                                        },
                                        {
                                            label: 'Barang Keluar',
                                            backgroundColor: '#FF0000',
                                            data: <?= json_encode(array_values($barangKeluarMinggu)); ?>
                                        }
                                    ]
                                };

                                const dataHari = {
                                    labels: <?= json_encode(array_keys($barangMasukHari)); ?>,
                                    datasets: [{
                                            label: 'Barang Masuk',
                                            backgroundColor: '#000080',
                                            data: <?= json_encode(array_values($barangMasukHari)); ?>
                                        },
                                        {
                                            label: 'Barang Keluar',
                                            backgroundColor: '#FF0000',
                                            data: <?= json_encode(array_values($barangKeluarHari)); ?>
                                        }
                                    ]
                                };

                                let chart;

                                function updateChart(type) {
                                    const ctx = document.getElementById('chartBarang').getContext('2d');
                                    if (chart) chart.destroy();
                                    const data = type === 'minggu' ? dataMinggu : type === 'hari' ? dataHari : dataBulan;
                                    chart = new Chart(ctx, {
                                        type: 'line',
                                        data: data,
                                        options: {
                                            responsive: true,
                                            elements: {
                                                line: {
                                                    tension: 0.3
                                                } // Membuat garis sedikit melengkung
                                            }
                                        }
                                    });
                                }

                                document.getElementById('filterData').addEventListener('change', function() {
                                    updateChart(this.value);
                                });
                                updateChart('bulan');
                            </script>
                        </div>

                    </div>

                    <!-- End of Main Content -->
                    <!-- Footer -->
                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto">
                            <div class="copyright text-center my-auto">
                                <span>Copyright &copy; Reza Rahadian</span>
                            </div>
                        </div>
                    </footer>
                    <!-- End of Footer -->
                </div>
                <!-- End of Content Wrapper -->
            </div>
            <!-- End of Page Wrapper -->
            <a class="scroll-to-top rounded" href="#page-top">
                <i class="fas fa-angle-up"></i>
            </a>
            <!-- Logout Modal-->
            <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">LOGOUT?</h5>
                            <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                            </button>
                        </div>
                        <div class="modal-body">Apakah yakin Logout</div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                            <a class="btn btn-primary" href="../index.php">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bootstrap core JavaScript-->
            <script src="vendor/jquery/jquery.min.js"></script>
            <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <!-- Core plugin JavaScript-->
            <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
            <!-- Custom scripts for all pages-->
            <script src="js/sb-admin-2.min.js"></script>
</body>

</html>