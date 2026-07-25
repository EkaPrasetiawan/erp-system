<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['Employee_ID'], $_SESSION['level']) ||
    !is_numeric($_SESSION['level'])
) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

// Cek level sesuai folder
$currentLevel = $_SESSION['level'];
$currentJabatan = $_SESSION['jabatan'];
$currentFolder = strtolower(basename(dirname(__FILE__))); // ambil nama folder saat ini

$allowedAccess = [
    'manager' => ['grade' => 3, 'jabatan' => 'Manager'],
    'spv'     => ['grade' => 3, 'jabatan' => 'SPV'],
    'staff'   => ['grade' => 3, 'jabatan' => 'Staff']
];

if (!isset($allowedAccess[$currentFolder]) || $allowedAccess[$currentFolder]['grade'] !== $currentLevel
            || $allowedAccess[$currentFolder]['jabatan'] !== $currentJabatan) {
    session_destroy();
    header('Location: ../../index.php');
    exit;
}

require '../../assets/modul3.php';
$allRom = viewRombongan2($konek) ?? [];

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>ERP_System</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>    
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
                        <h1 class="mt-4">Dashboard</h1>
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
                                        Rasio Progres
                                    </div>
                                    <div class="card-body d-flex justify-content-center align-items-center" style="height: 280px;">
                                        <canvas id="myStatusChart"></canvas>
                                    </div>
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
                                            <th>Tanggal Kunjungan</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Validasi</th>
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
            const allRomData = <?= json_encode($allRom, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>;

            // Helper Sanitasi Teks (Pencegahan XSS)
            function escapeHtml(text) {
                if (!text) return '';
                return String(text)
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Mapping angka database ke Teks Label
            const statusLabels = {
                0: 'Open',
                1: 'On Process',
                2: 'Done',
                3: 'Batal'
            };

            // Mapping angka ke Warna Badge Bootstrap (Statis / Read-Only)
            const statusBadges = {
                0: 'bg-success',          // Hijau
                1: 'bg-warning text-dark', // Kuning
                2: 'bg-secondary',        // Abu-abu
                3: 'bg-danger'            // Merah
            };

            const tbody = document.getElementById('dtRombonganAll');
            if (tbody) {
                allRomData.forEach((item, index) => {
                    const row = document.createElement('tr');
                    const tanggalDb = new Date(item.date_plan);
                    const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
                    const plan = !isNaN(tanggalDb.getTime()) 
                        ? tanggalDb.toLocaleDateString('id-ID', opsi) 
                        : '-';

                    // Konversi status angka DB ke Teks & Warna
                    const stAngka    = parseInt(item.status);
                    const labelTeks  = statusLabels[stAngka] || 'Open';
                    const badgeClass = statusBadges[stAngka] || 'bg-secondary';
                    
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${escapeHtml(item.client_name)}</td>
                        <td>${escapeHtml(item.marketing || '-')}</td>
                        <td>${plan}</td>
                        <td>${escapeHtml(item.judul || '-')}</td>
                        <td><span class="badge ${badgeClass}">${labelTeks}</span></td>
                        <td>${escapeHtml(item.oleh || '-')}</td>
                    `;
                    tbody.appendChild(row);
                });
            }

            const yearSelect = document.getElementById("yearSelect");
            // Ambil semua tahun unik dari date_plan
            const validYears = allRomData
                .map((item) => {
                    if (!item.date_plan) return null;
                    const d = new Date(item.date_plan);
                    return !isNaN(d.getTime()) ? d.getFullYear() : null;
                })
                .filter((year) => year !== null);

            const years = [...new Set(validYears)].sort((a, b) => a - b);
            if (years.length === 0) years.push(new Date().getFullYear());

            // Isi Opsi Dropdown Tahun
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
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember",
            ];

            // Variabel global untuk menyimpan chart agar bisa di-destroy nanti
            let chartPerBulan = null;
            let chartStatus   = null;

            // Fungsi render semua grafik berdasarkan tahun
            function renderCharts(selectedYear) {
                const yearInt = parseInt(selectedYear);
                
                // Filter data berdasarkan tahun yang dipilih
                const filtered = allRomData.filter((item) => {
                    if (!item.date_plan) return false;
                    const d = new Date(item.date_plan);
                    return !isNaN(d.getTime()) && d.getFullYear() === yearInt;
                });

                // ==================== Grafik 1: Rombongan Per Bulan ====================
                const monthlyCount = {};
                filtered.forEach((item) => {
                    const d = new Date(item.date_plan);
                    const month = d.toLocaleString("id-ID", { month: "long" });
                    monthlyCount[month] = (monthlyCount[month] || 0) + 1;
                });

                // Selalu petakan 12 bulan penuh agar skala sumbu X konsisten
                const valuesMonth = monthOrder.map((m) => monthlyCount[m] || 0);

                if (chartPerBulan) {
                    chartPerBulan.destroy();
                }

                const ctxMonth = document.getElementById("mySalesMount").getContext("2d");
                chartPerBulan = new Chart(ctxMonth, {
                    type: "bar",
                    data: {
                        labels: monthOrder,
                        datasets: [
                            {
                                label: `Jumlah Rombongan per Bulan (${selectedYear})`,
                                backgroundColor: "rgba(2,117,216,0.7)",
                                borderColor: "rgba(2,117,216,1)",
                                borderWidth: 1,
                                data: valuesMonth,
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                min: 0, 
                                ticks: { stepSize: 1 } 
                            },
                        },
                        plugins: { 
                            legend: { display: true } 
                        },
                    },
                });

                // ==================== Grafik 2: Rasio Progres / Status ====================
                const statusCount = {
                    'Open': 0,
                    'On Process': 0,
                    'Done': 0,
                    'Batal': 0
                };

                filtered.forEach((item) => {
                    const stAngka = parseInt(item.status);
                    const labelTeks = statusLabels[stAngka] || 'Open';
                    statusCount[labelTeks]++;
                });

                const labelsStatus = Object.keys(statusCount);
                const valuesStatus = Object.values(statusCount);

                // Skema Warna Sesuai Permintaan
                const bgColors = [
                    'rgba(40, 167, 69, 0.8)',   // Hijau (Open)
                    'rgba(255, 193, 7, 0.8)',   // Kuning (On Process)
                    'rgba(88, 92, 95, 0.8)',    // Abu-abu/Biru Gelap (Done)
                    'rgba(220, 53, 69, 0.8)'    // Merah (Batal)
                ];

                if (chartStatus) {
                    chartStatus.destroy();
                }

                const ctxStatus = document.getElementById("myStatusChart").getContext("2d");
                chartStatus = new Chart(ctxStatus, {
                    type: "doughnut",
                    data: {
                        labels: labelsStatus,
                        datasets: [{
                            data: valuesStatus,
                            backgroundColor: bgColors,
                            borderColor: [
                                'rgba(40, 167, 69, 1)',
                                'rgba(255, 193, 7, 1)',
                                'rgba(88, 92, 95, 1)',
                                'rgba(220, 53, 69, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'bottom',
                                labels:{
                                    boxWidth: 12,
                                    padding: 10
                                }
                            }
                        }
                    }
                });
            }

            // Render pertama kali
            renderCharts(yearSelect.value);

            // Ubah grafik jika tahun diganti
            yearSelect.addEventListener("change", (e) => renderCharts(e.target.value));
        </script>
    </body>
</html>