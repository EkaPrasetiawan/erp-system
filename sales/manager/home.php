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
        <title>Home</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>"
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
                        <h1 class="mt-4">Home</h1>
                        <!-- <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Home</li>
                        </ol> -->
                        <div class="row">
                            <div class="mb-3">
                                <label for="yearSelect">Pilih Tahun:</label>
                                <select id="yearSelect" class="form-select" style="width:auto; display:inline-block;"></select>
                            </div>

                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Data Rombongan Perbulan
                                    </div>
                                    <div class="card-body"><canvas id="mySalesMount" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Data Rombongan Persales
                                    </div>
                                    <div class="card-body"><canvas id="mySalesName" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Rombongan</th>
                                            <th>Sales</th>
                                            <th>Tnggal Kunjungan</th>
                                            <th>Type</th>
                                            <th>Satatus</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dtRombonganAll">
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
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script> -->
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            const viewBudget = <?= json_encode($allRom); ?>;
            const tbody = document.getElementById('dtRombonganAll');

            viewBudget.forEach((item, index) => {
                const row = document.createElement('tr');
                const tanggalDb = new Date(item.date_plan);
                const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
                const plan = tanggalDb.toLocaleDateString('id-ID', opsi);
                
                row.innerHTML =`
                <td>${index + 1 }</td>
                <td>${item.client_name}</td>
                <td>${item.marketing}</td>
                <td>${plan}</td>
                <td>${item.judul}</td>
                <td>${item.oleh}</td>
                `;
                tbody.appendChild(row);
            });
        </script>
        <script>
            const allRom = <?= json_encode($allRom); ?>;
            const yearSelect = document.getElementById("yearSelect");
            // Ambil semua tahun unik dari date_plan
            const years = [
            ...new Set(allRom.map((item) => new Date(item.date_plan).getFullYear())),
            ].sort();

            // Isi dropdown tahun
            years.forEach((year) => {
            const opt = document.createElement("option");
            opt.value = year;
            opt.textContent = year;
            yearSelect.appendChild(opt);
            });

            // Tentukan tahun default (tahun sekarang atau terakhir di data)
            const currentYear = new Date().getFullYear();
            yearSelect.value = years.includes(currentYear)
            ? currentYear
            : years[years.length - 1];

            // Urutan nama bulan tetap
            const monthOrder = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember",
            ];

            // Variabel global untuk menyimpan chart agar bisa di-destroy nanti
            let chartPerBulan = null;
            let chartPerSales = null;

            // Fungsi render semua grafik berdasarkan tahun
            function renderCharts(year) {
            const filtered = allRom.filter(
                (item) => new Date(item.date_plan).getFullYear() === parseInt(year)
            );

            // =============== Grafik 1: Rombongan Per Bulan ====================
            const monthlyCount = {};
            filtered.forEach((item) => {
                const month = new Date(item.date_plan).toLocaleString("id-ID", {
                month: "long",
                });
                monthlyCount[month] = (monthlyCount[month] || 0) + 1;
            });

            const labelsMonth = monthOrder.filter((m) =>
                Object.keys(monthlyCount).includes(m)
            );
            const valuesMonth = labelsMonth.map((m) => monthlyCount[m] || 0);

            if (chartPerBulan) chartPerBulan.destroy();
            const ctxMonth = document.getElementById("mySalesMount").getContext("2d");
            chartPerBulan = new Chart(ctxMonth, {
                type: "bar",
                data: {
                labels: labelsMonth,
                datasets: [
                    {
                    label: `Jumlah Rombongan per Bulan (${year})`,
                    backgroundColor: "rgba(2,117,216,0.7)",
                    borderColor: "rgba(2,117,216,1)",
                    data: valuesMonth,
                    },
                ],
                },
                options: {
                scales: {
                    y: { beginAtZero: true, min: 0, ticks: { stepSize: 1 } },
                },
                plugins: { legend: { display: true } },
                },
            });

            // =============== Grafik 2: Rombongan Per Sales ====================
            const salesCount = {};
            filtered.forEach((item) => {
                const sales = item.marketing || "Tidak Ada Sales";
                salesCount[sales] = (salesCount[sales] || 0) + 1;
            });

            const labelsSales = Object.keys(salesCount);
            const valuesSales = Object.values(salesCount);
            const colors = labelsSales.map((_, i) => `hsl(${(i * 60) % 360}, 70%, 50%)`);

            if (chartPerSales) chartPerSales.destroy();
            const ctxSales = document.getElementById("mySalesName").getContext("2d");
            chartPerSales = new Chart(ctxSales, {
                type: "bar",
                data: {
                labels: labelsSales,
                datasets: [
                    {
                    label: `Jumlah Rombongan per Sales (${year})`,
                    backgroundColor: colors,
                    // borderColor: colors,
                    data: valuesSales,
                    },
                ],
                },
                options: {
                scales: {
                    y: { beginAtZero: true, min: 0, ticks: { stepSize: 1 } },
                },
                plugins: { legend: { display: false } },
                },
            });
            }

            // Render pertama kali
            renderCharts(yearSelect.value);

            // Ubah grafik jika tahun diganti
            yearSelect.addEventListener("change", (e) => renderCharts(e.target.value));
        </script>
    </body>
</html>