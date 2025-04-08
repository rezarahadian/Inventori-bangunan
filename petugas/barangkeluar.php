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
// Tambah Data Barang Keluar
if (isset($_POST['add_barangkeluar'])) {
    $id_barang = $_POST['id_barang'];
    $id_customer = $_POST['id_customer'];
    $jumlah_keluar = (int) $_POST['jumlah_keluar']; // Pastikan ini integer
    $tanggal_keluar = $_POST['tanggal_keluar'];

    // Validasi agar hanya bisa memilih tanggal hari ini atau setelahnya
    $tanggalSekarang = strtotime(date('Y-m-d')); // Waktu sekarang
    $tanggalInput = strtotime($tanggal_keluar); // Waktu input dari form

    if ($tanggalInput < $tanggalSekarang) {
        echo "<script>alert('Tanggal keluar tidak boleh memilih tanggal yang telah lewat'); window.history.back();</script>";
        exit();
    }

    // Ambil ID Kategori berdasarkan Barang yang Dipilih
    $kategoriSql = "SELECT id_kategori FROM tb_barang WHERE id_barang = '$id_barang'";
    $kategoriResult = $config->query($kategoriSql);
    if ($kategoriResult->num_rows > 0) {
        $kategoriRow = $kategoriResult->fetch_assoc();
        $id_kategori = $kategoriRow['id_kategori']; // Ambil ID kategori yang sesuai
    } else {
        echo "<script>alert('Kategori tidak ditemukan!');</script>";
        exit(); // Hentikan eksekusi jika kategori tidak ditemukan
    }

    // Ambil jumlah stok saat ini
    $stokSql = "SELECT jumlah_stok FROM tb_stok WHERE id_barang = '$id_barang'";
    $stokResult = $config->query($stokSql);

    if ($stokResult->num_rows > 0) {
        $stokRow = $stokResult->fetch_assoc();
        $stokTersedia = (int) $stokRow['jumlah_stok'];

        $stokBaru = $stokTersedia - $jumlah_keluar;

        if ($jumlah_keluar > $stokTersedia) {
            echo "<script>alert('Tidak bisa menambah barang keluar karena melebihi stok yang ada!'); window.location.href='barangkeluar.php';</script>";
        } else {
            // Jika stok cukup, baru lakukan insert ke tb_barangkeluar
            $insertSql = "INSERT INTO tb_barangkeluar (id_barang, id_kategori, id_customer, jumlah_keluar, tanggal_keluar) 
                          VALUES ('$id_barang', '$id_kategori', '$id_customer', '$jumlah_keluar', '$tanggal_keluar')";

            if ($config->query($insertSql)) {
                // Update stok di tb_stok setelah barang keluar
                $updateStokSql = "UPDATE tb_stok SET jumlah_stok = $stokBaru WHERE id_barang = '$id_barang'";
                $config->query($updateStokSql);

                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='barangkeluar.php';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan data: " . $config->error . "');</script>";
            }
        }
    } else {
        echo "<script>alert('Barang tidak ditemukan dalam stok!'); window.location.href='barangkeluar.php';</script>";
    }
}



if (isset($_POST['update_barangkeluar'])) {
    $id_keluar = $_POST['id_keluar'];
    $jumlah_keluar = $_POST['jumlah_keluar'];

    // Dapatkan jumlah barang keluar sebelumnya
    $getOldSql = "SELECT id_barang, jumlah_keluar FROM tb_barangkeluar WHERE id_keluar = '$id_keluar'";
    $oldResult = $config->query($getOldSql);
    $oldRow = $oldResult->fetch_assoc();
    $id_barang = $oldRow['id_barang'];
    $jumlah_keluar_lama = $oldRow['jumlah_keluar'];

    // Hitung stok terbaru jika barang keluar diperbarui
    $stokSql = "SELECT jumlah_stok FROM tb_stok WHERE id_barang = '$id_barang'";
    $stokResult = $config->query($stokSql);
    $stokRow = $stokResult->fetch_assoc();
    $stokTersedia = $stokRow['jumlah_stok'] + $jumlah_keluar_lama; // Kembalikan stok lama dulu sebelum update

    if ($jumlah_keluar > $stokTersedia) {
        echo "<script>alert('Tidak bisa mengupdate barang keluar karena melebihi stok yang ada!'); window.location.href='barangkeluar.php';</script>";
    } else {
        // Update jumlah barang keluar
        $updateSql = "UPDATE tb_barangkeluar SET jumlah_keluar = '$jumlah_keluar' WHERE id_keluar = '$id_keluar'";

        if ($config->query($updateSql)) {
            // Update stok di tb_stok setelah perubahan jumlah barang keluar
            $newStok = $stokTersedia - $jumlah_keluar;
            $updateStokSql = "UPDATE tb_stok SET jumlah_stok = '$newStok' WHERE id_barang = '$id_barang'";
            $config->query($updateStokSql);

            echo "<script>alert('Data berhasil diupdate!'); window.location.href='barangkeluar.php';</script>";
        } else {
            echo "<script>alert('Gagal mengupdate data: " . $config->error . "');</script>";
        }
    }
}



// Hapus Data Barang Masuk
if (isset($_GET['delete'])) {
    $id_keluar = $_GET['delete'];
    $sql = "DELETE FROM tb_barangkeluar WHERE id_keluar='$id_keluar'";

    if ($config->query($sql)) {
        echo "<script>alert('Data berhasil dihapus!'); window.location.href='barangkeluar.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . $config->error . "');</script>";
    }
}

// Ambil data user berdasarkan ID untuk ditampilkan di modal
if (isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];
    $sql = "SELECT * FROM tb_barang WHERE id_barang = '$id_barang'";
    $result = $config->query($sql);
    $user = $result->fetch_assoc();
}

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
    <link href="custom.css" rel="stylesheet">
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
                <a class="nav-link" href="petugas.php">
                    <i class="fas fa-fw fa-th"></i>
                    <span>Dashboard</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBarang"
                    aria-expanded="true" aria-controls="collapseBarang">
                    <i class="fas fa-box-open"></i>
                    <span>Data Barang</span>
                </a>
                <div id="collapseBarang" class="collapse" aria-labelledby="headingBarang" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Data Barang:</h6>
                        <a class="collapse-item" href="stokbarang.php">Stok Barang</a>
                        <a class="collapse-item" href="barangmasuk.php">Barang Masuk</a>
                        <a class="collapse-item" href="barangkeluar.php">Barang Keluar</a>
                    </div>
                </div>
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
            <li class="nav-item">
                <a class="nav-link" href="customer.php">
                    <i class="fas fa-people-arrows"></i>
                    <span> Data Customer</span></a>
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
                    <h1 class="fontuser">Data Barang Keluar</h1>
                    <br>
                    <div class="card shadow mb-4">
                        <!-- Isi tabel dan lainnya -->
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="button" data-toggle="modal" data-target="#tambahDataModal">
                                Tambah Data
                            </button>
                            <!-- Form Pencarian -->
                            <form method="GET" class="form-inline">
                                <input type="text" name="search" class="form-control" placeholder="Cari Barang" value="<?= htmlspecialchars($search); ?>">
                            </form>
                        </div>
                        <div class="card-body">
                            <!-- Dropdown untuk Mengatur Jumlah Data per Halaman -->
                            <form method="GET" class="form-inline mb-3">
                                <label for="limit" class="mr-2">Tampilkan: </label>
                                <select name="limit" id="limit" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="5" <?= $limit == 5 ? 'selected' : ''; ?>>5</option>
                                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="15" <?= $limit == 15 ? 'selected' : ''; ?>>15</option>
                                    <option value="20" <?= $limit == 20 ? 'selected' : ''; ?>>20</option>
                                    <option value="all" <?= $limit === 'all' ? 'selected' : ''; ?>>Semua</option>
                                </select>
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                                <input type="hidden" name="page" value="1">
                            </form>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th>Customer</th>
                                        <th>Jumlah Keluar</th>
                                        <th>Tanggal Keluar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Query untuk mengambil data barang dan nama kategori
                                    $sql = "SELECT 
    tb_barangkeluar.id_keluar, 
    tb_barang.nama_barang, 
    tb_kategori.nama_kategori, 
    tb_customer.nama_customer,
    tb_barangkeluar.jumlah_keluar, 
    tb_barangkeluar.tanggal_keluar
FROM 
    tb_barangkeluar
INNER JOIN 
    tb_barang ON tb_barangkeluar.id_barang = tb_barang.id_barang
INNER JOIN 
    tb_kategori ON tb_barang.id_kategori = tb_kategori.id_kategori
INNER JOIN 
    tb_customer ON tb_barangkeluar.id_customer = tb_customer.id_customer
WHERE tb_barang.nama_barang LIKE '%$search%'
ORDER BY tb_barangkeluar.tanggal_keluar DESC";

                                    // Jika limit bukan "all", tambahkan LIMIT ke query
                                    if ($limit !== 'all') {
                                        $sql .= " LIMIT $offset, " . (int)$limit;
                                    }

                                    $result = $config->query($sql);

                                    if ($result->num_rows > 0) {
                                        $no = $offset + 1; // Penomoran dimulai dari (offset + 1)
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
            <td>$no</td>
            <td>{$row['nama_barang']}</td> 
            <td>{$row['nama_kategori']}</td> 
            <td>{$row['nama_customer']}</td> 
            <td>{$row['jumlah_keluar']}</td> 
            <td>{$row['tanggal_keluar']}</td> 
        </tr>";
                                            $no++;
                                        }
                                    } else {
                                        // Sesuaikan jumlah kolom agar tampilan tabel tetap rapi
                                        echo "<tr><td colspan='7' class='text-center'>Tidak ada data barang</td></tr>";
                                    }
                                    ?>



                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <?php if ($limit !== 'all'): ?>
                                <ul class="pagination">
                                    <li class="page-item <?= $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $page - 1; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search); ?>">Previous</a>
                                    </li>
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?= $i; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search); ?>"><?= $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= $page >= $total_pages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $page + 1; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search); ?>">Next</a>
                                    </li>
                                </ul>
                            <?php endif; ?>

                        </div><!-- End of Page Content -->
                    </div><!-- End of Main Content -->
                    <!-- Modal Edit -->
                    <!-- Form Modal Edit Data -->
                    <?php foreach ($result as $row) : ?>
                        <div class="modal fade" id="editDataModal<?= $row['id_keluar']; ?>" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editDataModalLabel">Edit Data Barang Keluar</h5>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Input hidden untuk id_keluar -->
                                            <input type="hidden" name="id_keluar" value="<?= $row['id_keluar']; ?>">

                                            <!-- Nama Barang (read-only) -->
                                            <div class="form-group">
                                                <label for="nama_barang">Nama Barang</label>
                                                <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= $row['nama_barang']; ?>" readonly>
                                            </div>

                                            <!-- Stok Masuk (editable) -->
                                            <div class="form-group">
                                                <label for="jumlah_keluar">Jumlah Keluar</label>
                                                <input type="number" class="form-control" id="jumlah_keluar" name="jumlah_keluar" value="<?= $row['jumlah_keluar']; ?>" required>
                                            </div>

                                            <!-- Nama customer (read-only) -->
                                            <div class="form-group">
                                                <label for="nama_customer">Nama customer</label>
                                                <input type="text" class="form-control" id="nama_customer" name="nama_customer" value="<?= $row['nama_customer']; ?>" readonly>
                                            </div>

                                            <!-- Tanggal Masuk (read-only) -->
                                            <div class="form-group">
                                                <label for="tanggal_keluar">Tanggal Keluar</label>
                                                <input type="text" class="form-control" id="tanggal_keluar" name="tanggal_keluar" value="<?= $row['tanggal_keluar']; ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" name="update_barangkeluar" class="btn btn-primary">Simpan Perubahan</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>



                    <?php endforeach; ?>

                    <!-- Modal Tambah -->
                    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" enctype="multipart/form-data"> <!-- Tambahkan enctype -->
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tambahDataModalLabel">Tambah Data Barang Keluar</h5>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Memilih Barang -->
                                        <div class="form-group">
                                            <label for="id_barang">Nama Barang</label>
                                            <select class="form-control" id="id_barang" name="id_barang" required>
                                                <?php
                                                // Mengambil daftar barang beserta kategorinya
                                                $barangSql = "SELECT tb_barang.id_barang, tb_barang.nama_barang, tb_kategori.nama_kategori 
                  FROM tb_barang 
                  JOIN tb_kategori ON tb_barang.id_kategori = tb_kategori.id_kategori";
                                                $barangResult = $config->query($barangSql);
                                                while ($barang = $barangResult->fetch_assoc()) {
                                                    echo "<option value='" . $barang['id_barang'] . "' 
                data-nama-kategori='" . $barang['nama_kategori'] . "'>"
                                                        . $barang['nama_barang'] .
                                                        "</option>";
                                                }
                                                ?>
                                            </select>

                                        </div>

                                        <!-- Kategori Otomatis -->
                                        <div class="form-group">
                                            <label for="id_kategori">Nama Kategori</label>
                                            <input type="text" class="form-control" id="kategori" name="id_kategori" readonly>
                                        </div>

                                        <!-- Memilih customer -->
                                        <div class="form-group">
                                            <label for="id_customer">Nama customer</label>
                                            <select class="form-control" id="id_customer" name="id_customer" required>
                                                <?php
                                                // Mengambil daftar customer dari tb_customer
                                                $customerSql = "SELECT * FROM tb_customer";
                                                $customerResult = $config->query($customerSql);
                                                while ($customer = $customerResult->fetch_assoc()) {
                                                    echo "<option value='" . $customer['id_customer'] . "'>" . $customer['nama_customer'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!-- Input Jumlah Barang Masuk -->
                                        <div class="mb-3">
                                            <label>Jumlah Keluar</label>
                                            <input type="number" name="jumlah_keluar" class="form-control" required placeholder="Masukan Jumlah Keluar">
                                        </div>

                                        <!-- Input Tanggal Barang Masuk -->
                                        <div class="form-group">
                                            <label for="tanggal_keluar">Tanggal Keluar</label>
                                            <input type="date" name="tanggal_keluar" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="add_barangkeluar" class="btn btn-primary">Simpan</button>
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <script>
                            document.addEventListener("DOMContentLoaded", function () {
        let today = new Date(); // Ambil tanggal hari ini
        let minDate = today.toISOString().split('T')[0]; // Format YYYY-MM-DD
        document.getElementById("tanggal_keluar").setAttribute("min", minDate);
    });
                        document.getElementById("id_barang").addEventListener("change", function() {
                            var selectedOption = this.options[this.selectedIndex];
                            var kategoriNama = selectedOption.getAttribute("data-nama-kategori"); // Ambil nama kategori
                            document.getElementById("kategori").value = kategoriNama; // Masukkan ke input kategori
                        });

                        // Memuat kategori secara otomatis saat halaman pertama kali dimuat
                        window.onload = function() {
                            var barangSelect = document.getElementById("id_barang");
                            var selectedOption = barangSelect.options[barangSelect.selectedIndex];
                            if (selectedOption) {
                                document.getElementById("kategori").value = selectedOption.getAttribute("data-nama-kategori");
                            }
                        };
                    </script>


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