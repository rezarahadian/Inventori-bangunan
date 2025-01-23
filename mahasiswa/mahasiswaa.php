<?php
//menanggil koneksi database
include 'config.php'
?>

<?php
session_start();

// Periksa apakah pengguna sudah login
if (empty($_SESSION['username']) || empty($_SESSION['level'])) {
    echo "<script>alert('Maaf, untuk mengakses halaman ini Anda harus login terlebih dahulu.'); document.location='../login.php';</script>";
    exit();
}
// Ambil data username dan level dari sesi
$username = $_SESSION['username']; 
$level = $_SESSION['level'];

// Query untuk mengambil data jurusan
$query_jurusan = "SELECT nama_jurusan, jenjang FROM tb_jurusan";
$result_jurusan = mysqli_query($config, $query_jurusan);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Aplikasi absensi mahasiswa</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
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
                <div class="sidebar-brand-text mx-1"> POLITEKNIK TEDC BANDUNG </div>
            </a>
            <!-- Divider -->
            <hr class="sidebar-divider my-0">
            <!-- Nav Item - Dashboard -->
        
            <li class="nav-item">
                <a class="nav-link" href="mahasiswaa.php">
                <i class="fas fa-fw fa-user-graduate"></i>
                    <span>Data Mahasiswa</span></a>
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
                                <?php echo ucfirst(string: $username); ?> <!-- untuk menamnpilkan level ucfirst untuk hurup awal agar kapital-->
                                </span>
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#profilModal">
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
               
<?php
// Proses Edit Data
if (isset($_POST['edit'])) {
    $id_mahasiswa = $_POST['id_mahasiswa'];
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $id_jurusan = $_POST['id_jurusan'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $tgl_lahir = $_POST['tgl_lahir'];
    $alamat = $_POST['alamat'];
    $no_telepon = $_POST['no_telepon'];
    $email = $_POST['email'];

    $query_update = "UPDATE tb_mahasiswa 
                     SET nim = '$nim', nama = '$nama', id_jurusan = '$id_jurusan', 
                         jenis_kelamin = '$jenis_kelamin', tgl_lahir = '$tgl_lahir', 
                         alamat = '$alamat', no_telepon = '$no_telepon', email = '$email' 
                     WHERE id_mahasiswa = '$id_mahasiswa'";

    if (mysqli_query($config, $query_update)) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='mahasiswaa.php';</script>";
    } else {
        echo "<script>alert('Data gagal diperbarui!'); window.location='mahasiswaa.php';</script>";
    }
}

?>
                <!-- Begin Page Content -->
                <div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Data Mahasiswa</h1>
    <br>
    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Jurusan</th>
                        <th>Jenis Kelamin</th>
                        <th>Tgl Lahir</th>
                        <th>Alamat</th>
                        <th>No Telepon</th>
                        <th>Email</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php

// Query untuk mengambil data mahasiswa berdasarkan username yang sedang login
$query = "
SELECT m.*, j.nama_jurusan
FROM tb_mahasiswa m
LEFT JOIN tb_jurusan j ON m.id_jurusan = j.id_jurusan
WHERE m.id_user = (SELECT id_user FROM tb_user WHERE username = '$username')
";

// Eksekusi query
$result = mysqli_query($config, $query);

// Cek apakah query berhasil
if ($result) {
    $no = 1; // Nomor urut data
    while ($mahasiswa = mysqli_fetch_assoc($result)) {
?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($mahasiswa['nim']); ?></td>
            <td><?= htmlspecialchars($mahasiswa['nama']); ?></td>
            <td>
            <?php
                        // Ambil nama jurusan dan jenjang berdasarkan id_jurusan dari data mahasiswa
                        $result_jurusan_view = mysqli_query($config, "SELECT nama_jurusan, jenjang FROM tb_jurusan WHERE id_jurusan = '{$mahasiswa['id_jurusan']}'");

                        // Periksa apakah data ditemukan
                        if ($jurusan_view = mysqli_fetch_assoc($result_jurusan_view)) {
                            // Menampilkan nama jurusan dan jenjang
                            echo htmlspecialchars($jurusan_view['nama_jurusan']) . " - " . htmlspecialchars($jurusan_view['jenjang']);
                        } else {
                            // Menampilkan pesan jika data tidak ditemukan
                            echo "Jurusan tidak ditemukan";
                        }
                        ?>
            </td>
            <td><?= htmlspecialchars($mahasiswa['jenis_kelamin']); ?></td>
            <td><?= htmlspecialchars($mahasiswa['tgl_lahir']); ?></td>
            <td><?= htmlspecialchars($mahasiswa['alamat']); ?></td>
            <td><?= htmlspecialchars($mahasiswa['no_telepon']); ?></td>
            <td><?= htmlspecialchars($mahasiswa['email']); ?></td>
            <td>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDataModal<?= $mahasiswa['id_mahasiswa']; ?>">Edit</button>
            </td>
        </tr>
<?php
    }
} else {
    echo "Error: " . mysqli_error($config);
}
?>                
  <!-- Modal Edit Data Mahasiswa -->
<?php foreach ($result as $mahasiswa) { ?> <!-- looping untuk memastikan bahwa setiap elemen dalam array $result diolah untuk menghasilkan modal sesuai data yang ada. -->
    <div class="modal fade" id="editDataModal<?= $mahasiswa['id_mahasiswa']; ?>" tabindex="-1" role="dialog" aria-labelledby="editDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDataModalLabel">Edit Data Mahasiswa</h5>
            </div>
            <form action="mahasiswaa.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_mahasiswa" value="<?= $mahasiswa['id_mahasiswa']; ?>">
                    <div class="form-group">
                        <label for="nim">NIM</label>
                        <input type="text" name="nim" class="form-control" value="<?= htmlspecialchars($mahasiswa['nim']); ?>" readonly>  <!-- readonly agar tetap muncul tapi tidak bisa di edit -->
                    </div>
                    <div class="form-group">
                        <label for="nama">Nama</label>
                        <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($mahasiswa['nama']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="id_jurusan">Jurusan</label>
                        <select name="id_jurusan" class="form-control" required>
                            <?php
                            // Ambil daftar jurusan untuk modal edit
                            $result_jurusan_edit = mysqli_query($config, "SELECT * FROM tb_jurusan");
                            while ($jurusan_edit = mysqli_fetch_assoc($result_jurusan_edit)):
                            ?>
                                <option value="<?= $jurusan_edit['id_jurusan']; ?>" <?= $jurusan_edit['id_jurusan'] == $mahasiswa['id_jurusan'] ? 'selected' : ''; ?>>
                                    <?= htmlspecialchars($jurusan_edit['nama_jurusan']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="jenis_kelamin">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="Laki-Laki">Laki-Laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="tgl_lahir">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" class="form-control" value="<?= $mahasiswa['tgl_lahir']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($mahasiswa['alamat']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="no_telepon">No Telepon</label>
                        <input type="text" name="no_telepon" class="form-control" value="<?= htmlspecialchars($mahasiswa['no_telepon']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($mahasiswa['email']); ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php } ?>

                </tbody>
            </table>

        </div>
    </div>
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
                    <a class="btn btn-primary" href="../login.php">Logout</a>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="profilModal" tabindex="-1" role="dialog" aria-labelledby="profilModalLabel" aria-hidden="true">
<div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="profileModalLabel">Profile Mahasiswa</h5>
               
            </div>
            <div class="modal-body">
                <?php
                // Ambil hanya data username dan password berdasarkan username
                $query = "SELECT username, password FROM tb_user WHERE username = '$username'";
                $result = mysqli_query($config, $query);
                $data = mysqli_fetch_assoc($result);
                ?>
                <div class="form-group">
                <ul class="list-group">
                    <li class="list-group-item"><strong>Username:</strong> <?= $data['username']; ?></li>
                    <li class="list-group-item"><strong>Password:</strong> <?= $data['password']; ?></li>
                </ul>
             </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
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