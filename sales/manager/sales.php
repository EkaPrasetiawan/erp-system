<?php

require '../../assets/fungsi.php';

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
        <!-- <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script> -->
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <?php require '../../assets/head-nav.php'; ?>
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
                        <h1 class="mt-4">Sales</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Sales Water Kingdom</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                Data All Sales Water Kingdom
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>Nama</th>
                                            <th>Bagian</th>
                                            <th>Status</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtSales">
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- modal -->
                        <div class="modal fade" id="editSales" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="formValidasi" method="POST">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Data Sales <span id="modalMarketingName"></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card">
                                                <div class="card-body">
                                                    <input type="hidden" id="sales_id" name="sales_id">
                                                    <div class="mb-3 row">
                                                        <label for="name" class="col-sm-4 col-form-label">Nama</label>
                                                        <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="name" name="name">
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="departemen" class="col-sm-4 col-form-label">Bagian</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select" id="departemen" name="departemen">
                                                                <option selected>---pilih bagian---</option>
                                                                <option value="Sekolah">Sekolah</option>
                                                                <option value="Perusahaan">Perusahaan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="status" class="col-sm-4 col-form-label">status</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select" id="status" name="status">
                                                                <option value="1">Aktiv</option>
                                                                <option value="0">Tidak Aktif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="tgl_masuk" class="col-sm-4 col-form-label">Tanggal Masuk</label>
                                                        <div class="col-sm-8">
                                                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                        <button type="submit" name="validasi" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <?php require '../../assets/footer.php' ?>
                </footer>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const tbody = document.getElementById("dtSales");
                const viewSales = <?= json_encode($sales); ?>;
                viewSales.forEach((item, index) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${index+1}</td>
                        <td>${item.name}</td>
                        <td>${item.departemen}</td>
                        <td>${item.checked}</td>
                        <td>${item.tgl_mulai}</td>
                        <td>
                            <button class="btn btn-warning btn-edit" 
                                    data-id="${item.Employee_ID}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editSales">
                                <i class="fa-solid fa-file-pen"></i> Edit
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });

                // Tambahkan event listener ke tombol edit
                tbody.addEventListener("click", (e) => {
                    if (e.target.closest(".btn-edit")) {
                        const button = e.target.closest(".btn-edit");
                        const salesId = button.dataset.id;

                        // Cari data berdasarkan ID
                        const data = viewSales.find(item => item.Employee_ID === salesId);

                        if (data) {
                            document.getElementById("sales_id").value = data.Employee_ID;
                            document.getElementById("name").value = data.name;
                            document.getElementById("departemen").value = data.departemen;
                            document.getElementById("status").value = data.checked;
                            document.getElementById("tgl_masuk").value = data.tgl_mulai;
                        }
                    }
                });
            });
        </script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
    </body>
</html>
