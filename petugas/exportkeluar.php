<?php
// menanggil koneksi database
include 'config.php';
?>
<html>

<head>
    <title>Laporan Barang Keluar</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h2> Laporan Barang Keluar</h2>
        <div class="data-tables datatable-dark">
            <form method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tgl_mulai">Tanggal Mulai:</label>
                            <input type="date" name="tgl_mulai" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tgl_selesai">Tanggal Selesai:</label>
                            <input type="date" name="tgl_selesai" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <div class="form-group">
                            <button type="submit" name="filter_tgl" class="btn btn-sm btn-info">Filter</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Masukkan tabel di sini, dimulai dari tag TABLE -->
            <table id="mauexport" class="table table-striped table-bordered">
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
                    $no = 1;
                    if (isset($_POST['filter_tgl'])) {
                        $mulai = $_POST['tgl_mulai'];
                        $selesai = $_POST['tgl_selesai'];
                        if (!empty($mulai) && !empty($selesai)) {
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
                        WHERE 
                            tb_barangkeluar.tanggal_keluar BETWEEN '$mulai' AND DATE_ADD('$selesai', INTERVAL 1 DAY)";
                        } else {
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
                            tb_customer ON tb_barangkeluar.id_customer = tb_customer.id_customer";
                        }
                    } else {
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
                        tb_customer ON tb_barangkeluar.id_customer = tb_customer.id_customer";
                    }

                    $query = mysqli_query($config, $sql);
                    while ($data = mysqli_fetch_array($query)) {
                        $nama_barang = $data['nama_barang'];
                        $kategori = $data['nama_kategori'];
                        $customer = $data['nama_customer'];
                        $jumlah_keluar = $data['jumlah_keluar'];
                        $tanggal_keluar = $data['tanggal_keluar'];
                    ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo $nama_barang; ?></td>
                            <td><?php echo $kategori; ?></td>
                            <td><?php echo $customer; ?></td>
                            <td><?php echo $jumlah_keluar; ?></td>
                            <td><?php echo $tanggal_keluar; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    </div>
    <style>
        .line {
            border-top: 2px solid black;
            width: 100%;
            margin-bottom: 10mm;
        }
    </style>
    <script>
        $(document).ready(function() {
            $('#mauexport').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'pdfHtml5',
                        text: 'Export PDF',
                        customize: function(doc) {
                            doc.pageSize = 'A4';
                            doc.pageMargins = [40, 30, 40, 30];
                            doc.defaultStyle.fontSize = 12;
                            doc.styles.tableHeader.fontSize = 14;
                            doc.content[1].margin = [10, 10, 10, 10];

                            // Menengahkan header tabel
                            doc.styles.tableHeader.alignment = 'center';

                            // Menengahkan isi tabel
                            doc.content[1].table.body.forEach(function(row, i) {
                                row.forEach(function(cell) {
                                    cell.alignment = 'center'; // Buat isi tabel rata tengah
                                });
                            });

                            // Tambahkan header toko
                            doc.content.unshift({
                                    text: 'Reza Jaya Bangunan',
                                    fontSize: 16,
                                    bold: true,
                                    margin: [0, 0, 0, 5],
                                    alignment: 'center'
                                }, {
                                    text: 'Kp Cibitung, Ganjarsari, Kec. Cikalong Wetan, Kabupaten Bandung Barat, Jawa Barat 40556',
                                    fontSize: 12,
                                    margin: [0, 0, 0, 2],
                                    alignment: 'center'
                                }, {
                                    text: 'Telp: 088564449907',
                                    fontSize: 12,
                                    margin: [0, 0, 0, 8],
                                    alignment: 'center'
                                }, {
                                    canvas: [{
                                        type: 'line',
                                        x1: 0,
                                        y1: 0,
                                        x2: 540,
                                        y2: 0,
                                        lineWidth: 1.5
                                    }]
                                }, {
                                    text: '\n'
                                } // Spasi setelah garis pemisah
                            );
                        }
                    },


                    {
                        extend: 'excelHtml5',
                        text: 'Export Excel',
                        customize: function(xlsx) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c[r]', sheet).attr('s', '42'); // Atur style Excel
                        }
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        customize: function(win) {
                            $(win.document.body).css('text-align', 'center'); // Pusatkan teks
                            $(win.document.body).find('table').css({
                                'width': '100%',
                                'border-collapse': 'collapse'
                            });
                            $(win.document.body).find('th, td').css({
                                'border': '1px solid black',
                                'padding': '8px',
                                'text-align': 'center'
                            });
                            $(win.document.body).prepend(
                                '<div class="header">Reza Jaya Bangunan</div>' +
                                '<div class="sub-header">Kp Cibitung, Ganjarsari, Kec. Cikalong Wetan, Kabupaten Bandung Barat, Jawa Barat 40556</div>' +
                                '<div class="sub-header">Telp: 0858-6444-9907</div>' +
                                '<div class="line"></div>'
                            );
                        }
                    }
                ]
            });
        });
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