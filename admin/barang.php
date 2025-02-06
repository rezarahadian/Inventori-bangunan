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
if (isset($_POST['add_barang'])) {
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $foto = $_FILES['foto']['name']; // Nama file
    $tmpFile = $_FILES['foto']['tmp_name']; // Lokasi sementara file
    $targetDir = "image/"; // Folder tujuan
    $targetFile = $targetDir . basename($foto); // Path lengkap file

    // Proses upload file
    if (move_uploaded_file($tmpFile, $targetFile)) {
        // Jika upload berhasil, simpan data ke database
        $sql = "INSERT INTO tb_barang (nama_barang, foto, id_kategori) VALUES ('$nama_barang', '$foto', '$id_kategori')";
        if ($config->query($sql)) {
            echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='barang.php';</script>";
        } else {
             echo "<script>alert('Gagal menambahkan data: " . $config->error . "');</script>";
        }
    } else {
        echo "Gagal mengupload foto.";
    }
}
// Update data barang
if (isset($_POST['update_barang'])) {
    $id_barang = $_POST['id_barang'];
    $nama_barang = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $foto = $_FILES['foto']['name']; // Nama file baru jika ada
    $tmpFile = $_FILES['foto']['tmp_name']; // Lokasi sementara file

    // Proses jika ada foto baru diupload
    if ($foto) {
        $targetDir = "image/";
        $targetFile = $targetDir . basename($foto); // Path lengkap file

        // Upload file ke server
        if (move_uploaded_file($tmpFile, $targetFile)) {
            // Update dengan foto baru
            $sql = "UPDATE tb_barang 
                    SET nama_barang = '$nama_barang', foto = '$foto', id_kategori = '$id_kategori' 
                    WHERE id_barang = '$id_barang'";
        } else {
            echo "Gagal mengupload foto.";
            exit;
        }
    } else {
        // Update tanpa mengubah foto
        $sql = "UPDATE tb_barang 
                SET nama_barang = '$nama_barang', id_kategori = '$id_kategori' 
                WHERE id_barang = '$id_barang'";
    }

    if ($config->query($sql)) {
         echo "<script>alert('Data berhasil di update!'); window.location.href='barang.php';</script>";
    } else {
         echo "<script>alert('Gagal mengupdate data: " . $config->error . "');</script>";
    }
}

// Hapus Data
if (isset($_GET['delete'])) {
    $id_barang = $_GET['delete'];
    $sql = "DELETE FROM tb_barang WHERE id_barang='$id_barang'";
    if ($config->query($sql)) {
         echo "<script>alert('Data berhasil di hapus'); window.location.href='barang.php';</script>";
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
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max($page, 1); // Pastikan page minimal 1
$offset = ($page - 1) * $limit;
// Fitur Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($config, $_GET['search']) : '';
// Query untuk mendapatkan data dengan pagination dan pencarian
$query = "SELECT * FROM tb_barang WHERE nama_barang LIKE '%$search%' LIMIT $limit OFFSET $offset";
$result = mysqli_query($config, $query);
// Query untuk menghitung jumlah total data dengan pencarian
$query_count = "SELECT COUNT(*) AS total FROM tb_barang WHERE nama_barang LIKE '%$search%'";
$count_result = mysqli_query($config, $query_count);
$total_data = mysqli_fetch_assoc($count_result)['total'];
// Menghitung jumlah total halaman
$total_pages = ceil($total_data / $limit);

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
    <link href="style.css" rel="stylesheet">
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
                <a class="nav-link" href="admin.php">
                <i class="fas fa-fw fa-th"></i>
                    <span>Dashboard</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="user.php">
                <i class="fas fa-users"></i>
                    <span> Data User</span></a>
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
                            <a class="collapse-item" href="barang.php">Barang</a>
                            <a class="collapse-item" href="satuan.php">Satuan</a>
                            <a class="collapse-item" href="kategori.php">Kategori</a>
                            <a class="collapse-item" href="stokbarang.php">Stok Barang</a>
                            <a class="collapse-item" href="barangmasuk.php">Barang Masuk</a>
                            <a class="collapse-item" href="barangkeluar.php">Barang Keluar</a>
                        </div>
                    </div>
                </li>
               
            <li class="nav-item">
                <a class="nav-link" href="supplier.php">
                  <i class="fas fa-people-arrows"></i>
                    <span> Data Supplier</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="customer.php">
                <i class="fas fa-people-arrows"></i>
                    <span> Data Customer</span></a>
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
                            <a class="collapse-item" href="stokbarang.php">Laporan Stok Barang</a>
                            <a class="collapse-item" href="barangmasuk.php">Laporan Barang Masuk</a>
                            <a class="collapse-item" href="barangkeluar.php">Laporan Barang Keluar</a>
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
            </nav>
            <!-- End of Topbar -->

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <!-- Konten Utama -->
                <h1 class="fontuser">Data Barang</h1>
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
                </select>
                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                <input type="hidden" name="page" value="1">
            </form>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Foto</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php
// Query untuk mengambil data barang dan nama kategori
$sql = "SELECT tb_barang.id_barang, tb_barang.nama_barang, tb_barang.foto, tb_kategori.nama_kategori 
        FROM tb_barang 
        INNER JOIN tb_kategori ON tb_barang.id_kategori = tb_kategori.id_kategori 
        WHERE tb_barang.nama_barang LIKE '%$search%'
        LIMIT $offset, $limit";

$result = $config->query($sql);

if ($result->num_rows > 0) {
    $no = $offset + 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
            <td>$no</td>
            <td>{$row['nama_barang']}</td>
            <td><img src='image/{$row['foto']}' alt='{$row['nama_barang']}' style='width: 50px; height: 50px;'></td>
            <td>{$row['nama_kategori']}</td>
             <td>
    <button class='btn btn-transparent btn-sm' data-toggle='modal' data-target='#editDataModal{$row['id_barang']}'>
        <i class='fas fa-edit'></i>
    </button>
    <a href='?delete={$row['id_barang']}' class='btn btn-transparent btn-sm' onclick='return confirm(\"Hapus data ini?\")'>
        <i class='fas fa-trash-alt'></i>
    </a>
</td>
        </tr>";
        $no++;
    }
} else {
    echo "<tr><td colspan='5'>Tidak ada data barang</td></tr>";
}
?>

                   
                </tbody>
            </table>

            <!-- Pagination -->
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
       
             </div><!-- End of Page Content -->
        </div><!-- End of Main Content -->
             <!-- Modal Edit -->
             <?php foreach ($result as $row) : ?>
    <div class="modal fade" id="editDataModal<?= $row['id_barang']; ?>" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" enctype="multipart/form-data"> <!-- Tambahkan enctype -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_barang" value="<?= $row['id_barang']; ?>"> <!-- Input hidden untuk id_barang -->
                        <div class="form-group">
                            <label for="nama_barang">Nama Barang</label>
                            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?= $row['nama_barang']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="foto">Foto Barang</label>
                            <input type="file" class="form-control" id="foto" name="foto">
                            <img src="image/<?= $row['foto']; ?>" alt="<?= $row['nama_barang']; ?>" width="100">
                        </div>
                        <div class="form-group">
                            <label for="id_kategori">Kategori Barang</label>
                            <select class="form-control" id="id_kategori" name="id_kategori" required>
                                <?php
                                $kategoriSql = "SELECT * FROM tb_kategori";
                                $kategoriResult = $config->query($kategoriSql);
                                while ($kategori = $kategoriResult->fetch_assoc()) {
                                    $selected = ($kategori['id_kategori'] == $row['id_kategori']) ? 'selected' : '';
                                    echo "<option value='" . $kategori['id_kategori'] . "' $selected>" . $kategori['nama_kategori'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="update_barang" class="btn btn-primary">Simpan</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
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
                    <h5 class="modal-title" id="tambahDataModalLabel">Tambah Data</h5>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required placeholder="Masukan Nama Barang">
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto Barang</label>
                        <input type="file" class="form-control" id="foto" name="foto" required>
                    </div>
                    <div class="form-group">
                        <label for="id_kategori">Kategori Barang</label>
                        <select class="form-control" id="id_kategori" name="id_kategori" required>
                            <?php
                            // Mengambil kategori dari tb_kategori
                            $kategoriSql = "SELECT * FROM tb_kategori";
                            $kategoriResult = $config->query($kategoriSql);
                            while ($kategori = $kategoriResult->fetch_assoc()) {
                                echo "<option value='" . $kategori['id_kategori'] . "'>" . $kategori['nama_kategori'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="add_barang" class="btn btn-primary">Simpan</button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </form>
    </div>
</div>
      

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