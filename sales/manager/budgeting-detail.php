<?php

require '../../assets/fungsi.php';
$dataFs = getFasilitasWK($konek);
$headFs = getKategoriFst($konek);
$vendorFs = getViewVendor($konek);
$viewFnB = getFnB($konek);
$viewCnC = getCnc($konek);

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
                        <!-- <h1 class="mt-4">Budgeting</h1> -->
                        <ol class="breadcrumb mb-4 mt-4">
                            <li class="breadcrumb-item active">
                                <i class="fa-solid fa-arrow-left"></i> Budgeting</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="" class="col-sm-2 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<?= $client_id ?>" id="" name="" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="" class="col-sm-2 col-form-label">Nama Instansi</label>
                                    <div class="col-sm-4">
                                    <input type="text" class="form-control" value="<?= $client_name ?>" id="" name="" readonly>
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
                                    <tbody id="fasilitas-wk">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Fasilitas Vendor
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#tambahFasilitasVendor">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Vendor</th>
                                            <th>Nama Fasilitas</th>
                                            <th>Jumlah</th>
                                            <th>Harga Jual</th>
                                            <th>Harga Vendor</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fasilitas-vendor">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Food and beverages
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#tambahFnB">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Fasilitas</th>
                                            <th>Qty</th>
                                            <th>Harga</th>
                                            <th>Catatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fasilitas-fnb">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                Cabana and Cabin
                            </div>
                            <div class="card-body">
                                <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#tambahCnC">
                                    <i class="fa-solid fa-plus"></i> Add
                                </button>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Pengguna</th>
                                            <th>Nama Facility</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cabanaAndcanbin">
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
        <!-- modal Tambah fasilitas wk -->
        <div class="modal fade" id="tambahFasilitas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Fasilitas Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="fasilitasWK" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="<?= $client_id ?>" id="cId" name="cId">
                                <input type="hidden" class="form-control" value="<?= $client_name ?>" id="cName" name="cName">
                                <div class="mb-3 row">
                                    <label for="kategori" class="col-sm-4 col-form-label">Kategori</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="kategori" name="kategori" required>
                                            <option value="">---pilih kategori---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="fasilitas" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="fasilitas" name="fasilitas" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="qty" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="qty" name="qty"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="hargaWk" class="col-sm-4 col-form-label">Harga</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="hargaWk" name="hargaWk"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
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
        <!-- akhir modal tambah fasilitas wk -->
        <!-- modal Update fasilitas wk -->
        <div class="modal fade" id="upateFasilitasWk" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Data Fasilitas Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="up_fasilitasWK" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="" id="idf" name="idf">
                                <input type="hidden" class="form-control" value="" id="up_kode" name="up_kode">
                                <div class="mb-3 row">
                                    <label for="up_kategori" class="col-sm-4 col-form-label">Kategori</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_kategori" name="up_kategori" required>
                                            <option value="">---pilih kategori---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_fsl" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_fsl" name="up_fsl" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_qty" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_qty" name="up_qty"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_hargaWk" class="col-sm-4 col-form-label">Harga</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_hargaWk" name="up_hargaWk"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
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
        <!-- akhir modal Update fasilitas wk -->
        <!-- modal Tambah fasilitas Vendor -->
        <div class="modal fade" id="tambahFasilitasVendor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Data Fasilitas Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="fasilitasVendor" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="<?= $client_id ?>" id="cId" name="cId">
                                <input type="hidden" class="form-control" value="<?= $client_name ?>" id="cName" name="cName">
                                <div class="mb-3 row">
                                    <label for="vendorHead" class="col-sm-4 col-form-label">Nama vendor</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="vendorHead" name="vendorHead" required>
                                            <option value="">---pilih kategori---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="namaFasilitas" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="namaFasilitas" name="namaFasilitas" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="qty" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="qty" name="qty"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="harga" class="col-sm-4 col-form-label">Harga Jual</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="harga" name="harga"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="hargaVend" class="col-sm-4 col-form-label">Harga Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="hargaVend" name="hargaVend"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
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
        <!-- khir modal tambah fasilitas Vendor -->
        <!-- modal Update fasilitas Vendor -->
        <div class="modal fade" id="upateFasilitasVendor" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Data Fasilitas Rombongan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="up_fasilitasVendor" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="" id="idFv" name="idFv">
                                <div class="mb-3 row">
                                    <label for="up_vendorHead" class="col-sm-4 col-form-label">Kategori</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_vendorHead" name="up_vendorHead" required>
                                            <option value="">---pilih vendor---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_namaFasilitas" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_namaFasilitas" name="up_namaFasilitas" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_qtyV" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_qtyV" name="up_qtyV"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_harga" class="col-sm-4 col-form-label">Harga Jual</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_harga" name="up_harga"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_hargaVend" class="col-sm-4 col-form-label">Harga Vendor</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_hargaVend" name="up_hargaVend"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
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
        <!-- akhir modal Update fasilitas Vendor -->
        <!-- modal Tambah fnb -->
        <div class="modal fade" id="tambahFnB" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Food And Beverages</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="fnbAdd" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="<?= $client_id ?>" id="cId" name="cId">
                                <input type="hidden" class="form-control" value="<?= $client_name ?>" id="cName" name="cName">
                                <input type="hidden" class="form-control" value="" id="fnb" name="fnb">
                                <div class="mb-3 row">
                                    <label for="fnbHead" class="col-sm-4 col-form-label">Menu</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="fnbHead" name="fnbHead" required>
                                            <option value="">---pilih menu---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="jumlah" name="jumlah"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="hargaFnB" class="col-sm-4 col-form-label">Harga Perpaket</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="hargaFnB" name="hargaFnB"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="ket" class="col-sm-4 col-form-label">Catatan</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" id="ket" name="ket"
                                        inputmode="text" oninput="this.value=this.value.replace(/[^a-zA-Z0-9.,\s/-]/g,'');" required></textarea>
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
        <!-- akhir modal tambah fnn -->
        <!-- modal Update fnb -->
        <div class="modal fade" id="upateFnB" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Food And Beverages</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="up_fnbAdd" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="" id="idFnB" name="idFnB">
                                <div class="mb-3 row">
                                    <label for="up_fnbHead" class="col-sm-4 col-form-label">Menu</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_fnbHead" name="up_fnbHead" required>
                                            <option value="">---pilih menu---</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_jumlah" class="col-sm-4 col-form-label">Jumlah</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_jumlah" name="up_jumlah"
                                        inputmode="numeric" maxlength="4" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_hargaFnB" class="col-sm-4 col-form-label">Harga Perpaket</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_hargaFnB" name="up_hargaFnB"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_ket" class="col-sm-4 col-form-label">Catatan</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control" id="up_ket" name="up_ket"
                                        inputmode="text" oninput="this.value=this.value.replace(/[^a-zA-Z0-9.,\s/-]/g,'');" required></textarea>
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
        <!-- akhir modal Update fnb -->
        <!-- modal Tambah cabin and cabana -->
        <div class="modal fade" id="tambahCnC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Cabana and Cabin</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cncAdd" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="" id="idCnC" name="idCnC">
                                <input type="hidden" class="form-control" value="<?= $client_name ?>" id="cName" name="cName">
                                <div class="mb-3 row">
                                    <label for="nPeng" class="col-sm-4 col-form-label">Nama Pengguna</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="nPeng" name="nPeng" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="fcnc" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="fcnc" name="fcnc" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
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
        <!-- akhir modal tambah cabin and cabana -->
        <!-- modal Update cabin and cabana -->
        <div class="modal fade" id="upateCnC" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Cabana and Cabin</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cncUpdate" method="POST" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" class="form-control" value="" id="cId" name="cId">
                                <div class="mb-3 row">
                                    <label for="up_nPeng" class="col-sm-4 col-form-label">Nama Pengguna</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_nPeng" name="up_nPeng" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_fcnc" class="col-sm-4 col-form-label">Fasilitas</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_fcnc" name="up_fcnc" required>
                                            <option value="">---pilih fasilitas---</option>
                                        </select>
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
        <!-- akhir modal Update cabin and cabana -->

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>

        <script>
            const viewFs = <?= json_encode($dataFs); ?>;
            const viewHeadFs = <?= json_encode($headFs); ?>;

            const modelEl = document.getElementById('tambahFasilitas');
            const selectHead = document.getElementById("kategori");
            const selectFs = document.getElementById("fasilitas");

            modelEl.addEventListener('show.bs.modal', function(){
                //kosongkan form
                selectHead.innerHTML = '<option value="">---pilih kategori---</option>';
                selectFs.innerHTML = '<option value="">---pilih fasilitas---</option>';

                viewHeadFs.forEach((item) =>{
                    const option = document.createElement("option");
                    option.value = item;
                    option.textContent = item;
                    selectHead.appendChild(option);
                });
            });
            //even listtener untuk dropdown ke-2
            selectHead.addEventListener('change', function(){
                const selectedHead = this.value;
                selectFs.innerHTML = '<option value="">---pilih fasilitas---</option>';

                if(selectedHead){
                    const filteredFs = viewFs.filter(item => item.group_head === selectedHead);
                    filteredFs.forEach((item) =>{
                        const option = document.createElement("option");
                        option.value = item.group_detail;
                        option.textContent = item.group_detail;
                        selectFs.appendChild(option);
                    });
                }
            });
        </script>
        <script>
            $('#fasilitasWK').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambah_fasilitasWK';

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
                    error: function(xhr, status, error) {
                        console.log("AJAX Error:", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Jaringan',
                            text: 'Terjadi kesalahan saat berkomunikasi dengan server.'
                        });
                    }
                });
            });
        </script>
        <script>
            $(document).ready(function(){
                const fasilitasId = '<?= $client_id ?>';

                if(fasilitasId){
                    $.ajax({
                        url: '../../assets/fungsi.php',
                        method: 'POST',
                        dataType: 'json',
                        data:{
                            aksi: 'getView_fasilitas',
                            fasilitas_id: fasilitasId
                        },
                        success: function(res){

                            //kosongkan data
                            $('#fasilitas-wk').empty();
                            $('#fasilitas-vendor').empty();
                            $('#fasilitas-fnb').empty();
                            $('#cabanaAndcanbin').empty();

                            //pisah data
                            let noWk = 1;
                            let noVend = 1;
                            let noFnB = 1;
                            let noCnC = 1;

                            if (Array.isArray(res)) {
                                res.forEach(item => {
                                    if(item.group_fasilitas && item.group_fasilitas.toLowerCase() === 'vendor'){
                                        $('#fasilitas-vendor').append(`
                                            <tr>
                                                <td>${noVend++}</td>
                                                <td>${item.client_id}</td>
                                                <td>${item.fasilitas_name}</td>
                                                <td>${item.qty}</td>
                                                <td>${item.price.toLocaleString('id-ID')}</td>
                                                <td>${item.price_vend.toLocaleString('id-ID')}</td>
                                                <td>
                                                    <button class="btn btn-warning btnUpdateFsVend" data-bs-toggle="modal" data-bs-target="#upateFasilitasVendor"
                                                        data-idf="${item.data_id}"
                                                        data-head="${item.client_id}"
                                                        data-fsl="${item.fasilitas_name}"
                                                        data-qty="${item.qty}"
                                                        data-price="${item.price}"
                                                        data-priceVend="${item.price_vend}"
                                                        >
                                                        <i class="fa-solid fa-file-pen"></i> edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Hapus</button>
                                                </td>
                                            </tr>
                                        `);
                                    } else if (item.group_fasilitas && item.group_fasilitas.toLowerCase() === 'food and beverages'){
                                        $('#fasilitas-fnb').append(`
                                            <tr>
                                                <td>${noFnB++}</td>
                                                <td>${item.fasilitas_name}</td>
                                                <td>${item.qty}</td>
                                                <td>${item.price.toLocaleString('id-ID')}</td>
                                                <td>${item.spec}</td>
                                                <td>
                                                    <button class="btn btn-warning btnUpdateFnB" data-bs-toggle="modal" data-bs-target="#upateFnB"
                                                        data-idFnB="${item.data_id}"
                                                        data-menu="${item.fasilitas_name}"
                                                        data-qtyFnB="${item.qty}"
                                                        data-harga="${item.price}"
                                                        data-ket="${item.spec}"
                                                        >
                                                        <i class="fa-solid fa-file-pen"></i> edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Hapus</button>
                                                </td>
                                            </tr>
                                        `);
                                    }else if(item.group_fasilitas && item.group_fasilitas.toLowerCase() === 'cabana and cabin'){
                                        $('#cabanaAndcanbin').append(`
                                            <tr>
                                                <td>${noCnC++}</td>
                                                <td>${item.catatan}</td>
                                                <td>${item.fasilitas_name}</td>
                                                <td>
                                                    <button class="btn btn-warning btnUpdateCnC" data-bs-toggle="modal" data-bs-target="#upateCnC"
                                                        data-idCnC="${item.data_id}"
                                                        data-penguna="${item.catatan}"
                                                        data-fasilitas="${item.fasilitas_name}"
                                                        >
                                                        <i class="fa-solid fa-file-pen"></i> edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Hapus</button>
                                                </td>
                                            </tr>
                                        `);
                                    } else {
                                        $('#fasilitas-wk').append(`
                                            <tr>
                                                <td>${noWk++}</td>
                                                <td>${item.fasilitas_name}</td>
                                                <td>${item.price.toLocaleString('id-ID')}</td>
                                                <td>
                                                    <button class="btn btn-warning btnUpdateFsWk" data-bs-toggle="modal" data-bs-target="#upateFasilitasWk"
                                                        data-idf="${item.data_id}"
                                                        data-kode="${item.fasilitas_id}"
                                                        data-head="${item.group_fasilitas}"
                                                        data-fsl="${item.fasilitas_name}"
                                                        data-qty="${item.qty}"
                                                        data-price="${item.price}"
                                                        >
                                                        <i class="fa-solid fa-file-pen"></i> edit
                                                    </button>
                                                    <button class="btn btn-danger btn-sm">
                                                    <i class="fa-regular fa-trash-can"></i> Hapus</button>
                                                </td>
                                            </tr>
                                        `);
                                    }
                                });
                            } else {
                                console.error("Data dari server bukan array:", res);
                            }
                        },
                        error: function(xhr, status, error){
                            console.error("Ajax Error: ", status, error);
                        }
                    });
                }
            });
        </script>
        <script>
            const upKategori = document.getElementById("up_kategori");
            const upFasilitas = document.getElementById("up_fsl");

            function populatedUpFasilitas(selectedHead, selectedFs = null) {
                upFasilitas.innerHTML = '<option value="">---pilih fasilitas---</option>';
                // Perbaikan: gunakan parameter 'selectedHead'
                if (selectedHead) { 
                    const filteredFs = viewFs.filter(item => item.group_head === selectedHead);
                    filteredFs.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.group_detail;
                        option.textContent = item.group_detail;
                        upFasilitas.appendChild(option);
                    });
                    // Perbaikan: gunakan parameter 'selectedFs'
                    if (selectedFs) {
                        upFasilitas.value = selectedFs;
                    }
                }
            }

            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('btnUpdateFsWk') || e.target.closest('.btnUpdateFsWk')) {
                    const button = e.target.closest('.btnUpdateFsWk');
                    const idf = button.getAttribute('data-idf');
                    const kode = button.getAttribute('data-kode');
                    const head = button.getAttribute('data-head');
                    const namaFs = button.getAttribute('data-fsl');
                    const qty = button.getAttribute('data-qty');
                    const harga = button.getAttribute('data-price');

                    document.getElementById('idf').value = idf;
                    document.getElementById('up_kode').value = kode;
                    document.getElementById('up_qty').value = qty;
                    document.getElementById('up_hargaWk').value = harga;

                    upKategori.innerHTML = '<option value="">---pilih kategori---</option>';
                    viewHeadFs.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item;
                        option.textContent = item;
                        upKategori.appendChild(option);
                    });
                    upKategori.value = head;

                    // Memanggil fungsi dengan variabel 'head' dan 'namaFs'
                    populatedUpFasilitas(head, namaFs);
                }
            });

            upKategori.addEventListener('change', function() {
                // Memanggil fungsi dengan nilai 'this.value'
                populatedUpFasilitas(this.value);
            });
        </script>
        <script>
            $('#up_fasilitasWK').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=update_fasilitasWK';
                console.log("data di kirim: ", formData);
                
                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success: function(res){
                        let response = [];
                        try {
                            response = JSON.parse(res);
                        } catch (e){
                            console.error("Respon Error: ", res);
                            Swal.fire({
                                icon: 'error',
                                title: 'format Respon salah'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: 'Gagal Memperbaharui data',
                                showConfirmButton: true,
                                confirmButtonText: 'Oke',
                                allowOutsideClick: false,
                                allowEscapeKey: false 
                            })
                        }
                    },
                    error: function(xhr, status, error){
                        swal.fire({
                            icon: 'error',
                            title: 'error ajax',
                            text: error
                        });
                    }
                });
            });
        </script>
        <script>
            //view dropdown vendor fasilitas
            const viewFsvend = <?= json_encode($vendorFs); ?>;
            const headVendor = [...new Set(viewFsvend.map(item => item.vendor_head))];
            const modalEl = document.getElementById('tambahFasilitasVendor');
            const vendHead = document.getElementById("vendorHead");
            const nameFast = document.getElementById("namaFasilitas");

            modalEl.addEventListener('show.bs.modal', function(){
                vendHead.innerHTML = '<option value="">---pilih kategori---</option>';
                nameFast.innerHTML = '<option value="">---pilih kategori---</option>';
                headVendor.forEach((item) =>{
                    const option = document.createElement("option");
                    option.value = item;
                    option.textContent = item;
                    vendHead.appendChild(option);
                });
            });
            vendHead.addEventListener('change', function(){
                const vendHead = this.value;
                nameFast.innerHTML = '<option value="">---pilih kategori---</option>';

                if(vendHead){
                    const filterHead = viewFsvend.filter(item => item.vendor_head === vendHead);
                    filterHead.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.vendor_detail;
                        option.textContent = item.vendor_detail;
                        nameFast.appendChild(option);
                    });
                }
            });
        </script>
        <script>
            $('#fasilitasVendor').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambah_fasilitasVend';
                console.log("data Dikirim: ", formData);

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success : function(res){
                        let response = {};
                        try{
                            response = JSON.parse(res);
                        } catch(e){
                            console.log("Eror Bukan Json: ", res);
                            swal.fire({
                                icon: 'error',
                                title: 'form respon salah',
                                text: 'Terjadi masalah'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error){
                        console.log("AJAX Error:", error);
                        swal.fire({
                            icon: 'error',
                            title: 'Error Jaringan'
                        });
                    }
                });
            });

            //update
            const upVendorHed = document.getElementById("up_vendorHead");
            const upNamaFasilits = document.getElementById("up_namaFasilitas");

            function updateFasilitasVend(headVendor, nameFast = null){
                upNamaFasilits.innerHTML = '<option value="">---pilih fasilits---</option>'

                if(headVendor){
                    const filterFsV = viewFsvend.filter(item => item.vendor_head === headVendor);
                    filterFsV.forEach((item) => {
                         const option = document.createElement("option");
                        option.value = item.vendor_detail;
                        option.textContent = item.vendor_detail;
                        upNamaFasilits.appendChild(option);
                    })
                    if(nameFast){
                        upNamaFasilits.value = nameFast;
                    }
                }
            }
            document.addEventListener('click', function(e){
                if(e.target.classList.contains('btnUpdateFsVend') || e.target.closest('.btnUpdateFsVend')){
                    const button = e.target.closest('.btnUpdateFsVend');
                    const idFv = button.getAttribute('data-idf');
                    const vendor = button.getAttribute('data-head');
                    const fasilitas = button.getAttribute('data-fsl');
                    const qty = button.getAttribute('data-qty');
                    const harga = button.getAttribute('data-price');
                    const hargaJual = button.getAttribute('data-priceVend');

                    document.getElementById('idFv').value = idFv;
                    document.getElementById('up_qtyV').value = qty;
                    document.getElementById('up_harga').value = harga;
                    document.getElementById('up_hargaVend').value = hargaJual;

                    upVendorHed.innerHTML = '<option value="">---pilih kategori---</option>';
                    headVendor.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item;
                        option.textContent = item;
                        upVendorHed.appendChild(option);
                    });
                    upVendorHed.value = vendor;
                    updateFasilitasVend(vendor, fasilitas);
                }
            });
            upVendorHed.addEventListener('change', function(){
                updateFasilitasVend(this.value);
            });

            $('#up_fasilitasVendor').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=update_fasilitasVend';

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success: function(res){
                        let response = [];
                        try{
                            response = JSON.parse(res);
                        } catch (e) {
                            console.log("Respon Error: ", res);
                            swal.fire({
                                icon: 'error',
                                title: 'format respon salah'
                            });
                            return;
                        }
                        if(response.status = "success"){
                            location.reload();
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'gagal',
                                text: 'Gagal Memperbharui Data',
                                showConfirmButton: true,
                                confirmButtonText: 'Oke',
                                allowEscapeKey: false,
                                allowOutsideClick: false
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error Ajax',
                            text: error
                        });
                    }
                });
            });
        </script>
        <script>
            const viewMenuFnB = <?= json_encode($viewFnB); ?>;
            const modelEll = document.getElementById('tambahFnB');
            const selectFnB = document.getElementById("fnbHead");

            document.getElementById('fnb').value = viewMenuFnB[0].group_head;
            modelEll.addEventListener('show.bs.modal', function(){
                selectFnB.innerHTML = '<option value="">---pilih menu---</option>';
                viewMenuFnB.forEach((item) =>{
                    const option = document.createElement("option");
                    option.value = item.group_detail;
                    option.textContent = item.group_detail;
                    selectFnB.appendChild(option);
                });
            });
            //tambah
            $('#fnbAdd').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambahFnB';

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success: function(res){
                        let response = {};
                        try{
                            response = JSON.parse(res);
                        } catch(e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'respon salah',
                                text: 'Terjadi kesalahan jaringan'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error){
                        swal.fire({
                            icon: 'error',
                            title: 'Error Jaringan'
                        });
                    }
                });
            });

            //update
            const upFnB = document.getElementById("up_fnbHead");

            document.addEventListener('click', function(e){
                if(e.target.classList.contains('btnUpdateFnB') || e.target.closest('.btnUpdateFnB')){
                    const button = e.target.closest('.btnUpdateFnB');
                    const idFnB = button.getAttribute('data-idFnB');
                    const menu = button.getAttribute('data-menu');
                    const qty = button.getAttribute('data-qtyFnB');
                    const harga = button.getAttribute('data-harga');
                    const catatan = button.getAttribute('data-ket');

                    document.getElementById('idFnB').value = idFnB;
                    document.getElementById('up_jumlah').value = qty;
                    document.getElementById('up_hargaFnB').value = harga;
                    document.getElementById('up_ket').value = catatan;

                    upFnB.innerHTML = '<option value="">---pilih kategori---</option>';
                    viewMenuFnB.forEach((item) =>{
                        const option = document.createElement("option");
                        option.value = item.group_detail;
                        option.textContent = item.group_detail;
                        upFnB.appendChild(option);
                    });
                    upFnB.value = menu;
                }
            });

            $('#up_fnbAdd').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=updateFnB';
                // console.log("data up: ", formData);

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success: function(res){
                        // console.log("server res: ", res);
                        let response = [];
                        try{
                            response = JSON.parse(res);
                        } catch(e) {
                            swal.fire({
                                icon: 'error',
                                title: 'formt respon salah'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        } else {
                            swal.fire({
                                icon: 'error',
                                title: 'gagal',
                                text: 'Gagal memperbaharui data',
                                showConfirmButton: true,
                                confirmButtonText: 'Oke',
                                allowEscapeKey: false,
                                allowOutsideClick: false
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        swal.fire({
                            icon: 'error',
                            title: 'error ajax',
                            text: error
                        });
                    }
                });
            });
        </script>
        <script>
            const viewCnc = <?= json_encode($viewCnC); ?>;
            const modalCnc = document.getElementById('tambahCnC');
            const selectCnc = document.getElementById("fcnc");

            modalCnc.addEventListener('show.bs.modal', function(){
                selectCnc.innerHTML = '<option value="">---pilih fasilitas---</option>';
                viewCnc.forEach((item) => {
                    const option = document.createElement("option");
                    option.value = item.facility_name;
                    option.textContent = item.facility_name;
                    selectCnc.appendChild(option);
                });
            });

            $('#cncAdd').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=tambah_cabanaAndcabin';
                console.log("data kirim: ", formData);

                $.ajax({
                    url: '../../assets/fungsi.php',
                    method: 'POST',
                    data: formData,
                    success : function(res){
                        console.log("server: ", res);
                        let response = {};
                        try{
                            response = JSON.parse(res);
                        } catch(e){
                            Swal.fire({
                                icon: 'error',
                                title: 'respon salah',
                                text: 'Terjadi kesalahan saat load'
                            });
                            return;
                        }
                        if(response.status === "success"){
                            location.reload();
                        }
                    },
                    error: function(xhr, status, error){
                        console.log("ajax error: ", error);
                        Swal.fire({
                            icon: 'error',
                            title: 'error jaringan'
                        });
                    }
                });
            });

            //update
            const upFcnc = document.getElementById("up_fcnc");
            document.addEventListener('click', function(e){
                if(e.target.classList.contains('btnUpdateCnC') || e.target.closest('.btnUpdateCnC')){
                    const button = e.target.closest('.btnUpdateCnC');
                    const idCnC = button.getAttribute('data-idCnC');
                    const pengguna = button.getAttribute('data-penguna');
                    const fasilitas = button.getAttribute('data-fasilitas');

                    document.getElementById('idCnC').value = idCnC;
                    document.getElementById('up_nPeng').value = pengguna;

                    upFcnc.innerHTML = '<option value="">---pilih fasilitas---</option>';
                    viewCnc.forEach((item) => {
                        const option = document.createElement("option");
                        option.value = item.facility_name;
                        option.textContent = item.facility_name;
                        upFcnc.appendChild(option);
                    });
                    upFcnc.value = fasilitas;
                }
            })
        </script>
    </body>
</html>
