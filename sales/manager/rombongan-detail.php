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
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Cabana and Cabin</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form id="approval" method="POST" autocomplete="off">
                                <div class="modal-body">
                                    <div class="card">
                                        <div class="card-body">
                                            <input type="text" class="form-control" value="<?= $client_id ?>" id="cId" name="cId">
                                            <input type="text" class="form-control" value="<?= $client_name ?>" id="cName" name="cName">
                                            <div class="row text-start">
                                                <div class=" border border-dark">
                                                    <div class="row">
                                                        <p class="fw-bold mt-2 mb-1">Detail Rombongan</p>
                                                        <div id="detailInfo"></div>
                                                        <div class="row text-end mt-2">
                                                            <div class="col-7"></div>
                                                            <div class="col-3"><strong>TOTAL PENDAPATAN :</strong></div>
                                                            <div class="col-2"><strong id="total_pendapatan"></strong></div>
                                                        </div>
                                                    </div>
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
                
                row.innerHTML =`
                <td>${index + 1 }</td>
                <td>${item.client_name}</td>
                <td>${item.client_id}</td>
                <td>${item.marketing}</td>
                <td>${plan}</td>
                <td>${item.oleh}</td>
                <td>
                    <div class="d-grid gap-1">
                        <a class="btn btn-primary btnDetail"
                        data-client-id="${item.client_id}"
                        data-client-name="${item.client_name}"
                        data-client-date="${item.date_plan}">
                        <i class="fa-solid fa-newspaper"></i> Detail</a>
                        <a class="btn btn-success btnApproval" data-bs-toggle="modal" data-bs-target="#approval"
                        data-client-id="${item.client_id}"
                        <i class="fa-solid fa-signature"></i></i> ACC</a>
                    </div>
                </td>
                `;
                tbody.appendChild(row);
            });

        </script>
        <script>
            //even delegation
            document.body.addEventListener('click', function(e) {
                // Periksa apakah elemen yang diklik atau elemen terdekatnya memiliki class 'btnDetail'
                const clickedElement = e.target.closest('.btnDetail');

                if (clickedElement) {
                    e.preventDefault();

                    const clientId = clickedElement.dataset.clientId;
                    const clientName = clickedElement.dataset.clientName;
                    const clientDate = clickedElement.dataset.clientDate;

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'rombongan-reques.php';

                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'client_id';
                    inputId.value = clientId;

                    const inputName = document.createElement('input');
                    inputName.type = 'hidden';
                    inputName.name = 'client_name';
                    inputName.value = clientName;

                    const inputDate = document.createElement('input');
                    inputDate.type = 'hidden';
                    inputDate.name = 'date_plan';
                    inputDate.value = clientDate;

                    form.appendChild(inputId);
                    form.appendChild(inputName);
                    form.appendChild(inputDate);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        </script>
        <script>
            document.addEventListener("click", function (e) {
            const btn = e.target.closest(".btnApproval");
            if (!btn) return;

            const clientId = btn.dataset.clientId;

            $.ajax({
                url: "../../assets/fungsi.php",
                type: "POST",
                data: { aksi: "getDetailRombongan", client_id: clientId },
                dataType: "json",
                success: function (res) {
                    if (res.status !== "success") return;

                    const master = res.master;
                    const list = res.budget;

                    // tampilkan master rombongan
                    let html = `
                        <div><strong>Nama Rombongan:</strong> ${master.client_name}</div>
                        <div><strong>Tanggal Kunjungan:</strong> ${master.date_plan}</div>
                        <div><strong>Jumlah Pax:</strong> ${master.jumlah_pax} Orang</div>
                        <div><strong>Marketing:</strong> ${master.marketing}</div>
                        <hr>
                        <p class="fw-bold">Detail Fasilitas</p>
                    `;

                    let totalPendapatan = 0;

                    list.forEach(item => {
                        const harga = Number(item.price || 0);
                        const subtotal = harga * Number(item.qty || 0);
                        totalPendapatan += harga;

                        html += `
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>${item.fasilitas_name} (${item.qty}x)</span>
                                <span>Rp ${harga.toLocaleString()}</span>
                                <span>=</span>
                                <span>Rp ${subtotal.toLocaleString()}</span>
                            </div>
                        `;
                    });

                    document.getElementById("detailInfo").innerHTML = html;
                    document.getElementById("total_pendapatan").innerText = 
                        "Rp " + totalPendapatan.toLocaleString();
                }
            });
        });
        </script>
    </body>
</html>
