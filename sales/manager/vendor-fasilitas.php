<?php

require '../../assets/fungsi.php';
$vendor = getvendor($konek);
$viewVendor = getViewVendor($konek);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Fasilitas_Vendor</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
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
                        <h1 class="mt-4">Fasilitas</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Vendor</li>
                        </ol>
                        <div class="btn"> 
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahFasilitasVendor">
                            <i class="fa-solid fa-plus"></i> Add
                            </button>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Vendor</th>
                                            <th>Fasilitas</th>
                                            <th>Jumlah</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtFasilitas">
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
        //modal Tambah data fasilitas
        <div class="modal fade" id="tambahFasilitasVendor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Fasilitas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="tambahFsVend" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="vendorName" class="col-sm-4 col-form-label">Nama Vendor</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="vendorName" name="vendorName" required>
                                            <option value="">---pilih vendor---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="fasilitasName" class="col-sm-4 col-form-label">Nama Fasilitas</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="fasilitasName" name="fasilitasName" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="jumlah" name="jumlah"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
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
        //akhir modal tambah data fsilitaas
        //modal Update data fasilitas
        <div class="modal fade" id="updateFasilitas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Data Fasilitas</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateFsVend" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <input type="hidden" id="up_id" name="up_id" class="col-form-label">
                                    <label for="up_vendorName" class="col-sm-4 col-form-label">Kategori</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_vendorName" name="up_vendorName" required>
                                            <option value="">---pilih vendor---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_fasilitasName" class="col-sm-4 col-form-label">Nama Fasilitas</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_fasilitasName" name="up_fasilitasName" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_qty" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_qty" name="up_qty"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
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
        //akhir modal Update Fasilits

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            const viewfasilitas = <?= json_encode($viewVendor); ?>;
            const tbody = document.getElementById("dtFasilitas");

            viewfasilitas.forEach((item, index)=>{
                const row = document.createElement("tr");
                row.innerHTML =`
                <td>${index + 1}</td>
                <td>${item.vendor_head}</td>
                <td>${item.vendor_detail}</td>
                <td>${item.stok}</td>
                <td>
                    <button class="btn btn-warning btnUpdateVend" data-bs-toggle="modal" data-bs-target="#updateFasilitas"
                    data-id="${item.id_vendor}"
                    data-vend="${item.vendor_head}"
                    data-nama="${item.vendor_detail}"
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
            const viewVendor = <?= json_encode($vendor); ?>;

            const modelEl = document.getElementById('tambahFasilitasVendor');
            const selectVend = document.getElementById('vendorName');

            modelEl.addEventListener('show.bs.modal', function(){
                selectVend.innerHTML = '<option>---pilih vendor----</option>';

                viewVendor.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.nama_vendor;
                    option.textContent = item.nama_vendor;
                    selectVend.appendChild(option);
                });
            });
        </script>
        <script>
            $('#tambahFsVend').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambah_fasilitasVendor';
                console.log("data dikirim: ", formData);

                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    success : function(res){
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
            document.addEventListener('click', function(e){
                const viewVendor = <?= json_encode($vendor) ?>;
                if(e.target.classList.contains('btnUpdateVend') || e.target.closest('.btnUpdateVend')){
                    const button = e.target.closest('.btnUpdateVend');
                    const id_vendor = button.getAttribute('data-id');
                    const vendor = button.getAttribute('data-vend');
                    const nama = button.getAttribute('data-nama');
                    const qty = button.getAttribute('data-qty');
                    const upVend = document.getElementById("up_vendorName")

                    document.getElementById('up_id').value = id_vendor;
                    document.getElementById('up_vendorName').value = vendor;
                    document.getElementById('up_fasilitasName').value = nama;
                    document.getElementById('up_qty').value = qty;

                    upVend.innerHTML = '<option value="">---pilih kategori---</option>';
                    viewVendor.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.nama_vendor;
                        option.textContent = item.nama_vendor;
                        upVend.appendChild(option);
                    });
                    upVend.value = vendor;
                }
            });
        </script>
        <script>
            $('#updateFsVend').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=update_fasilitasVendor';
                console.log("data dikirim: ",formData);

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success: function(res){
                        console.log("data diterima;", res);
                        let response =[];
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
                        text: 'Data Fasilitas berhasil Diperbaharui.',
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
                            text: 'Gagal Memperbahrui data Fsilitas!!!',
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