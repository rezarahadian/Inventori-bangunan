<?php
//menanggil koneksi database
include 'config.php'
?>
<html>
<head>
  <title>Program Studi</title>
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
			<h2>Program Studi</h2>
				<div class="data-tables datatable-dark">
					
					<!-- Masukkan table nya disini, dimulai dari tag TABLE -->
                    <table id="mauexport">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Jurusan</th>
                                            <th>Jenjang</th>
                                            <th>Jumlah Mahasiswa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                  <!-- untuk menampilkan tabel data dari database -->
                                    <?php
                                    $i = 1;
                                    $jurusan =mysqli_query($config,"select * from tb_dataprogramstudi");
                                    while($data=mysqli_fetch_array(result: $jurusan)){
                                    $namajurusan = $data['program_studi'];
                                    $jenjang = $data['jenjang'];
                                    $jumlahmahasiswa = $data['jumlah_mahasiswa']
                                   
                                    ?>
                                        <tr>
                                            <td><?=$i++;?></td>
                                            <td><?php echo$namajurusan;?></td>
                                            <td><?php echo$jenjang;?></td>
                                            <td><?php echo$jumlahmahasiswa;?></td>
                                        </tr>
                                        <?php
                                    };
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