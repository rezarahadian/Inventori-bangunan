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
// Tambah Data
if (isset($_POST['add_customer'])) {
    $namacustomer = trim($_POST['nama_customer']); // Hilangkan spasi di awal & akhir
    $notelepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);

    // Validasi nama customer hanya boleh berisi huruf dan spasi
    if (!preg_match("/^[a-zA-Z\s]+$/", $namacustomer)) {
        echo "<script>alert('Nama customer hanya boleh berisi huruf dan spasi!'); window.history.back();</script>";
        exit();
    }

    // Validasi no telepon hanya boleh angka
    if (!preg_match("/^[0-9]+$/", $notelepon)) {
        echo "<script>alert('Nomor telepon hanya boleh berisi angka!'); window.history.back();</script>";
        exit();
    }

    // Validasi alamat boleh huruf, angka, dan spasi, tetapi tidak boleh simbol
    if (!preg_match("/^[a-zA-Z0-9\s.,]+$/", $alamat)) {
        echo "<script>alert('Alamat hanya boleh berisi huruf, angka, dan spasi!'); window.history.back();</script>";
        exit();
    }

    // Cek apakah customer sudah ada
    $cek_sql = "SELECT COUNT(*) FROM tb_customer WHERE nama_customer = ?";
    $cek_stmt = $config->prepare($cek_sql);
    $cek_stmt->bind_param("s", $namacustomer);
    $cek_stmt->execute();
    $cek_stmt->bind_result($count);
    $cek_stmt->fetch();
    $cek_stmt->close();

    if ($count > 0) {
        echo "<script>alert('customer sudah ada!'); window.history.back();</script>";
        exit();
    }

    // Insert ke database menggunakan prepared statement
    $sql = "INSERT INTO tb_customer (nama_customer, no_telepon, alamat) VALUES (?, ?, ?)";
    $stmt = $config->prepare($sql);
    $stmt->bind_param("sss", $namacustomer, $notelepon, $alamat);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='customer.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan data: " . $config->error . "');</script>";
    }

    $stmt->close();
}

// Update Data
if (isset($_POST['update_customer'])) {
    $id_customer = $_POST['id_customer'];
    $namacustomer = trim($_POST['nama_customer']);
    $notelepon = trim($_POST['no_telepon']);
    $alamat = trim($_POST['alamat']);

    // Validasi nama customer hanya boleh berisi huruf dan spasi
    if (!preg_match("/^[a-zA-Z\s]+$/", $namacustomer)) {
        echo "<script>alert('Nama customer hanya boleh berisi huruf dan spasi!'); window.history.back();</script>";
        exit();
    }

    // Validasi no telepon hanya boleh angka
    if (!preg_match("/^[0-9]+$/", $notelepon)) {
        echo "<script>alert('Nomor telepon hanya boleh berisi angka!'); window.history.back();</script>";
        exit();
    }

    // Validasi alamat boleh huruf, angka, dan spasi, tetapi tidak boleh simbol
    if (!preg_match("/^[a-zA-Z0-9\s.,]+$/", $alamat)) {
        echo "<script>alert('Alamat hanya boleh berisi huruf, angka, dan spasi!'); window.history.back();</script>";
        exit();
    }

    // Cek apakah nama customer yang baru sudah ada di database (selain dirinya sendiri)
    $cek_sql = "SELECT COUNT(*) FROM tb_customer WHERE nama_customer = ? AND id_customer != ?";
    $cek_stmt = $config->prepare($cek_sql);
    $cek_stmt->bind_param("si", $namacustomer, $id_customer);
    $cek_stmt->execute();
    $cek_stmt->bind_result($count);
    $cek_stmt->fetch();
    $cek_stmt->close();

    if ($count > 0) {
        echo "<script>alert('Nama customer sudah digunakan!'); window.history.back();</script>";
        exit();
    }

    // Update data di database menggunakan prepared statement
    $sql = "UPDATE tb_customer SET nama_customer = ?, no_telepon = ?, alamat = ? WHERE id_customer = ?";
    $stmt = $config->prepare($sql);
    $stmt->bind_param("sssi", $namacustomer, $notelepon, $alamat, $id_customer);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location.href='customer.php';</script>";
    } else {
        echo "<script>alert('Gagal mengupdate data: " . $config->error . "');</script>";
    }

    $stmt->close();
}
// Hapus Data
if (isset($_GET['delete'])) {
    $id_customer = $_GET['delete'];
    $sql = "DELETE FROM tb_customer WHERE id_customer='$id_customer'";
    if ($config->query($sql)) {
        echo "<script>alert('Data berhasil di hapus'); window.location.href='customer.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data: " . $config->error . "');</script>";
    }
}

// Ambil data user berdasarkan ID untuk ditampilkan di modal
if (isset($_GET['id_customer'])) {
    $id_customer = $_GET['id_customer'];
    $sql = "SELECT * FROM tb_customer WHERE id_customer = '$id_customer'";
    $result = $conn->query($sql);
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
$query = "SELECT * FROM tb_customer WHERE nama_customer LIKE '%$search%'";
if ($limit !== 'all') {
    $query .= " LIMIT $limit OFFSET $offset";
}
$result = mysqli_query($config, $query);

// Query untuk menghitung jumlah total data dari tabel tb_barangkeluar dengan pencarian
$query_count = "SELECT COUNT(*) AS total FROM tb_customer WHERE nama_customer LIKE '%$search%'";
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
            <a class="sidebar-brand d-flex align-items-center justify-content-center">
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
                        <a class="collapse-item" href="customer.php">Satuan</a>
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
                    <h1 class="fontuser">Data Customer</h1>
                    <br>
                    <div class="card shadow mb-4">
                        <!-- Isi tabel dan lainnya -->
                        <div class="card-header py-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="button" data-toggle="modal" data-target="#tambahDataModal">
                                Tambah Data
                            </button>
                            <!-- Form Pencarian -->
                            <form method="GET" class="form-inline">
                                <input type="text" name="search" class="form-control" placeholder="Cari Customer" value="<?= htmlspecialchars($search); ?>">
                            </form>
                        </div>
                        <div class="card-body">
                            <!-- Dropdown untuk Mengatur Jumlah Data per Halaman -->
                            <form method="GET" class="form-inline mb-3">
                                <label for="limit" class="mr-2">Tampilkan: </label>
                                <select name="limit" id="limit" class="form-control mr-2" onchange="this.form.submit()">
                                    <option value="5" <?= $limit == 5 ? 'selected' : ''; ?>>5</option>
                                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                                    <option value="all" <?= $limit === 'all' ? 'selected' : ''; ?>>Semua</option>
                                </select>
                                <input type="hidden" name="search" value="<?= htmlspecialchars($search); ?>">
                                <input type="hidden" name="page" value="1">
                            </form>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Customer</th>
                                        <th>No Telepon</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Inisialisasi query awal
                                    $sql = "SELECT * FROM tb_customer WHERE 1";

                                    // Jika ada pencarian, tambahkan kondisi WHERE
                                    if (!empty($search)) {
                                        $sql .= " AND nama_customer LIKE '%" . mysqli_real_escape_string($config, $search) . "%'";
                                    }

                                    // Tambahkan ORDER BY
                                    $sql .= " ORDER BY id_customer DESC";

                                    // Jika limit bukan "all", tambahkan LIMIT ke query
                                    if ($limit !== 'all') {
                                        $sql .= " LIMIT $offset, " . (int)$limit;
                                    }

                                    // Eksekusi query
                                    $result = $config->query($sql);
                                    if ($result->num_rows > 0) {
                                        $no = $offset + 1;
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>
                                <td>$no</td>
                                <td>{$row['nama_customer']}</td> <!-- Menampilkan nama kategori -->
                                 <td>{$row['no_telepon']}</td>
                                  <td>{$row['alamat']}</td>
                                <td>
                                    <button class='btn btn-transparent btn-sm' data-toggle='modal' data-target='#editDataModal{$row['id_customer']}'>
                                        <i class='fas fa-edit'></i>
                                    </button>
                                    <a href='?delete={$row['id_customer']}' class='btn btn-transparent btn-sm' onclick='return confirm(\"Hapus data ini?\")'>
                                        <i class='fas fa-trash-alt'></i>
                                    </a>
                                </td>
                            </tr>";
                                            $no++;
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center'>Tidak ada data customer</td></tr>";
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
                    <?php foreach ($result as $row) : ?>
                        <div class="modal fade" id="editDataModal<?= $row['id_customer']; ?>" tabindex="-1" aria-labelledby="editDataModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <form method="POST" onsubmit="return validateForm('nama_customer_edit<?= $row['id_customer']; ?>', 'no_telepon_edit<?= $row['id_customer']; ?>', 'alamat_edit<?= $row['id_customer']; ?>', 'namacustomerErrorEdit<?= $row['id_customer']; ?>', 'noTeleponErrorEdit<?= $row['id_customer']; ?>', 'alamatErrorEdit<?= $row['id_customer']; ?>')">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editDataModalLabel">Edit Data</h5>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_customer" value="<?= $row['id_customer']; ?>">

                                            <div class="mb-3">
                                                <label>Nama customer</label>
                                                <input type="text" name="nama_customer" class="form-control"
                                                    id="nama_customer_edit<?= $row['id_customer']; ?>"
                                                    value="<?= $row['nama_customer']; ?>" required
                                                    oninput="validateNama(this, 'namacustomerErrorEdit<?= $row['id_customer']; ?>')">
                                                <small id="namacustomerErrorEdit<?= $row['id_customer']; ?>" class="text-danger" style="display:none;">
                                                    Nama hanya boleh berisi huruf dan spasi!
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label>No Telepon</label>
                                                <input type="text" name="no_telepon" class="form-control"
                                                    id="no_telepon_edit<?= $row['id_customer']; ?>"
                                                    value="<?= $row['no_telepon']; ?>" required
                                                    oninput="validateTelepon(this, 'noTeleponErrorEdit<?= $row['id_customer']; ?>')">
                                                <small id="noTeleponErrorEdit<?= $row['id_customer']; ?>" class="text-danger" style="display:none;">
                                                    Nomor telepon hanya boleh berisi angka!
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label>Alamat</label>
                                                <input type="text" name="alamat" class="form-control"
                                                    id="alamat_edit<?= $row['id_customer']; ?>"
                                                    value="<?= $row['alamat']; ?>" required
                                                    oninput="validateAlamat(this, 'alamatErrorEdit<?= $row['id_customer']; ?>')">
                                                <small id="alamatErrorEdit<?= $row['id_customer']; ?>" class="text-danger" style="display:none;">
                                                    Alamat tidak boleh mengandung simbol!
                                                </small>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="submit" name="update_customer" class="btn btn-primary">Simpan</button>
                                            <button class="btn btn-secondary" type="button" data-dismiss="modal" onclick="resetForm('nama_customer_edit<?= $row['id_customer']; ?>', 'no_telepon_edit<?= $row['id_customer']; ?>', 'alamat_edit<?= $row['id_customer']; ?>', 'namacustomerErrorEdit<?= $row['id_customer']; ?>', 'noTeleponErrorEdit<?= $row['id_customer']; ?>', 'alamatErrorEdit<?= $row['id_customer']; ?>')">Batal</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Modal Tambah -->
                    <div class="modal fade" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" onsubmit="return validateForm('nama_customer', 'no_telepon', 'alamat', 'namacustomerError', 'noTeleponError', 'alamatError')">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="tambahDataModalLabel">Tambah Data</h5>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label>Nama customer</label>
                                            <input type="text" name="nama_customer" class="form-control" required
                                                placeholder="Masukkan Nama customer" id="nama_customer"
                                                oninput="validateNama(this, 'namacustomerError')">
                                            <small id="namacustomerError" class="text-danger" style="display:none;">
                                                Nama hanya boleh berisi huruf dan spasi!
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label>No Telepon</label>
                                            <input type="text" name="no_telepon" class="form-control" required
                                                placeholder="Masukkan No Telepon" id="no_telepon"
                                                oninput="validateTelepon(this, 'noTeleponError')">
                                            <small id="noTeleponError" class="text-danger" style="display:none;">
                                                Nomor telepon hanya boleh berisi angka!
                                            </small>
                                        </div>

                                        <div class="mb-3">
                                            <label>Alamat</label>
                                            <input type="text" name="alamat" class="form-control" required
                                                placeholder="Masukkan Alamat" id="alamat"
                                                oninput="validateAlamat(this, 'alamatError')">
                                            <small id="alamatError" class="text-danger" style="display:none;">
                                                Alamat tidak boleh mengandung simbol!
                                            </small>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="submit" name="add_customer" class="btn btn-primary">Simpan</button>
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal" onclick="resetForm('nama_customer', 'no_telepon', 'alamat', 'namacustomerError', 'noTeleponError', 'alamatError')">Batal</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Script Validasi -->
                    <script>
                        function validateAlamat(input, errorId) {
    let regex = /^[a-zA-Z0-9\s.,]+$/; // Mengizinkan huruf, angka, spasi, titik, dan koma
    let errorElement = document.getElementById(errorId);

    if (!regex.test(input.value)) {
        errorElement.style.display = "block";
    } else {
        errorElement.style.display = "none";
    }
}

function validateForm(namaId, teleponId, alamatId, namaErrorId, teleponErrorId, alamatErrorId) {
    let nama = document.getElementById(namaId);
    let telepon = document.getElementById(teleponId);
    let alamat = document.getElementById(alamatId);

    let namaError = document.getElementById(namaErrorId);
    let teleponError = document.getElementById(teleponErrorId);
    let alamatError = document.getElementById(alamatErrorId);

    let namaValid = /^[a-zA-Z\s]+$/.test(nama.value);
    let teleponValid = /^[0-9]+$/.test(telepon.value);
    let alamatValid = /^[a-zA-Z0-9\s.,]+$/.test(alamat.value); // Mengizinkan titik dan koma

    let errorMessages = [];

    if (!namaValid) {
        namaError.style.display = "block";
        errorMessages.push("Nama tidak valid! Hanya boleh huruf dan spasi.");
        nama.value = ""; // Reset hanya jika salah
    } else {
        namaError.style.display = "none";
    }

    if (!teleponValid) {
        teleponError.style.display = "block";
        errorMessages.push("Nomor telepon tidak valid! Hanya boleh angka.");
        telepon.value = ""; // Reset hanya jika salah
    } else {
        teleponError.style.display = "none";
    }

    if (!alamatValid) {
        alamatError.style.display = "block";
        errorMessages.push("Alamat tidak valid! Hanya boleh huruf, angka, spasi, titik, dan koma.");
        alamat.value = ""; // Reset hanya jika salah
    } else {
        alamatError.style.display = "none";
    }

    // Jika ada error, munculkan alert
    if (errorMessages.length > 0) {
        alert(errorMessages.join("\n"));
        return false;
    }

    return true;
}

                    </script>



                    <!-- Footer -->
                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto">
                            <div class="copyright text-center my-auto">
                                <span>Copyright &copy; Your Website 2020</span>
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