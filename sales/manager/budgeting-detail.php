<?php

require '../../assets/fungsi.php';
$allRom = getAllRombongan($konek);
$dataFs = getFasilitasWK($konek);

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client_id = $_POST['client_id'] ??'';
    $client_name = $_POST['client_name'] ??'';
}


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Budgeting-detail</title>
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
                        <!-- <h1 class="mt-4">Budgeting</h1> -->
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">
                                <i class="fa-solid fa-arrow-left"></i> Budgeting</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<?= $client_id ?>" id="" name="" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<?= $client_name ?>" id="" name="" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="" class="col-sm-1 col-form-label">HTM</label>
                                    <div class="col-sm-4">
                                    <input type="text" class="form-control" id="up_gate" name="gate">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Fasilitas Water kingdom
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#tambahFasilitas">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Fasilitas</th>
                                            <th>Harga</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Fasilitas Vendor
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" type="submit">Button</button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Fasilitas</th>
                                            <th>Harga Jual</th>
                                            <th>Harga Vendor</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Food and beverages
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#tambahFasilitas">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Fasilitas</th>
                                            <th>Harga Jual</th>
                                            <th>Harga Vendor</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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
        //modal Tambah fasilitas wk
        <div class="modal fade" id="tambahFasilitas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="fasilitasWK" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="fasilitas" class="col-sm-4 col-form-label">Fasilits</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="fasilitas" name="fasilitas" required>
                                            <option value="">---pilih kategori---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="hargaWk" class="col-sm-4 col-form-label">Harga</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="hargaWk" name="hargaWk" required>
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
        //akhir modal tambah fasilitas wk
        //modal Update fasilitas wk
        <div class="modal fade" id="tambahDataRombogan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Rombongan</h1>
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
                                    <label for="kunjungan" class="col-sm-4 col-form-label">Rencana Kunjugan</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="tgl_kunjungan" name="tgl_kunjungan">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jumlah" class="col-sm-4 col-form-label">Jumlah_Pax</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="jumlah" name="jumlah"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="gate" class="col-sm-4 col-form-label">Gate IN</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="gate" name="gate">
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
        //akhir modal Update fasilitas wk

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>

        <script>
            const viewFs = <?= json_encode($dataFs); ?>;
            const modalEl = document.getElementById('tambahFasilitas');
            modalEl.addEventListener('show.bs.modal', function(){
                const selectFs = document.getElementById("fasilitas");

                selectFs.innerHTML = '<option value="">---pilih fasilitas---</option>';

                viewFs.forEach((item) => {
                    if(item.group_detail){
                        const option = document.createElement("option");
                        option.value = item.group_detail;
                        option.textContent = item.group_detail;
                        selectFs.appendChild(option);
                    }
                });
            });   
        </script>
        <script>
            $('#fasilitasWK').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'aksi=tambah_fasilitasWK';

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success : function(res){
                        let response = {};
                        try{
                            response = JSON.parse(res);
                        } catch(e){
                            console.error("Error bukan JSON: ",res);
                            Swal.fire({
                                icon: 'error',
                                title: 'Forma Respon Salah',
                                text: 'terjai kesalahan'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        }
                    },
                })
            })
        </script>
    </body>
</html>
