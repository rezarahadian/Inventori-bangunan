<?php
//menanggil koneksi database
include 'config.php'
?>

<?php
session_start();
// Periksa apakah pengguna sudah login
if (empty($_SESSION['username']) || empty($_SESSION['role'])) {
    echo "<script>alert('Maaf, untuk mengakses halaman ini Anda harus login terlebih dahulu.'); document.location='../index.php';</script>";
    exit();
}
// Ambil data username dan role dari sesi
$username = $_SESSION['username']; 
$role = $_SESSION['role'];

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

// Menyiapkan query menggunakan prepared statements
$query_barang = "SELECT COUNT(*) AS total_barang FROM tb_barang";
$query_barang_masuk = "SELECT COUNT(*) AS total_barang_masuk FROM tb_barangmasuk";
$query_barang_keluar = "SELECT COUNT(*) AS total_barang_keluar FROM tb_barangkeluar";
$query_supplier = "SELECT COUNT(*) AS total_supplier FROM tb_supplier";

// Eksekusi query dan ambil hasilnya
$result_barang = mysqli_query($config, $query_barang);
$row_barang = $result_barang ? mysqli_fetch_assoc($result_barang) : ['total_barang' => 0];

$result_barang_masuk = mysqli_query($config, $query_barang_masuk);
$row_barang_masuk = $result_barang_masuk ? mysqli_fetch_assoc($result_barang_masuk) : ['total_barang_masuk' => 0];

$result_barang_keluar = mysqli_query($config, $query_barang_keluar);
$row_barang_keluar = $result_barang_keluar ? mysqli_fetch_assoc($result_barang_keluar) : ['total_barang_keluar' => 0];

$result_supplier = mysqli_query($config, $query_supplier);
$row_supplier = $result_supplier ? mysqli_fetch_assoc($result_supplier) : ['total_supplier' => 0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Aplikasi inventori bangunan</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
         <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="owner.css" rel="stylesheet">
    <style>
    /* Pastikan konten tidak tertutup navbar */
.content {
    margin-left: 0px;
    padding: 20px;

}

        .stat-card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 150px;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            color: white;
        }

        .stat-barang { background-color: #007bff; }
        .stat-barang-masuk { background-color: #28a745; }
        .stat-barang-keluar { background-color: #ffc107; color: black; }
        .stat-supplier { background-color: #e83e8c; }

        .stat-card h3 {
            margin-bottom: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .stat-card span {
            font-size: 24px;
            font-weight: bold;
        }

        .chart-container {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }

        .chart-box {
            width: 30%;
            height: 300px;
            margin-left: 250px;
        }

    </style>
</head>
<body id="page-top">
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
                <div class="sidebar-brand-icon rotate-n-1">
                <i class="fas fa-university"></i>
                </div>
                <div class="sidebar-brand-text mx-1"> Reza Jaya Bangunan</div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
            <li class="nav-item">
                <a class="nav-link" href="owner.php">
                <i class="fas fa-fw fa-th"></i>
                    <span >Dashboard</span></a>
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
                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    <!-- Topbar Navbar -->
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
                                <?php echo ucfirst(string: $role); ?> <!-- untuk menamnpilkan level ucfirst untuk hurup awal agar kapital-->
                                </span> 
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
                    </ul>
                </nav>
                <!-- End of Topbar -->
                <!-- Begin Page Content -->
                <!-- Content -->
    <div class="content">
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card stat-barang">
                    <h3>Data Barang</h3>
                    <span><?= $row_barang['total_barang']; ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-barang-masuk">
                    <h3>Barang Masuk</h3>
                    <span><?= $row_barang_masuk['total_barang_masuk']; ?></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card stat-barang-keluar">
                    <h3>Barang Keluar</h3>
                    <span><?= $row_barang_keluar['total_barang_keluar']; ?></span>
                </div>
            </div>
            
        </div>

        <!-- Statistik Barang Masuk & Keluar -->
<div class="row">
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Statistik Barang Masuk & Keluar</h6>
            </div>
            <div class="card-body">
                <canvas id="chartBarang"></canvas>
            </div>
            <div class="chart-box">
                <canvas id="donutChart"></canvas>
            </div>
        </div>
    </div>
</div>
    </div>

    <!-- Script untuk grafik -->
    <script>
        var ctx = document.getElementById('chartBarang').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets: [{
                   
                    data: [
                       
                        <?= $row_barang_masuk['total_barang_masuk']; ?>,
                        <?= $row_barang_keluar['total_barang_keluar']; ?>,
                    ],
                    backgroundColor: [ '#28a745', '#ffc107', '#e83e8c'],
                    borderColor: [ '#28a745', '#ffc107', '#e83e8c'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        var donutCtx = document.getElementById('donutChart').getContext('2d');
        var donutChart = new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Data Barang', 'Barang Masuk', 'Barang Keluar'],
                datasets: [{
                    label: 'Jumlah',
                    data: [
                        <?= $row_barang['total_barang']; ?>,
                        <?= $row_barang_masuk['total_barang_masuk']; ?>,
                        <?= $row_barang_keluar['total_barang_keluar']; ?>,
                    ],
                    backgroundColor: ['#007bff', '#28a745', '#ffc107']
                }]
            },
            options: {
                responsive: true
            }
        });
    </script>
    <script>
    document.getElementById("notifButton").addEventListener("click", function() {
        // Hilangkan badge merah saat diklik
        document.getElementById("notifBadge").style.display = "none";
    });
</script>
                <!-- /.container-fluid -->
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

    <!-- Scroll to Top Button-->
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
                </div>
                <div class="modal-body">Apakah Yakin Logout</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>
</body>

</html>