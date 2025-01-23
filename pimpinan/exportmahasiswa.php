<?php
//menanggil koneksi database
include 'config.php'
?>
<html>
<head>
  <title>Data Mahasiswa</title>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
  <style>
    #mauexport {
        border: 1px solid black;
        border-collapse: collapse;
    }

    #mauexport th, #mauexport td {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
  </style>
</head>
<body>
<div class="container">
			<h2>Data Mahasiswa</h2>
				<div class="data-tables datatable-dark">	
					<!-- Masukkan table nya disini, dimulai dari tag TABLE -->
                    <table id="mauexport">
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
                                                        </tr>
                                    </thead>
                                    <tbody>
                                  <!-- untuk menampilkan tabel data dari database -->
                                  <?php
                                    $no = 1;
                                    $mahasiswa = mysqli_query($config, "SELECT * FROM tb_mahasiswa");

                                    while ($data = mysqli_fetch_array($mahasiswa)) {
                                        $nim = $data['nim'];
                                        $nama = $data['nama'];
                                        $id_jurusan = $data['id_jurusan'];
                                        
                                        // Query untuk mengambil nama jurusan berdasarkan id_jurusan
                                        $query_jurusan_nama = "SELECT nama_jurusan FROM tb_jurusan WHERE id_jurusan = $id_jurusan";
                                        $result_jurusan_nama = mysqli_query($config, $query_jurusan_nama);

                                        // Ambil nama jurusan dari hasil query
                                        $jurusan = mysqli_fetch_assoc($result_jurusan_nama);
                                        $nama_jurusan = $jurusan['nama_jurusan'];

                                        $jeniskelamin = $data['jenis_kelamin'];
                                        $tgllahir = $data['tgl_lahir'];
                                        $alamat = $data['alamat'];
                                        $notel = $data['no_telepon'];
                                        $email = $data['email'];
                                        ?>
                                        <tr>
                                        <td><?=$no++;?></td>
                                            <td><?php echo$nim;?></td>
                                            <td><?php echo$nama;?></td>
                                            <td><?php echo$nama_jurusan;?></td>
                                            <td><?php echo$jeniskelamin;?></td>
                                            <td><?php echo$tgllahir;?></td>
                                            <td><?php echo$alamat;?></td>
                                            <td><?php echo$notel;?></td>
                                            <td><?php echo$email;?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
					        </div>
                        </div>	
<script>
$(document).ready(function() {
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'excel', 'pdf', 'print'
        ]
    } );
} );
</script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>
</body>
</html>