<?php

require '../../assets/fungsi.php';
$allClient = getAllClient($konek);
$allrombongan = viewRombongan($konek);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Rombongan</title>
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
                        <h1 class="mt-4">Data Rombongan</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                        <div class="btn"> 
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahDataRombongan">
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
                                            <th>ROMBONGAN</th>
                                            <th>SALES</th>
                                            <th>PIC</th>
                                            <th>RENCANA KEDATANGAN</th>
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
        <!-- modal Tambah data Rombogan -->
        <div class="modal fade" id="tambahDataRombongan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addRombongan" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" id="noTlp" name="noTlp">
                                <div class="mb-3 row">
                                    <label for="id_rom" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="id_rom" name="id_rom" required>
                                            <option value="">---id rombongan---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="instansi" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="instansi" name="instansi" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="pic" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="pic" name="pic" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="tgl_dtng" class="col-sm-4 col-form-label">Rencana Kedatangan</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="tgl_dtng" name="tgl_dtng" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="gate" class="col-sm-4 col-form-label">Gate In</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="gate" name="gate" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="pax" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="pax" name="pax"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="harga" class="col-sm-4 col-form-label">Harga Perpax</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="harga" name="harga"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="judul" class="col-sm-4 col-form-label">Judul</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="judul" name="judul" required>
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
        <!-- akhir modal tambah data rombongan -->
        <!-- modal Update data rombongan -->
        <div class="modal fade" id="updateDataRombongan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit data Rombogan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateRombongan" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="up_IDrom" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_IDrom" name="up_IDrom" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_instansi" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_instansi" name="up_instansi" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="upPic" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="upPic" name="upPic" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="upTgl_dtng" class="col-sm-4 col-form-label">Rencana Kedatangan</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="upTgl_dtng" name="upTgl_dtng" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_gate" class="col-sm-4 col-form-label">Gate In</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_gate" name="up_gate" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_pax" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_pax" name="up_pax"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="upHarga" class="col-sm-4 col-form-label">Harga Perpax</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="upHarga" name="upHarga"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_judul" class="col-sm-4 col-form-label">Judul</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_judul" name="up_judul" required>
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
        <!-- akhir modal Update data rombongan -->
        <!-- modal Update DP rombongan -->
        <div class="modal fade" id="updateDP" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Down Payment Rombogan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateDPform" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="up_IDromDP" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_IDromDP" name="up_IDromDP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_instansiDP" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_instansiDP" name="up_instansiDP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="upPicDP" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="upPicDP" name="upPicDP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_dp" class="col-sm-4 col-form-label">Down Payment</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_dp" name="up_dp"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="picDP" class="col-sm-4 col-form-label">Upload Bukti</label>
                                    <div class="col-sm-8">
                                        <input type="file" class="form-control" id="picDP" name="picDP" accept="image/jpeg, image/png, image/jpg">
                                        <small class="form-text text-muted">Hanya JPG, JPEG, PNG. Maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="mb-3 row" id="currentDPImageRow">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8">
                                        <img id="currentDPImage" src="" alt="" style="max-width: 100%; height: auto; display: none;">
                                        <p id="noImageMessage" style="color: red; display: none;">Belum ada bukti DP terunggah.</p>
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
        <!-- akhir modal Update DP rombongan -->
        <!-- modal Update CP rombongan -->
        <div class="modal fade" id="updateClearPayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Clear Payment Rombogan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updateCPform" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="up_IDromCP" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_IDromCP" name="up_IDromCP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_instansiCP" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_instansiCP" name="up_instansiCP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="upPicCP" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="upPicCP" name="upPicCP" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_cp" class="col-sm-4 col-form-label">Clear Payment</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_cp" name="up_cp"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="picCP" class="col-sm-4 col-form-label">Upload Gambar</label>
                                    <div class="col-sm-8">
                                        <input type="file" class="form-control" id="picCP" name="picCP" accept="image/jpeg, image/png, image/jpg" required>
                                        <small class="form-text text-muted">Hanya JPG, JPEG, PNG. Maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="mb-3 row" id="currentCPImageRow">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8">
                                        <img id="currentCPImage" src="" alt="" style="max-width: 100%; height: auto; display: none;">
                                        <!-- <p id="noImageMessage" style="color: red; display: none;">Belum ada bukti DP terunggah.</p> -->
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
        <!-- akhir modal Update CP rombongan -->

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            const viewRombogan = <?= json_encode($allrombongan); ?>;
            const tbody = document.getElementById("dtClient");
            viewRombogan.forEach((item, index)=>{
                const row = document.createElement("tr");

                // Memproses tanggal
                const tanggalDb = new Date(item.date_plan);
                const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
                const kunjungan = tanggalDb.toLocaleDateString('id-ID', opsi);
                row.innerHTML =`
                <td>${index+1}</td>
                <td>${item.client_id}</td>
                <td>${item.client_name}</td>
                <td>${item.marketing}</td>
                <td>${item.client_pic}</td>
                <td>${kunjungan}</td>
                <td>
                    <div class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                        <button class="btn btn-success btnUpdateRombongan" data-bs-toggle="modal" data-bs-target="#updateDataRombongan"
                            data-id="${item.client_id}"
                            data-instansi="${item.client_name}"
                            data-pic="${item.client_pic}"
                            data-tanggal="${item.date_plan}"
                            data-gate="${item.gate_in}"
                            data-jumlah="${item.jumlah_pax}"
                            data-harga="${item.hrg_tiket}"
                            data-judul="${item.judul}"
                            ><i class="fa-solid fa-file-pen"></i>
                        </button>
                        <button class="btn btn-warning btnUpdateDP" data-bs-toggle="modal" data-bs-target="#updateDP"
                            data-id="${item.client_id}"
                            data-instansi="${item.client_name}"
                            data-pic="${item.client_pic}"
                            data-dp="${item.down_payment || ''}"
                            data-img="${item.img_dp}"
                        ><i class="fa-solid fa-hand-holding-dollar"></i>
                        </button>
                        <button class="btn btn-primary btnUpdateClearPayment" data-bs-toggle="modal" data-bs-target="#updateClearPayment"
                            data-id="${item.client_id}"
                            data-instansi="${item.client_name}"
                            data-pic="${item.client_pic}"
                            data-cp="${item.clear_payment || ''}"
                            data-img="${item.img_cp}"
                            ${item.down_payment > 0 ? '' : 'disabled'}
                        ><i class="fa-solid fa-circle-dollar-to-slot"></i>
                        </button>
                    </div>
                </td>
                `;
                tbody.appendChild(row);
            });

            const viewClient = <?= json_encode($allClient) ?>;
            const selectId = document.getElementById("id_rom");
            const modelEl = document.getElementById('tambahDataRombongan');
            const namaRombongan = document.getElementById('instansi');
            const picRombongan = document.getElementById('pic');
            const telephone = document.getElementById('noTlp');

            modelEl.addEventListener('show.bs.modal', function(){
                selectId.innerHTML = '<option value="">---id rombongan---</option>';
                namaRombongan.innerHTML = '';
                viewClient.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.client_id;
                    option.textContent = item.client_id;
                    selectId.appendChild(option);
                });
            });
            selectId.addEventListener('change', function(){
                const selectedID = this.value;

                if(selectedID){
                    const selectedClient = viewClient.find(item => item.client_id === selectedID);   
                    if(selectedClient){
                        namaRombongan.value = selectedClient.client_name;
                        picRombongan.value = selectedClient.pic;
                        telephone.value = selectedClient.phone;
                    } else {
                        namaRombongan.value = '';
                        picRombongan.value = '';
                        telephone.value = '';
                    }
                } else {
                    namaRombongan.value = '';
                    picRombongan.value = '';
                    telephone.value = '';
                }
            });
        </script>
        <script>
            $('#harga').on('input', function(e) {
                let rawValue = $(this).val();
                $(this).val(formatNumber(rawValue));
            });
            $('#addRombongan').on('submit', function(e) {
                e.preventDefault();
                const formatHrg = $('#harga').val();
                $('#harga').val(formatHrg.replace(/\./g, ''));
                const formData = $(this).serialize()+'&aksi=tambah_dataRombongan';
                console.log("data kirim ", formData);
                $('#harga').val(formatHrg);

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
                        if (response.status === "success"){
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
                            text: 'Gagal menambahkan data !!!',
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
                if(e.target.classList.contains('btnUpdateRombongan') || e.target.closest('.btnUpdateRombongan')){
                    const button = e.target.closest('.btnUpdateRombongan');
                    const id = button.getAttribute('data-id');
                    const instansi = button.getAttribute('data-instansi');
                    const pic = button.getAttribute('data-pic');
                    const tanggal = button.getAttribute('data-tanggal');
                    const gate = button.getAttribute('data-gate');
                    const jumlah = button.getAttribute('data-jumlah');
                    const harga = button.getAttribute('data-harga');
                    const judul = button.getAttribute('data-judul');

                    const formaTanggal = tanggal ? tanggal.substring(0, 10) : '';

                    document.getElementById('up_IDrom').value = id;
                    document.getElementById('up_instansi').value = instansi;
                    document.getElementById('upPic').value = pic;
                    document.getElementById('upTgl_dtng').value = formaTanggal;
                    document.getElementById('up_gate').value = gate;
                    document.getElementById('up_pax').value = jumlah;
                    document.getElementById('upHarga').value = formatNumber(harga);
                    document.getElementById('up_judul').value = judul;
                }
            });

            document.addEventListener('click',function(e){
                if(e.target.classList.contains('btnUpdateDP') || e.target.closest('.btnUpdateDP')){
                    const button = e.target.closest('.btnUpdateDP');
                    const id = button.getAttribute('data-id');
                    const instansi = button.getAttribute('data-instansi');
                    const pic = button.getAttribute('data-pic');
                    const dp = button.getAttribute('data-dp');
                    const imgPath = button.getAttribute('data-img');
                    const imgDefault = '../../assets/img/money.jpg';

                    document.getElementById('up_IDromDP').value = id;
                    document.getElementById('up_instansiDP').value = instansi;
                    document.getElementById('upPicDP').value = pic;
                    document.getElementById('up_dp').value = formatNumber(dp);

                    // --- LOGIKA MENAMPILKAN GAMBAR ---
                    const imgElement = document.getElementById('currentDPImage');
                    const noImageMsg = document.getElementById('noImageMessage');
                    
                    // --- FIX CEK GAMBAR KOSONG ---
                    let finalImagePath = "";

                    if (
                        imgPath !== null &&
                        imgPath !== undefined &&
                        imgPath.trim() !== "" &&
                        imgPath.trim().toLowerCase() !== "null" &&
                        imgPath.trim().toLowerCase() !== "undefined"
                    ) {
                        finalImagePath = '../../' + imgPath;
                    } else {
                        finalImagePath = imgDefault;
                    }

                    imgElement.src = finalImagePath;
                    imgElement.style.display = 'block';
                    noImageMsg.style.display = 'none';

                    // Reset input file ketika modal dibuka
                    document.getElementById('picDP').value = "";
                }
            });

            document.getElementById('picDP').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const previewImg = document.getElementById('currentDPImage');
                const noImageMsg = document.getElementById('noImageMessage');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result; // Ganti gambar lama ke gambar baru
                        previewImg.style.display = 'block';
                        noImageMsg.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });

            document.addEventListener('click',function(e){
                if(e.target.classList.contains('btnUpdateClearPayment') || e.target.closest('.btnUpdateClearPayment')){
                    const button = e.target.closest('.btnUpdateClearPayment');
                    const id = button.getAttribute('data-id');
                    const instansi = button.getAttribute('data-instansi');
                    const pic = button.getAttribute('data-pic');
                    const cp = button.getAttribute('data-cp');
                    const imgPath = button.getAttribute('data-img');
                    const imgDefault = '../../assets/img/money.jpg';

                    document.getElementById('up_IDromCP').value = id;
                    document.getElementById('up_instansiCP').value = instansi;
                    document.getElementById('upPicCP').value = pic;
                    document.getElementById('up_cp').value = formatNumber(cp);

                    // --- LOGIKA MENAMPILKAN GAMBAR ---
                    const imgElement = document.getElementById('currentCPImage');
                    // const noImageMsg = document.getElementById('noImageMessage');
                    
                    // --- FIX CEK GAMBAR KOSONG ---
                    let finalImagePath = "";

                    if (
                        imgPath !== null &&
                        imgPath !== undefined &&
                        imgPath.trim() !== "" &&
                        imgPath.trim().toLowerCase() !== "null" &&
                        imgPath.trim().toLowerCase() !== "undefined"
                    ) {
                        finalImagePath = '../../' + imgPath;
                    } else {
                        finalImagePath = imgDefault;
                    }

                    imgElement.src = finalImagePath;
                    imgElement.style.display = 'block';
                    // noImageMsg.style.display = 'none';

                    // Reset input file ketika modal dibuka
                    document.getElementById('picCP').value = "";
                }
            });

            document.getElementById('picCP').addEventListener('change', function(event) {
                const file = event.target.files[0];
                const previewImg = document.getElementById('currentCPImage');
                // const noImageMsg = document.getElementById('noImageMessage');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result; // Ganti gambar lama ke gambar baru
                        previewImg.style.display = 'block';
                        // noImageMsg.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                }
            });
        </script>
        <script>
            $('#upHarga').on('input', function(e) {
                let rawValue = $(this).val();
                $(this).val(formatNumber(rawValue));
            });
            $('#updateRombongan').on('submit', function(e){
                e.preventDefault();
                const formatupHrg = $('#upHarga').val();
                $('#upHarga').val(formatupHrg.replace(/\./g, ''));
                const formData = $(this).serialize() + '&aksi=update_dataRombongan';
                $('#upHarga').val(formatupHrg);
                
                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    success: function(res){
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
            //upate dp
            $('#up_dp').on('input', function(e) {
                let rawValue = $(this).val();
                $(this).val(formatNumber(rawValue));
            });
            $('#updateDPform').on('submit', function(e){
                e.preventDefault();
                // A. Bersihkan input DP dari format titik sebelum FormData dibuat
                const formattedDP = $('#up_dp').val();
                $('#up_dp').val(formattedDP.replace(/\./g, ''));

                // B. Buat FormData untuk mengirim file dan data POST
                const formData = new FormData(this);
                formData.append('aksi', 'update_dp_rombongan'); 
                console.log("data kirim DP: ", ...formData);

                // C. Kembalikan format titik di input field
                $('#up_dp').val(formattedDP);
                
                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    processData: false, // Wajib untuk FormData
                    contentType: false, // Wajib untuk FormData
                    success: function(res){
                        console.log("respon DP: ", res);
                        let response = {};
                        try {
                            response = JSON.parse(res);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Respon Salah',
                                text: 'ERROR...' });
                            return;
                        }
                        if(response.status === "success"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Update Berhasil',
                                text: 'Data DP dan Bukti berhasil diperbaharui.',
                                showConfirmButton: true,
                                confirmButtonText: 'Oke',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menyimpan data DP!!!'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", error);
                        Swal.fire({ icon: 'error', title: 'Error AJAX', text: error });
                    }
                });
            });

            //update Clear Payment
            $('#up_cp').on('input', function(e) {
                let rawValue = $(this).val();
                $(this).val(formatNumber(rawValue));
            });
            $('#updateCPform').on('submit', function(e){
                e.preventDefault();
                // A. Bersihkan input DP dari format titik sebelum FormData dibuat
                const formattedDP = $('#up_cp').val();
                $('#up_cp').val(formattedDP.replace(/\./g, ''));

                // B. Buat FormData untuk mengirim file dan data POST
                const formData = new FormData(this);
                formData.append('aksi', 'update_cp_rombongan'); 
                console.log("data kirim CP: ", ...formData);

                // C. Kembalikan format titik di input field
                $('#up_dp').val(formattedDP);
                
                $.ajax({
                    url : '../../assets/fungsi.php',
                    method : 'POST',
                    data : formData,
                    processData: false, // Wajib untuk FormData
                    contentType: false, // Wajib untuk FormData
                    success: function(res){
                        console.log("respon DP: ", res);
                        let response = {};
                        try {
                            response = JSON.parse(res);
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Format Respon Salah',
                                text: 'ERROR...' });
                            return;
                        }
                        if(response.status === "success"){
                            Swal.fire({
                                icon: 'success',
                                title: 'Update Berhasil',
                                text: 'Data Clear Payment dan Bukti berhasil diperbaharui.',
                                showConfirmButton: true,
                                confirmButtonText: 'Oke',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                            }).then((result)=>{
                                if(result.isConfirmed){
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: response.message || 'Gagal menyimpan data CP!!!'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", error);
                        Swal.fire({ icon: 'error', title: 'Error AJAX', text: error });
                    }
                });
            });

        </script>
        <script>
            // Fungsi untuk memformat angka dengan pemisah ribuan (titik)
            function formatNumber(angka) {
                if (typeof angka !== 'string') {
                    angka = String(angka);
                }
                // Hanya sisakan angka murni (jika ada koma/titik lain, hapus)
                var number_string = angka.replace(/[^0-9]/g, '');
                
                // Format dengan titik sebagai pemisah ribuan
                var sisa 	= number_string.length % 3,
                    rupiah 	= number_string.substr(0, sisa),
                    ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
                    
                if (ribuan) {
                    separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }
                
                return rupiah;
            }

            // 1. Terapkan format real-time pada modal TAMBAH
            const tambahFormattedInputs = ['#pax', '#harga'];
            tambahFormattedInputs.forEach(id => {
                $(id).on('input', function(e) {
                    // Mendapatkan nilai murni dan memformatnya kembali
                    let rawValue = $(this).val();
                    $(this).val(formatNumber(rawValue));
                });
            });

            // 2. Terapkan format real-time pada modal UPDATE
            const updateFormattedInputs = ['#up_pax', '#upHarga'];
            updateFormattedInputs.forEach(id => {
                $(id).on('input', function(e) {
                    // Mendapatkan nilai murni dan memformatnya kembali
                    let rawValue = $(this).val();
                    $(this).val(formatNumber(rawValue));
                });
            });
        </script>
    </body>
</html>