<?php

require '../../assets/fungsi.php';
$allRom = getAllClient($konek);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Client</title>
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
                        <h1 class="mt-4">Client</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                        <div class="btn"> 
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataClient">
                            <i class="fa-solid fa-plus"></i> Add
                            </button>
                        </div>
                        <div class="card mb-4">
                            <!-- <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div> -->
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>ID</th>
                                            <th>INSTANSI</th>
                                            <th>SALES</th>
                                            <th>PIC</th>
                                            <th>TELEPHONE</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtClient">
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
        //modal Tambah data Rombogan
        <div class="modal fade" id="tambahDataClient" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Client</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="tambahClient" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="Kode" class="col-sm-4 col-form-label">Kode</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="kode" name="kode" value="<?= $code; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="instansi" class="col-sm-4 col-form-label">Nama Instansi</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="instansi" name="instansi" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="pic" class="col-sm-4 col-form-label">Nama PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="pic" name="pic" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="noTlp" class="col-sm-4 col-form-label">Nomor Telephone</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="noTlp" name="noTlp"
                                        pattern="[0-9]{10,13}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-4 col-form-label">Alamat</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="alamat" id="alamat" required></textarea>
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
        <!-- akhir modal tambah data Client -->
        <!-- modal Update data Client -->
        <div class="modal fade" id="updateDataClient" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update data Client</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateClient" action="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="up_kode" class="col-sm-4 col-form-label">Kode</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_kode" name="kode" value="<?= $code; ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_instansi" class="col-sm-4 col-form-label">Nama Instansi</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_instansi" name="instansi" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_pic" class="col-sm-4 col-form-label">Nama PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_pic" name="pic" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_noTlp" class="col-sm-4 col-form-label">Nomor Telephone</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_noTlp" name="noTlp"
                                        pattern="[0-9]{10,13}" inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_alamat" class="col-sm-4 col-form-label">Alamat</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" name="alamat" id="up_alamat" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- akhir modal Update data Client -->

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            const viewRombogan = <?= json_encode($allRom); ?>;
            const tbody = document.getElementById("dtClient");
            viewRombogan.forEach((item, index)=>{
                const row = document.createElement("tr");
                row.innerHTML =`
                <td>${index+1}</td>
                <td>${item.client_id}</td>
                <td>${item.client_name}</td>
                <td>${item.marketing_name}</td>
                <td>${item.pic}</td>
                <td>${item.phone}</td>
                <td>
                    <button class="btn btn-success btnUpdateClient" data-bs-toggle="modal" data-bs-target="#updateDataClient"
                        data-id="${item.client_id}"
                        data-instansi="${item.client_name}"
                        data-pic="${item.pic}"
                        data-noTlp="${item.phone}"
                        data-alamat="${item.address}"
                        ><i class="fa-solid fa-file-pen"></i> Edit
                    </button>
                </td>
                `;
                tbody.appendChild(row);
            });
        </script>
        <script>
            $('#tambahClient').on('submit', function(e) {
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambah_dataClient';
                console.log("data kirim ", formData);

                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    success: function(res){
                        console.log("respon : ", res);
                        let response = {};
                        try {
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
                        text: 'Data Client berhasil ditambahkan.',
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
                            text: 'Gagal menambahkan data Client!!!',
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
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error AJAX',
                            text: error
                        });
                    }
                });

            });
        </script>
        <script>
            document.addEventListener('click',function(e){
                if(e.target.classList.contains('btnUpdateClient') || e.target.closest('.btnUpdateClient')){
                    const button = e.target.closest('.btnUpdateClient');
                    const id = button.getAttribute('data-id');
                    const instansi = button.getAttribute('data-instansi');
                    const pic = button.getAttribute('data-pic');
                    const noTlp = button.getAttribute('data-noTlp');
                    const alamat = button.getAttribute('data-alamat');

                    document.getElementById('up_kode').value = id;
                    document.getElementById('up_instansi').value = instansi;
                    document.getElementById('up_pic').value = pic;
                    document.getElementById('up_noTlp').value = noTlp;
                    document.getElementById('up_alamat').value = alamat;
                }
            });
        </script>
        <script>
            $('#updateClient').on('submit', function(e){
                e.preventDefault();

                const formData = $(this).serialize() + '&aksi=update_dataClient';
                console.log("data dikirim :",formData);
                
                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    success: function(res){
                        console.log("respon dari server update: ", res);
                        let response = {};
                        try {
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
                        if(response.status === "success"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Update Berhsil',
                                text: 'Data Berhasil Diperbaharui',
                                showConfirmButton: true,
                                confirmButtonText: 'oke',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    location.reload();
                                }
                            });
                        }else{
                            Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: 'Gagal menambahkan data Client!!!',
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
                    error: function(xhr, status, error) {
                    console.log("AJAX Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error AJAX',
                        text: error
                    });
                    }
                });
            });
        </script>

    </body>
</html>
