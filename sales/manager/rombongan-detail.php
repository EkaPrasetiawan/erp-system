<?php

require '../../assets/fungsi.php';


$allRom = viewRombongan($konek) ?? [];


?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Rombongan detail</title>
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
                        <h1 class="mt-4">Rombongan Details</h1>
                        <!-- <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Home</li>
                        </ol> -->
                        <div class="card mb-4 mt-4">
                            <!-- <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div> -->
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Rombongan</th>
                                            <th>Kode Registrasi</th>
                                            <th>Sales</th>
                                            <th>Tnggal Kunjungan</th>
                                            <th>Satatus</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtBudget">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- modal Tambah cabin and cabana -->
                    <div class="modal fade" id="approval" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                            <div class="modal-header mb-3">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Confirmation Form</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="approvalForm" method="POST" autocomplete="off">
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row text-start">
                                            <input type="hidden" class="form-control" id="cId" name="cId">
                                            <input type="hidden" class="form-control" id="idAppv" name="idAppv">
                                            <div class="row">
                                                <p class="fw-bold mt-2 mb-1">Informasi Rombongan</p>
                                                <div id="detailInfo"></div>
                                            </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Acc</button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <!-- akhir modal tambah cabin and cabana -->
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <?php require '../../assets/footer.php' ?>
                </footer>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>

        <script>
            const viewBudget = <?= json_encode($allRom); ?>;
            const tbody = document.getElementById('dtBudget');

            viewBudget.forEach((item, index) => {
                const row = document.createElement('tr');
                const tanggalDb = new Date(item.date_plan);
                const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
                const plan = tanggalDb.toLocaleDateString('id-ID', opsi);

                const isDisabled = item.oleh === "UnApproved" ? 'disabled' : '';
                const btnClass = item.oleh === "unApproved" ? 'btn-secondary' : 'btn-primary';
                
                row.innerHTML =`
                <td>${index + 1 }</td>
                <td>${item.client_name}</td>
                <td>${item.rombongan_id}</td>
                <td>${item.marketing}</td>
                <td>${plan}</td>
                <td>${item.oleh}</td>
                <td>
                    <div class="d-grid gap-1">
                        <a class="btn btn-info btnBs"
                        data-rombongan-id="${item.rombongan_id}"
                        data-client-name="${item.client_name}"
                        data-client-date="${item.date_plan}">
                        <i class="fa-solid fa-newspaper"></i> Budget Submission</a>

                        <a class="btn ${btnClass} btnFb ${isDisabled}"
                        data-rombongan-id="${item.rombongan_id}"
                        data-client-name="${item.client_name}"
                        data-client-date="${item.date_plan}">
                        <i class="fa-solid fa-newspaper"></i> Final Budget</a>

                        <a class="btn btn-success btnApproval" data-bs-toggle="modal" data-bs-target="#approval"
                        data-rombongan-id="${item.rombongan_id}"
                        data-client-appv="${item.data_id}">
                        <i class="fa-solid fa-signature"></i> ACC</a>
                    </div>
                </td>
                `;
                tbody.appendChild(row);
            });
            document.addEventListener('click', function(e){
                const btn = e.target.closest('.btnFb');

                if(btn && btn.classList.contains('disabled')){
                    e.preventDefault();
                    e.stopPropagation();
                    alert("Data belum di-approve!");
                    return false;
                }
            });

        </script>
        <script>
            // Kirim data via POST menggunakan form dinamis

            document.body.addEventListener('click', function(e){

                const btn = e.target.closest('.btnBs, .btnFb');
                if (!btn) return;
                e.preventDefault();

                /* ---------- tentukan target action ---------- */
                const actionMap = {
                    btnBs: 'rombongan-reques.php',
                    btnFb: 'rombongan-requesP-fin.php'
                };

                const targetClass = btn.classList.contains('btnBs') ? 'btnBs' : 'btnFb';
                const actionUrl   = actionMap[targetClass];

                /* ---------- ambil dataset ---------- */
                const data = {
                    rombongan_id:   btn.dataset.rombonganId,
                    client_name: btn.dataset.clientName,
                    date_plan:   btn.dataset.clientDate
                };

                /* ---------- validasi ---------- */
                if (!data.rombongan_id) {
                    console.warn('rombongan_id kosong — submit dibatalkan');
                    return;
                }

                /* ---------- submit ---------- */
                postRedirect(actionUrl, data);

            });


            /* =====================================================
            HELPER: POST REDIRECT
            ===================================================== */
            function postRedirect(action, data){

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = action;

                Object.entries(data).forEach(([name,value]) => {

                    const input = document.createElement('input');
                    input.type  = 'hidden';
                    input.name  = name;
                    input.value = value ?? '';

                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            }
        </script>
        <script>
            document.addEventListener("click", function (e) {
                const btn = e.target.closest(".btnApproval");
                if (!btn) return;

                const rombonganId = btn.dataset.rombonganId;
                const dtaId = btn.dataset.clientAppv;

                $.ajax({
                    url: "../../assets/fungsi.php",
                    type: "POST",
                    data: { aksi: "getDetailRombongan", rombongan_id: rombonganId, data_id: dtaId },
                    dataType: "json",
                    success: function (res) {
                        if (res.status !== "success") return;

                        const master = res.master;
                        const list = res.budget;
                        const tanggalDb = new Date(master.date_plan);
                        const tgl_ren = tanggalDb.toLocaleDateString('id-ID', {
                            year: 'numeric', month: 'long', day: 'numeric'
                        });
                        const idRom = document.getElementById("cId");
                        const idAppv = document.getElementById("idAppv");
                        idRom.value = rombonganId;
                        idAppv.value = master.data_id;

                        // tampilkan master rombongan
                        let html = `
                            <div>Nama Rombongan: ${master.client_name}</div>
                            <div>Tanggal Kunjungan: ${tgl_ren}</div>
                            <div>Jumlah Pax: ${formatNumber(list)} Orang</div>
                            <div class=" mb-3">Marketing: ${master.marketing}</div>
                        `;

                        const status = (master.oleh ||"").toString().trim().toLowerCase();
                        const isChecked = status === "approved" ? "checked" : "";
                        console.log("Status:", status, "Is Checked:", isChecked);

                        html += `
                        <div class="form-check">
                            <input type="hidden" name="acc" value="0">
                            <input class="form-check-input" type="checkbox"  id="acc" name="acc" value="1" ${isChecked}>
                            <label class="form-check-label" for="Acc">
                                Aproval Rombongan
                            </label>
                        </div>
                        `;
                        document.getElementById("detailInfo").innerHTML = html;
                    }
                });
            });

            $('#approvalForm').on('submit', function(e){
                e.preventDefault();
                const formData = $(this).serialize()+'&aksi=approveRombongan';
                $.ajax({
                    url: "../../assets/fungsi.php",
                    type: "POST",
                    data: formData,
                    dataType: "json",
                    success: function (res) {
                        if (res.status === "success") {
                            Swal.fire('Success', res.message, 'success').then(() => {
                                location.reload();
                            });
                        } else if (res.status === "nochange") {
                            Swal.fire('No Change', res.message, 'info');
                        } else {
                            Swal.fire('Error', res.message, 'error');
                        }
                    }
                });
            });

            function formatNumber(v){
                v = v.toString().replace(/\D/g,'');
                return v.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
            }
        </script>
    </body>
</html>
