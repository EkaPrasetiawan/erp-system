<?php

require '../../assets/fungsi.php';

$client_id = '';
$client_name='';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $client_id = $_POST['client_id'] ?? '';
    $client_name = $_POST['client_name'] ?? '';
}

$viewPay = viewPayemnt($konek, $client_id);
$datarombongan = getRombonganOk($konek, $client_id);

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
                        <h1 class="mt-4">Rombongan Payment</h1>
                        <div class="btn">
                            <a class="nav-link" href="rombongan.php">
                                <div class="sb-nav-link-icon fw-bold"><i class="fa-solid fa-arrow-left"></i>
                                   Data Rombongan
                                </div>
                            </a>
                        </div>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                        <div class="btn"> 
                            <button type="button" class="btn btn-primary "
                            data-id="<?= $client_id ?>" data-bs-toggle="modal" data-bs-target="#addPayment">
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
                                            <th>NOMINAL</th>
                                            <th>METODE</th>
                                            <th>TANGGAL</th>
                                            <th>KETERANGAN</th>
                                            <th>AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtPayment">
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
        <!-- modal add payment -->
        <div class="modal fade btnAddPayment" id="addPayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Payment Rombogan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addPay" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label for="idPay" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="idPay" name="idPay" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="instansi" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="instansi" name="instansi" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="picPay" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="picPay" name="picPay" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jenis" class="col-sm-4 col-form-label">Jenis Pembayaran</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="jenis" name="jenis" required>
                                            <option value="">---pilih kategori---</option>
                                            <option value="DP">Down Payment</option>
                                            <option value="Pelunasan">Clear Payment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="metode" class="col-sm-4 col-form-label">Metode</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="metode" name="metode" required>
                                            <option value="">---pilih metode---</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="tgl_pay" class="col-sm-4 col-form-label">Tanggal Pembayaran</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="tgl_pay" name="tgl_pay" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="price" class="col-sm-4 col-form-label">Nominal</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="price" name="price"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="imgPay" class="col-sm-4 col-form-label">Upload Bukti</label>
                                    <div class="col-sm-8">
                                        <input type="file" class="form-control" id="imgPay" name="imgPay" accept="image/jpeg, image/png, image/jpg">
                                        <small class="form-text text-muted">Hanya JPG, JPEG, PNG. Maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8">
                                        <img id="currentPayImage" src="" alt="" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px dashed #ddd;
                                        display: flex; align-items: center; justify-content: center;">
                                        <p id="noImageMessage" style="color: red; display: none;">Belum ada bukti pembayaran terunggah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnSimpanBayar" class="btn btn-primary" disabled>Simpan</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- akhir modal add payment -->
        <!-- modal tampil picture -->
        <div class="modal fade btnViewBukti" id="viewBukti" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Bukti Pembayaran</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <div class="img-container"
                                        style="background-color: #f8f9fa; min-height: 250px; display: flex;
                                        align-items: center; justify-content: center;">
                                        <img id="viewBuktiImage"
                                        style="max-width: 100%; max-height: 50vh; width: auto;
                                        height: auto; object-fit: contain; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- akhir tampil picture -->
         <!-- modal update payment -->
        <div class="modal fade" id="updatePayment" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Update Payment Rombogan</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="updatePay" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="modal-body">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" id="up_payment_Id" name="paymentId">
                                <input type="hidden" id="old_price" value="<?= $row['price'] ?>">
                                <div class="mb-3 row">
                                    <label for="up_idPay" class="col-sm-4 col-form-label">ID Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_idPay" name="up_idPay" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_instansi" class="col-sm-4 col-form-label">Rombongan</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_instansi" name="up_instansi" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_picPay" class="col-sm-4 col-form-label">PIC</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" id="up_picPay" name="up_picPay" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_jenis" class="col-sm-4 col-form-label">Jenis Pembayaran</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_jenis" name="up_jenis" required>
                                            <option value="">---pilih kategori---</option>
                                            <option value="DP">Down Payment</option>
                                            <option value="Pelunasan">Clear Payment</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_metode" class="col-sm-4 col-form-label">Metode</label>
                                    <div class="col-sm-8">
                                        <select class="form-select" id="up_metode" name="up_metode" required>
                                            <option value="">---pilih metode---</option>
                                            <option value="Cash">Cash</option>
                                            <option value="Transfer">Transfer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_tgl_pay" class="col-sm-4 col-form-label">Tanggal Pembayaran</label>
                                    <div class="col-sm-8">
                                        <input type="date" class="form-control" id="up_tgl_pay" name="up_tgl_pay" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_price" class="col-sm-4 col-form-label">Nominal</label>
                                    <div class="col-sm-8">
                                        <input type="tel" class="form-control" id="up_price" name="up_price"
                                        inputmode="numeric" oninput="this.value=this.value.replace(/[^0-9]/g,'');" required>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="up_imgPay" class="col-sm-4 col-form-label">Upload Bukti</label>
                                    <div class="col-sm-8">
                                        <input type="file" class="form-control" id="up_imgPay" name="up_imgPay" accept="image/jpeg, image/png, image/jpg">
                                        <small class="form-text text-muted">Hanya JPG, JPEG, PNG. Maksimal 2MB.</small>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-8">
                                        <img id="currentUpPayImage" src="" alt="" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px dashed #ddd;
                                        display: flex; align-items: center; justify-content: center;">
                                        <p id="noUpImageMessage" style="color: red; display: none;">Belum ada bukti pembayaran terunggah.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="btnUpdateBayar" class="btn btn-primary">Update</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        <!-- akhir modal update payment -->

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            /* =====================================================
            DATA SOURCE DARI PHP
            ===================================================== */
            const paymentView = <?= json_encode($viewPay); ?>;
            const viewClient  = <?= json_encode($datarombongan); ?>;


            /* =====================================================
            ELEMENT GLOBAL
            ===================================================== */
            const tbody        = document.getElementById("dtPayment");
            const imgDefault   = '../../assets/img/money.jpg';

            // ADD FORM
            const addImg       = document.getElementById('currentPayImage');
            const addFileInput = document.getElementById('imgPay');
            const btnSimpan    = document.getElementById('btnSimpanBayar');

            // UPDATE FORM
            const upImg        = document.getElementById('currentUpPayImage');
            const upFileInput  = document.getElementById('up_imgPay');


            /* =====================================================
            RENDER TABLE PAYMENT
            ===================================================== */
            paymentView.forEach((item, index) => {

                const row = document.createElement("tr");
                const tanggalDb = new Date(item.date_pay);
                const tgl_pay = tanggalDb.toLocaleDateString('id-ID', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });

                row.innerHTML = `
                    <td>${index+1}</td>
                    <td>${item.rombongan_id}</td>
                    <td>${item.rombongan_name}</td>
                    <td>${formatNumber(item.price)}</td>
                    <td>${item.metode}</td>
                    <td>${tgl_pay}</td>
                    <td>${item.jenis}</td>
                    <td>
                        <div class="d-flex gap-1 justify-content-center">

                            <!-- BUTTON UPDATE -->
                            <button class="btn btn-primary btnUpdatePayment"
                                data-bs-toggle="modal"
                                data-bs-target="#updatePayment"
                                data-id="${item.id}"
                                data-idRom="${item.rombongan_id}"
                                data-nameRom="${item.rombongan_name}"
                                data-pic="${item.pic}"
                                data-jenis="${item.jenis}"
                                data-metode="${item.metode}"
                                data-price="${item.price}"
                                data-tglpay="${item.date_pay}"
                                data-img="../../${item.img_payment || ''}">
                                <i class="fa-solid fa-file-pen"></i>
                            </button>

                            <button class="btn btn-info btnViewBukti"
                                data-bs-toggle="modal"
                                data-bs-target="#viewBukti"
                                data-img="../../${item.img_payment || ''}">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                    </td>
                `;

                tbody.appendChild(row);
            });

            /* =====================================================
            CLICK HANDLER GLOBAL (SEMUA BUTTON MODAL)
            ===================================================== */
            document.addEventListener('click', function(e){

                /* ---------- ADD PAYMENT BUTTON ---------- */
                const btnAdd = e.target.closest('[data-bs-target="#addPayment"]');
                if (btnAdd) {
                    // reset preview
                    addImg.src = imgDefault;
                    if(addFileInput) addFileInput.value = "";

                    const id = btnAdd.dataset.id;
                    const data = viewClient.find(x => x.client_id === id);

                    if (data) {
                        idPay.value = data.client_id;
                        instansi.value = data.client_name;
                        picPay.value = data.client_pic;
                    }
                }

                /* ---------- VIEW BUKTI ---------- */
                const btnView = e.target.closest('.btnViewBukti');
                if (btnView) {
                    const img = btnView.dataset.img || '';
                    const target = document.getElementById('viewBuktiImage');

                    // guard null supaya tidak jadi /null
                    target.src = img !== '../../' ? img : '';
                }


                /* ---------- UPDATE PAYMENT ---------- */
                const btnUpdate = e.target.closest('.btnUpdatePayment');
                if (btnUpdate) fillUpdateModal(btnUpdate);
            });


            /* =====================================================
            FILL MODAL UPDATE
            ===================================================== */
            function fillUpdateModal(btn){

                up_payment_Id.value = btn.dataset.id;
                up_idPay.value      = btn.dataset.idrom;
                up_instansi.value   = btn.dataset.namerom;
                up_picPay.value     = btn.dataset.pic;

                up_price.value      = formatNumber(btn.dataset.price);
                old_price.value     = formatNumber(btn.dataset.price);

                // guard substring null
                up_tgl_pay.value    = btn.dataset.tglpay
                    ? btn.dataset.tglpay.substring(0,10)
                    : '';

                up_jenis.value      = btn.dataset.jenis;
                up_metode.value     = btn.dataset.metode;

                // tampilkan gambar lama
                if (btn.dataset.img && btn.dataset.img !== '../../') {
                    upImg.src = btn.dataset.img;
                    upImg.style.display = 'block';
                } else {
                    upImg.src = '';
                    upImg.style.display = 'none';
                }
            }


            /* =====================================================
            IMAGE PREVIEW — ADD FORM
            ===================================================== */
            addFileInput?.addEventListener('change', function(){

                const file = this.files[0];
                if (!file) return resetAddPreview();

                if (!file.type.startsWith('image/')) {
                    alert('File harus gambar');
                    return resetAddPreview();
                }

                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire('Error','Maks 2MB','error');
                    return resetAddPreview();
                }

                const reader = new FileReader();
                reader.onload = e => addImg.src = e.target.result;
                reader.readAsDataURL(file);

                btnSimpan.disabled = false;
            });

            function resetAddPreview(){
                addFileInput.value = '';
                addImg.src = imgDefault;
                btnSimpan.disabled = true;
            }


            /* =====================================================
            IMAGE PREVIEW — UPDATE FORM
            ===================================================== */
            upFileInput?.addEventListener('change', function(){

                const file = this.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = e => upImg.src = e.target.result;
                reader.readAsDataURL(file);
            });


            /* =====================================================
            SUBMIT ADD PAYMENT
            ===================================================== */
            $('#addPay').on('submit', function(e){

                e.preventDefault();

                let fd = new FormData(this);
                fd.append('aksi','tambah_payment');
                fd.set('price', $('#price').val().replace(/\./g,''));

                ajaxSubmit(fd);
            });


            /* =====================================================
            SUBMIT UPDATE PAYMENT
            ===================================================== */
            $('#updatePay').on('submit', function(e){

                e.preventDefault();

                let fd = new FormData(this);
                fd.append('aksi','update_payment');
                fd.set('up_price', $('#up_price').val().replace(/\./g,''));

                ajaxSubmit(fd);
            });


            /* =====================================================
            AJAX HELPER
            ===================================================== */
            function ajaxSubmit(formData){

                $.ajax({
                    url:'../../assets/fungsi.php',
                    method:'POST',
                    data:formData,
                    contentType:false,
                    processData:false,
                    success: res => {

                        let r;
                        try { r = JSON.parse(res); }
                        catch { return Swal.fire('Error','Respon bukan JSON','error'); }

                        if (r.status === 'success')
                            Swal.fire('Sukses','Berhasil','success')
                                .then(()=>location.reload());
                        else if (r.status === 'nochange')
                            Swal.fire('Info','Tidak ada perubahan data','info');
                        else
                            Swal.fire('Gagal', r.message || 'Error','error');
                    }
                });
            }

            /* =====================================================
            FORMAT ANGKA RUPIAH
            ===================================================== */
            function formatNumber(v){
                v = v.toString().replace(/\D/g,'');
                return v.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
            }

            $(document).on('input','#price,#up_price', function(){
                this.value = formatNumber(this.value);
            });
        </script>
    </body>
</html>