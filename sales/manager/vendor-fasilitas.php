<?php

require '../../assets/fungsi.php';
$vendor = getvendor($konek);
$kodeVen = getKodeVen($konek);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Home</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>"
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <?php require '../../assets/head-nav.php' ?>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <?php require 'nav.php'; ?>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Vendor</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Fasilitas</li>
                        </ol>
                         <div class="btn"> 
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahFasilitasVen">
                            <i class="fa-solid fa-plus"></i> Add
                            </button>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Kode</th>
                                            <th>Nama Vendor</th>
                                            <th>Service</th>
                                            <th>PIC</th>
                                            <th>Nomor Telephone</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtVendor">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <?php require '../../assets/footer.php' ?>
                </footer>
            </div>
        </div>

        //modal Tambah data fasilitas vendor
        <div class="modal fade" id="tambahFasilitasVen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Fasilitas Vendor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="vendorFs" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="kdVendor" class="col-sm-4 col-form-label">Kode Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="kdVendor" name="kdVendor" value="<?= $kodeVen; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="fasilitas" class="col-sm-4 col-form-label">Nama Fasilitas</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="fasilitas" name="fasilitas" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="namaVen" class="col-sm-4 col-form-label">Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="namaVen" name="namaVen">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="pic" class="col-sm-4 col-form-label">Nama Pemilik</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="pic" name="pic">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="noTlp" class="col-sm-4 col-form-label">Nomor Telephone</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="noTlp" name="noTlp"
                                        pattern="[0-9]{10,13}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        //akhir modal tambah data fsilitaas vendor
        //modal Update data fasilitas vendor
        <div class="modal fade" id="updateFasilitasVen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Data Fasilitas Vendor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateFs" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="kdVendor" class="col-sm-4 col-form-label">Kode Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="kdVendor" name="kdVendor" value="<?= $kodeVen; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_fasilitas" class="col-sm-4 col-form-label">Nama Fasilitas</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_fasilitas" name="up_fasilitas" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_namaVen" class="col-sm-4 col-form-label">Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_namaVen" name="up_namaVen">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_pic" class="col-sm-4 col-form-label">Nama Pemilik</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_pic" name="up_pic">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_noTlp" class="col-sm-4 col-form-label">Nomor Telephone</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_noTlp" name="up_noTlp"
                                        pattern="[0-9]{10,13}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        //akhir modal Update Fasilits vendor

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            const viewVendorService =<?= json_encode($vendor); ?>;
            const tbody = document.getElementById('dtVendor');

            viewVendorService.forEach((item, index) =>{
                const row = document.createElement("tr");
                row.innerHTML =`
                <td>${index + 1}</td>
                <td>${item.kode_vendor}</td>
                <td>${item.nama_vendor}</td>
                <td>${item.pic}</td>
                <td>${item.noTlp}</td>
                <td>${item.service}</td>                
                <td>
                    <button class="btn btn-warning btnUpdateFs" data-bs-toggle="modal" data-bs-target="#updateFasilitasVen"
                        data-id="${item.id_markom}"
                        data-kategori="${item.group_head}"
                        data-nama="${item.group_detail}"
                        data-qty="${item.stok}"
                        >
                        <i class="fa-solid fa-file-pen"></i> edit
                    </button>
                </td>                
                `;
                tbody.appendChild(row);
            });
        </script>
        <script>
            $('#vendorFs').on('submit', function(e){
                e.preventDefault();

                const formData = $(this).serialize()+'&aksi=tambah_fasilitasVendor';
                console.log("data di kirim : ", formData);

                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    success : function(res){
                        console.log("respon server ; ", res);
                        let response = {};
                        try{
                            response = JSON.parse(res);
                            } catch (e) {
                            console.error("Respon bukan JSON:", res);
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Respon Salah',
                                text: 'Server tidak mengembalikan data JSON.'
                            });
                            return;
                        }
                        if (response.status === "success") {
                        Swal.fire({
                        icon: 'success',
                        title: 'Validasi Berhasil',
                        text: 'Data Fasilitas berhasil ditambahkan.',
                        // timer: 2000,
                        showConfirmButton: true, // Tampilkan tombol konfirmasi
                        confirmButtonText: 'Oke', // Teks tombol konfirmasi
                        allowOutsideClick: false, // Tidak bisa menutup dengan klik di luar
                        allowEscapeKey: false // Tidak bisa menutup dengan tombol Escape
                        }).then((result) => {
                            // Jika tombol "Oke" diklik
                            if (result.isConfirmed) {
                                location.reload(); // Refresh halaman
                            }
                        });
                        }else {
                            Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menambahkan data Fsilitas!!!',
                            showConfirmButton: true, // Tampilkan tombol konfirmasi
                            confirmButtonText: 'Oke', // Teks tombol konfirmasi
                            allowOutsideClick: false, // Tidak bisa menutup dengan klik di luar
                            allowEscapeKey: false // Tidak bisa menutup dengan tombol Escape
                            }).then((result) => {
                                // Jika tombol "Oke" diklik
                                if (result.isConfirmed) {
                                    location.reload(); // Refresh halaman
                                }
                                });
                        }
                    },

                });
            });
        </script>

    </body>
</html>
