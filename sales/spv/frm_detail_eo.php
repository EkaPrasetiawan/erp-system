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

require '../../assets/modul2.php';

$rombongan_id = '';
$client_name  = '';
$client_date  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rombongan_id = $_POST['rombongan_id'] ?? '';
    $client_name  = $_POST['client_name']  ?? '';
    $client_date  = $_POST['date_plan']    ?? '';
}

// Ambil data rombongan (kode & nama)
$eoView = getEoView($konek, $rombongan_id);
$eoData = $eoView[0] ?? null;

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Detail Event Order</title>
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
                        <div class="row mt-3 mb-3">
                            <a class="nav-link" href="frm-event-order.php">
                                <div class="sb-nav-link-icon text-lg fw-bold"><i class="fa-solid fa-arrow-left"></i>
                                    Form Event Order
                                </div>
                            </a>
                        </div>
                        <h1 class="mt-4">Detail Event Order</h1>

                        <div class="card mb-4 mt-4">
                            <div class="card-body">
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Kode Rombongan</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($eoData['rombongan_id'] ?? $rombongan_id); ?>" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label class="col-sm-2 col-form-label">Nama Rombongan</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" value="<?= htmlspecialchars($eoData['client_name'] ?? $client_name); ?>" readonly>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-success" id="btnTambahEo"
                                        data-rombongan-id="<?= htmlspecialchars($rombongan_id); ?>"
                                        data-client-name="<?= htmlspecialchars($client_name); ?>"
                                        data-client-date="<?= htmlspecialchars($client_date); ?>">
                                        <i class="fa-solid fa-plus"></i> Tambah
                                    </button>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="text-center">
                                            <tr>
                                                <th>No</th>
                                                <th>Kebutuhan Acara</th>
                                                <th>Jenis</th>
                                                <th>Jumlah</th>
                                                <th>Satuan</th>
                                                <th>Keterangan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dtEo">
                                            <tr>
                                                <td colspan="9" class="text-center">Memuat data...</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Tabel Catatan Khusus -->
                                <div class="mt-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="mb-0">Catatan Khusus</h5>
                                    </div>
                                    <div class="mb-3">
                                        <button type="button" class="btn btn-success" id="btnTambahNotes"
                                            data-rombongan-id="<?= htmlspecialchars($rombongan_id); ?>"
                                            data-client-name="<?= htmlspecialchars($client_name); ?>"
                                            data-client-date="<?= htmlspecialchars($client_date); ?>">
                                            <i class="fa-solid fa-plus"></i> Tambah Catatan Khusus
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm" id="tblNotes">
                                            <thead class="text-center table-light">
                                                <tr>
                                                    <th style="width: 5%;">No</th>
                                                    <th style="width: 85%;">Catatan</th>
                                                    <th style="width: 10%;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody id="dtNotes">
                                                <tr>
                                                    <td colspan="4" class="text-center">Belum ada catatan.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="button" class="btn btn-primary" id="btnPrintEo"
                                        data-rombongan-id="<?= htmlspecialchars($rombongan_id); ?>">
                                        <i class="fa-solid fa-print"></i> Print Event Order
                                    </button>
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

        <!-- Modal Tambah Event Order -->
        <div class="modal fade" id="modalEo" tabindex="-1" aria-labelledby="modalEoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formTambahEo" method="POST" autocomplete="off">
                    <input type="hidden" name="rombongan_id" id="eoRombonganId" value="<?= htmlspecialchars($rombongan_id); ?>">
                    <input type="hidden" name="client_name" id="eoClientName" value="<?= htmlspecialchars($client_name); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEoLabel">Tambah Kebutuhan Acara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="kebutuhan" class="form-label">Kebutuhan Acara</label>
                                <input type="text" class="form-control" id="kebutuhan" name="kebutuhan" required>
                            </div>
                            <div class="mb-3">
                                <label for="jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="jenis" name="jenis" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="tiket masuk">Tiket Masuk</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="food and beverages">Food and Beverages</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" step="any" class="form-control" id="jumlah" name="jumlah" value="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="satuan" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="satuan" name="satuan" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="arrival_time" class="form-label">Jam Kedatangan</label>
                                <input type="time" class="form-control" id="arrival_time" name="arrival_time">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Update Event Order -->
        <div class="modal fade" id="modalUpdateEo" tabindex="-1" aria-labelledby="modalUpdateEoLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formUpdateEo" method="POST" autocomplete="off">
                    <input type="hidden" name="id_eo" id="upIdEo">
                    <input type="hidden" name="rombongan_id" id="upRombonganId" value="<?= htmlspecialchars($rombongan_id); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUpdateEoLabel">Update Kebutuhan Acara</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="up_kebutuhan" class="form-label">Kebutuhan Acara</label>
                                <input type="text" class="form-control" id="up_kebutuhan" name="kebutuhan" required>
                            </div>
                            <div class="mb-3">
                                <label for="up_jenis" class="form-label">Jenis</label>
                                <select class="form-select" id="up_jenis" name="jenis" required>
                                    <option value="">-- Pilih Jenis --</option>
                                    <option value="tiket masuk">Tiket Masuk</option>
                                    <option value="operasional">Operasional</option>
                                    <option value="food and beverages">Food and Beverages</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="up_jumlah" class="form-label">Jumlah</label>
                                    <input type="number" step="any" class="form-control" id="up_jumlah" name="jumlah" value="0" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="up_satuan" class="form-label">Satuan</label>
                                    <input type="text" class="form-control" id="up_satuan" name="satuan" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="up_keterangan" class="form-label">Keterangan</label>
                                <textarea class="form-control" id="up_keterangan" name="keterangan" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="up_arrival_time" class="form-label">Jam Kedatangan</label>
                                <input type="time" class="form-control" id="up_arrival_time" name="arrival_time">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal tambah Catatan -->
        <div class="modal fade" id="modalNotes" tabindex="-1" aria-labelledby="modalNotesLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formNotes" method="POST" autocomplete="off">
                    <input type="hidden" name="rombongan_id" id="eoRombonganId" value="<?= htmlspecialchars($rombongan_id); ?>">
                    <input type="hidden" name="client_name" id="eoClientName" value="<?= htmlspecialchars($client_name); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalNotesLabel">Catatan Khusus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="up_keterangan" class="form-label">Catatan</label>
                                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal update Catatan -->
        <div class="modal fade" id="modalUpdateNotes" tabindex="-1" aria-labelledby="modalEditNotesLabel" aria-hidden="true">
            <div class="modal-dialog">
                <form id="formUpdateNotes" method="POST" autocomplete="off">
                    <input type="hidden" name="id_notes" id="id_notes">
                    <input type="hidden" name="up_rombonganID" id="up_rombonganID" value="<?= htmlspecialchars($rombongan_id); ?>">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalEditNotesLabel">Catatan Khusus</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="up_notes" class="form-label">Catatan</label>
                                <textarea class="form-control" id="up_notes" name="up_notes" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>
        <script>
            // Buka modal tambah
            document.getElementById('btnTambahEo').addEventListener('click', function () {
                const modal = new bootstrap.Modal(document.getElementById('modalEo'));
                modal.show();
            });
            document.getElementById('btnTambahNotes').addEventListener('click', function () {
                const modal = new bootstrap.Modal(document.getElementById('modalNotes'));
                modal.show();
            });

            // Toggle Jam Kedatangan berdasarkan Jenis (modal tambah)
            function toggleArrivalTime(selectId, inputId) {
                const select = document.getElementById(selectId);
                const inputGroup = document.getElementById(inputId).closest('.mb-3');
                function updateVisibility() {
                    if (select.value === 'tiket masuk') {
                        inputGroup.style.display = 'block';
                    } else {
                        inputGroup.style.display = 'none';
                    }
                }
                select.addEventListener('change', updateVisibility);
                updateVisibility();
            }
            toggleArrivalTime('jenis', 'arrival_time');
            toggleArrivalTime('up_jenis', 'up_arrival_time');

            // Buka halaman print
            document.getElementById('btnPrintEo').addEventListener('click', function () {
                const rombonganId = $(this).attr('data-rombongan-id');
                if (!rombonganId) {
                    Swal.fire('Error!', 'Rombongan tidak ditemukan.', 'error');
                    return;
                }
                // Buka print_eo.php di iframe hidden, lalu trigger print
                const printUrl = 'print_eo.php?rombongan_id=' + encodeURIComponent(rombonganId);
                const iframe = document.createElement('iframe');
                iframe.style.display = 'none';
                iframe.src = printUrl;
                document.body.appendChild(iframe);
                iframe.onload = function () {
                    setTimeout(function () {
                        try {
                            iframe.contentWindow.print();
                        } catch (e) {
                            Swal.fire('Error!', 'Gagal membuka jendela print.', 'error');
                        }
                        document.body.removeChild(iframe);
                    }, 500);
                };
            });

            // Render tabel kebutuhan acara via AJAX (pattern rombongan-reques)
            $(document).ready(function () {
                const rombonganId = '<?= htmlspecialchars($rombongan_id); ?>';
                const tbody = document.getElementById('dtEo');

                function loadEo() {
                    if (!rombonganId) {
                        tbody.innerHTML = '<tr><td colspan="9" class="text-center">Data rombongan tidak ditemukan.</td></tr>';
                        return;
                    }
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center">Memuat data...</td></tr>';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'GET',
                        data: { aksi: 'get_event_order', rombongan_id: rombonganId },
                        dataType: 'json',
                        success: function (data) {
                            tbody.innerHTML = '';
                            // Filter out items with jenis = 'catatan' (shown separately in Catatan Khusus table)
                            const filtered = (data || []).filter(item => (item.jenis || '').toLowerCase().trim() !== 'catatan');
                            if (!filtered.length) {
                                tbody.innerHTML = '<tr><td colspan="9" class="text-center">Belum ada kebutuhan acara.</td></tr>';
                                return;
                            }
                            filtered.forEach((item, index) => {
                                const row = document.createElement('tr');
                                const showArrival = (item.jenis || '').toLowerCase().trim() === 'tiket masuk';
                                row.innerHTML = `
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.kebutuhan}</td>
                                    <td>${item.jenis || '-'}</td>
                                    <td class="text-end">${parseFloat(item.jumlah).toLocaleString()}</td>
                                    <td>${item.satuan}</td>
                                    <td>${item.keterangan || '-'}</td>
                                    <td class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                                        <button class="btn btn-warning btn-sm btnEditEo me-1" data-id="${item.id_eo}" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm btnHapusEo" data-id="${item.id_eo}" data-name="${item.kebutuhan}">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                `;
                                tbody.appendChild(row);
                            });
                        },
                        error: function () {
                            tbody.innerHTML = '<tr><td colspan="9" class="text-center text-danger">Gagal memuat data.</td></tr>';
                        }
                    });
                }

                // Simpan tambah kebutuhan acara
                $('#formTambahEo').on('submit', function (e) {
                    e.preventDefault();
                    const formData = $(this).serialize() + '&aksi=tambah_event_order';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'POST',
                        data: formData,
                        success: function (res) {
                            let response = {};
                            try { response = JSON.parse(res); } catch (e) { response = { status: 'error' }; }
                            if (response.status === 'success') {
                                $('#modalEo').modal('hide');
                                $('#formTambahEo')[0].reset();
                                Swal.fire('Berhasil!', 'Data tersimpan.', 'success').then(() => loadEo());
                            } else if (response.status === 'exists') {
                                Swal.fire('Opps!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                // Buka modal update dan isi data
                $(document).on('click', '.btnEditEo', function () {
                    const idEo = $(this).attr('data-id');
                    if (!idEo) return;

                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'GET',
                        data: { aksi: 'get_event_order', rombongan_id: rombonganId },
                        dataType: 'json',
                        success: function (data) {
                            const item = data.find(r => String(r.id_eo) === String(idEo));
                            if (!item) {
                                Swal.fire('Error!', 'Data tidak ditemukan.', 'error');
                                return;
                            }
                            $('#upIdEo').val(item.id_eo);
                            $('#up_kebutuhan').val(item.kebutuhan);
                            $('#up_jenis').val(item.jenis || '');
                            $('#up_jumlah').val(item.jumlah);
                            $('#up_satuan').val(item.satuan);
                            $('#up_keterangan').val(item.keterangan || '');
                            $('#up_arrival_time').val(item.arrival_time || '');
                            const upSelect = document.getElementById('up_jenis');
                            const upInputGroup = document.getElementById('up_arrival_time').closest('.mb-3');
                            if (upSelect.value === 'tiket masuk') {
                                upInputGroup.style.display = 'block';
                            } else {
                                upInputGroup.style.display = 'none';
                            }
                            const modalUpdate = new bootstrap.Modal(document.getElementById('modalUpdateEo'));
                            modalUpdate.show();
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                // Simpan update kebutuhan acara
                $('#formUpdateEo').on('submit', function (e) {
                    e.preventDefault();
                    const formData = $(this).serialize() + '&aksi=update_event_order';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'POST',
                        data: formData,
                        success: function (res) {
                            let response = {};
                            try { response = JSON.parse(res); } catch (e) { response = { status: 'error' }; }
                            if (response.status === 'success') {
                                $('#modalUpdateEo').modal('hide');
                                Swal.fire('Berhasil!', 'Data berhasil diperbarui.', 'success').then(() => loadEo());
                            } else if (response.status === 'nochange') {
                                Swal.fire('Info', response.message || 'Tidak ada perubahan data.', 'info');
                            } else if (response.status === 'exists') {
                                Swal.fire('Opps!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan sistem.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                // ===== NOTES FUNCTIONALITY =====
                // Load notes table (only items with jenis = 'catatan')
                function loadNotes() {
                    if (!rombonganId) return;
                    const notesTbody = document.getElementById('dtNotes');
                    notesTbody.innerHTML = '<tr><td colspan="3" class="text-center">Memuat data...</td></tr>';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'GET',
                        data: { aksi: 'get_event_order', rombongan_id: rombonganId },
                        dataType: 'json',
                        success: function (data) {
                            notesTbody.innerHTML = '';
                            // Filter items with jenis = 'catatan'
                            const notesItems = (data || []).filter(item => (item.jenis || '').toLowerCase().trim() === 'catatan');
                            if (!notesItems.length) {
                                notesTbody.innerHTML = '<tr><td colspan="3" class="text-center">Belum ada catatan.</td></tr>';
                                return;
                            }
                            notesItems.forEach((item, index) => {
                                const row = document.createElement('tr');
                                row.innerHTML = `
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.notes}</td>
                                    <td class="d-flex flex-column flex-sm-row gap-1 justify-content-center">
                                        <button class="btn btn-warning btn-sm btnEditNotes me-1" data-id="${item.id_eo}" title="Edit">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        <button class="btn btn-danger btn-sm btnHapusNotes" data-id="${item.id_eo}">
                                            <i class="fa-solid fa-trash"></i> Hapus
                                        </button>
                                    </td>
                                `;
                                notesTbody.appendChild(row);
                            });
                        },
                        error: function () {
                            notesTbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>';
                        }
                    });
                }

                // Save notes (INSERT new record with jenis = 'catatan')
                $('#formNotes').on('submit', function (e) {
                    e.preventDefault();
                    const formData = $(this).serialize() + '&aksi=tambah_notes';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'POST',
                        data: formData,
                        success: function (res) {
                            let response = {};
                            try { response = JSON.parse(res); } catch (e) { response = { status: 'error' }; }
                            if (response.status === 'success') {
                                $('#modalNotes').modal('hide');
                                Swal.fire('Berhasil!', 'Catatan berhasil ditambahkan.', 'success').then(() => {
                                    loadEo();
                                    loadNotes();
                                });
                            } else if (response.status === 'exists') {
                                Swal.fire('Opps!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan sistem.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                // Buka modal update notes
                $(document).on('click', '.btnEditNotes', function () {
                    const idEo = $(this).attr('data-id');
                    if (!idEo) return;

                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'GET',
                        data: { aksi: 'get_event_order', rombongan_id: rombonganId },
                        dataType: 'json',
                        success: function (data) {
                            const item = data.find(r => String(r.id_eo) === String(idEo));
                            if (!item) {
                                Swal.fire('Error!', 'Data tidak ditemukan.', 'error');
                                return;
                            }
                            $('#id_notes').val(item.id_eo);
                            $('#up_notes').val(item.notes || '');
                            
                            const modalUpdate = new bootstrap.Modal(document.getElementById('modalUpdateNotes'));
                            modalUpdate.show();
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                // Save notes (INSERT new record with jenis = 'catatan')
                $('#formUpdateNotes').on('submit', function (e) {
                    e.preventDefault();
                    const formData = $(this).serialize() + '&aksi=update_notes';
                    $.ajax({
                        url: '../../assets/modul2.php',
                        method: 'POST',
                        data: formData,
                        success: function (res) {
                            let response = {};
                            try { response = JSON.parse(res); } catch (e) { response = { status: 'error' }; }
                            if (response.status === 'success') {
                                $('#modalUpdateNotes').modal('hide');
                                Swal.fire('Berhasil!', 'Data berhasil diperbarui.', 'success').then(() => {
                                    loadEo();
                                    loadNotes();
                                });
                            } else if (response.status === 'nochange') {
                                Swal.fire('Info', response.message || 'Tidak ada perubahan data.', 'info');
                            } else if (response.status === 'exists') {
                                Swal.fire('Opps!', response.message, 'warning');
                            } else {
                                Swal.fire('Error!', response.message || 'Terjadi kesalahan sistem.', 'error');
                            }
                        },
                        error: function () {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                        }
                    });
                });

                function hapusData(id, namaItem, tabel, kolom, callback) {
                    if (!id) return;
                    const pesan = namaItem ? `"${namaItem}" akan dihapus.` : 'Data ini akan dihapus.';
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: pesan,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '../../assets/modul2.php',
                                method: 'POST',
                                data: {
                                    aksi: 'hapus_data_generik',
                                    id: id,
                                    tabel: tabel,
                                    kolom: kolom,
                                    nama_item: namaItem || ''
                                },
                                success: function (res) {
                                    if (res.trim() === 'success') {
                                        Swal.fire('Terhapus!', 'Data berhasil dihapus.', 'success')
                                            .then(() => {
                                                if (typeof callback === 'function') callback();
                                            });
                                    } else {
                                        Swal.fire('Gagal!', 'Terjadi kesalahan: ' + res, 'error');
                                    }
                                },
                                error: function (xhr, status, error) {
                                    Swal.fire('Error!', 'Gagal menghapus data. Periksa koneksi.', 'error');
                                    console.error('AJAX error:', status, error);
                                }
                            });
                        }
                    });
                }

                // 1. Hapus Kebutuhan Acara (reload tabel EO)
                $(document).on('click', '.btnHapusEo', function () {
                    const id = $(this).data('id');
                    const nama = $(this).data('name');
                    hapusData(id, nama, 'event_order', 'id_eo', function() {
                        loadEo(); // reload tabel kebutuhan
                    });
                });

                // 2. Hapus Catatan Khusus (reload EO + Notes)
                $(document).on('click', '.btnHapusNotes', function () {
                    const id = $(this).data('id');
                    hapusData(id, 'Catatan', 'event_order', 'id_eo', function() {
                        loadEo();
                        loadNotes();
                    });
                });

                // Initial load
                loadEo();
                loadNotes();
            });
        </script>
    </body>
</html>
