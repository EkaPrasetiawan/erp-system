<?php

require '../../assets/fungsi.php';
$allRom = getAllRombongan($konek) ?? [];


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
                        <h1 class="mt-4">Budgeting</h1>
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
                row.innerHTML =`
                <td>${index + 1 }</td>
                <td>${item.client_name}</td>
                <td>${item.client_id}</td>
                <td>${item.marketing_name}</td>
                <td>${item.tgl_kunjungan}</td>
                <td>${item.marketing_name}</td>
                <td>
                    <a class="btn btn-primary btnDetail"
                    data-client-id="${item.client_id}"
                    data-client-name="${item.client_name}">
                    <i class="fa-solid fa-newspaper"></i> Detail</a>
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

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'budgeting-detail.php';

                    const inputId = document.createElement('input');
                    inputId.type = 'hidden';
                    inputId.name = 'client_id';
                    inputId.value = clientId;

                    const inputName = document.createElement('input');
                    inputName.type = 'hidden';
                    inputName.name = 'client_name';
                    inputName.value = clientName;

                    form.appendChild(inputId);
                    form.appendChild(inputName);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        </script>
    </body>
</html>
