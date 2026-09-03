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

require '../../assets/modul2.php';

$rombongan_id = $_GET['rombongan_id'] ?? '';

if ($rombongan_id === '') {
    echo '<script>alert("Rombongan tidak ditemukan."); window.history.back();</script>';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Print Event Order</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <style>
        @page {
            size: A4;
            margin: 15mm;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11pt;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 210mm;
            margin: 0 auto;
            padding: 10mm;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 15px;
            /* border-bottom: 3px double #000; */
            padding-bottom: 15px;
        }
        .header img {
            max-height: 60px;
        }
        .header h1 {
            font-size: 18pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .info-section {
            margin-bottom: 15px;
            border: 1px solid #ccc;
            padding: 12px 15px;
            border-radius: 5px;
        }
        .info-section h3 {
            font-size: 12pt;
            font-weight: bold;
            /* margin-top: 0; */
            /* margin-bottom: 12px; */
            /* color: #000; */
            /* border-bottom: 1px solid #999; */
            /* padding-bottom: 5px; */
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 5px 20px;
            margin-bottom: 5px;
        }
        .info-grid .info-col {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .info-grid .info-row {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }
        .info-grid .label {
            font-weight: bold;
            color: #555;
            min-width: 140px;
            white-space: nowrap;
        }
        .info-grid .value {
            color: #000;
        }
        .eo-section {
            margin-bottom: 10px;
        }
        .eo-section h3 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px;
            /* color: #000; */
            /* border-bottom: 1px solid #999; */
            /* padding-bottom: 5px; */
        }
        .eo-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11pt;
        }
        .eo-table th {
            background-color: #f0f0f0;
            border: 1px solid #999;
            padding: 6px 8px;
            text-align: left;
            font-weight: bold;
        }
        .eo-table td {
            border: 1px solid #999;
            padding: 6px 8px;
            vertical-align: top;
        }
        .eo-table .text-center {
            text-align: center;
        }
        .eo-table .text-end {
            text-align: right;
        }
        .tiket-masuk-table td {
            border: 1px solid #999;
            padding: 4px 8px;
        }
        .tiket-masuk-table .tiket-label {
            width: 25%;
            font-weight: bold;
            text-align: left;
        }
        .tiket-masuk-table .tiket-value {
            width: 25%;
            text-align: left;
        }
        .no-data {
            text-align: center;
            color: #999;
            font-style: italic;
            padding: 10px;
        }
        .notes-section {
            margin-top: 10px;
        }
        .notes-section h3 {
            font-size: 11pt;
            margin-bottom: 8px;
        }
        .catatan {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 50px;
            border-radius: 5px;
            font-size: 11pt;
        }
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 10pt;
            text-align: left;
            /* color: #777; */
            /* border-top: 1px solid #ccc; */
            /* padding-top: 10px; */
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            /* margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ccc; */
        }
        .signature-col {
            text-align: center;
            flex: 1;
        }
        .signature-col p {
            margin: 0 0 5px 0;
            font-size: 11pt;
        }
        .signature-label {
            font-weight: bold;
        }
        .signature-name {
            font-weight: bold;
            display: inline-block;
            padding: 0 20px;
            min-width: 150px;
        }
        .btn-print {
            display: block;
            margin: 15px auto;
            padding: 10px 30px;
            font-size: 11px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-print:hover {
            background-color: #218838;
        }
        @media print {
            .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="../../assets/img/logo1.png" alt="Logo" onerror="this.style.display='none'">
            <h1>Event Order Water Kingdom</h1>
        </div>

        <!-- Informasi Rombongan -->
        <div class="info-section">
            <!-- <h3>Informasi Rombongan</h3> -->
            <div class="info-grid">
                <!-- Kolom 1 -->
                <div class="info-col">
                    <div class="info-row">
                        <span class="label">Tanggal Kegiatan</span>
                        <span class="value" id="info-tanggal">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Nama Group</span>
                        <span class="value" id="info-client">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Telp</span>
                        <span class="value" id="info-phone">-</span>
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="info-col">
                    <div class="info-row">
                        <span class="label">Rombongan ID</span>
                        <span class="value" id="info-rombongan-id">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">PIC Rombongan</span>
                        <span class="value" id="info-pic">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">Handphone</span>
                        <span class="value" id="info-hp-pic">-</span>
                    </div>
                    <div class="info-row">
                        <span class="label">PIC</span>
                        <span class="value" id="info-sales">-</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Order: Tiket Masuk -->
        <div class="eo-section">
            <h3>Informasi Kedatangan</h3>
            <div id="eo-tiket-masuk"><div class="no-data">Memuat data...</div></div>
        </div>

        <!-- Event Order: Food and Beverages -->
        <div class="eo-section">
            <h3>Food and Beverages</h3>
            <div id="eo-fnb"><div class="no-data">Memuat data...</div></div>
        </div>

        <!-- Event Order: Event (Operasional + Lainnya) -->
        <div class="eo-section">
            <h3>Event</h3>
            <div id="eo-event"><div class="no-data">Memuat data...</div></div>
        </div>

        <div id="eo-event-notes" class="notes-section">
            <h3>Catatan</h3>
            <div id="event-notes-content" class="catatan"></div>
        </div>

        <div class="footer">
            <p id="footer-info"></p>
        </div>

        <!-- Tanda Tangan -->
        <div class="signature-section">
            <div class="signature-col">
                <p class="signature-label" style="margin-bottom: 40px;">Dibuat oleh</p>
                <p id="signature-created-by" class="signature-name" style="margin-top: 10px;"></p>
                <p>Sales Oficer</p>
            </div>
            <div class="signature-col">
                <p class="signature-label" style="margin-bottom: 40px;">Diketahui oleh</p>
                <p id="signature-approved-by" class="signature-name" style="margin-top: 10px;"></p>
                <p>Sales Manager</p>
            </div>
        </div>
    </div>

    <button class="btn-print" onclick="window.print()">
        <i class="fa-solid fa-print"></i> Cetak
    </button>

    <script>
        // Auto-print ketika halaman dibuka via iframe (dari frm_detail_eo.php)
        // Print akan dipicu setelah semua data AJAX selesai dimuat
        const isIframe = window.self !== window.top;
        const rombonganId = new URLSearchParams(window.location.search).get('rombongan_id') || '';

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text || '';
            return div.innerHTML;
        }

        function formatNumber(num) {
            const n = parseFloat(num);
            if (isNaN(n)) return '0';
            return n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
        }

        function formatDate(dateStr) {
            if (!dateStr || dateStr === '2000-01-01 00:00:00') return '-';
            const dt = new Date(dateStr);
            if (isNaN(dt.getTime())) return '-';
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            return dt.getDate() + ' ' + months[dt.getMonth()] + ' ' + dt.getFullYear();
        }

        function renderEoTable(containerId, items, showArrivalTime, colHeader, showHeader, showNumber) {
            colHeader = colHeader || 'Kebutuhan Acara';
            showHeader = showHeader !== false;
            showNumber = showNumber !== false;
            const container = document.getElementById(containerId);
            if (!items || items.length === 0) {
                container.innerHTML = '<div class="no-data">Tidak ada data.</div>';
                return;
            }
            let html = '<table class="eo-table">';
            if (showHeader) {
                html += '<thead><tr>';
                if (showNumber) {
                    html += '<th class="text-center">No</th>';
                }
                html += '<th>' + colHeader + '</th>' +
                    '<th class="text-center">Jumlah</th>' +
                    '<th>Satuan</th>';
                if (showArrivalTime) {
                    html += '<th>Jam Kedatangan</th>';
                }
                html += '<th>Keterangan</th>' +
                    '</tr></thead>';
            }
            html += '<tbody>';
            items.forEach((item, i) => {
                html += '<tr>';
                if (showNumber) {
                    html += '<td class="text-center">' + (i + 1) + '</td>';
                }
                html += '<td>' + escapeHtml(item.kebutuhan) + '</td>' +
                    '<td class="text-center">' + formatNumber(item.jumlah) + '</td>' +
                    '<td>' + escapeHtml(item.satuan) + '</td>';
                if (showArrivalTime) {
                    html += '<td>' + escapeHtml(item.arrival_time || '-') + '</td>';
                }
                html += '<td>' + escapeHtml(item.keterangan || '-') + '</td>' +
                    '</tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function renderTiketMasuk(items) {
            const container = document.getElementById('eo-tiket-masuk');
            if (!items || items.length === 0) {
                container.innerHTML = '<div class="no-data">Tidak ada data.</div>';
                return;
            }
            let html = '<table class="eo-table tiket-masuk-table"><tbody>';
            items.forEach(function (item) {
                html += '<tr>' +
                    '<td class="tiket-label">Jumlah Tamu :</td>' +
                    '<td class="tiket-value">' + formatNumber(item.jumlah) + ' ' + escapeHtml(item.satuan) + '</td>' +
                    '<td class="tiket-label">Jam Masuk :</td>' +
                    '<td class="tiket-value">' + escapeHtml(item.arrival_time || '-') + '</td>' +
                    '</tr>';
                if (item.keterangan && item.keterangan.trim() !== '') {
                    html += '<tr>' +
                        '<td class="tiket-label">Keterangan</td>' +
                        '<td class="tiket-value" colspan="3">' + escapeHtml(item.keterangan) + '</td>' +
                        '</tr>';
                }
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function loadPrintData() {
            if (!rombonganId) {
                document.getElementById('info-rombongan-id').textContent = 'Rombongan tidak ditemukan';
                return;
            }

            // Ambil data rombongan
            $.ajax({
                url: '/erp-system/assets/modul2.php',
                method: 'GET',
                data: { aksi: 'get_rombongan_detail', rombongan_id: rombonganId },
                dataType: 'json',
                success: function (rombongan) {
                    if (!rombongan) {
                        document.getElementById('info-rombongan-id').textContent = 'Data tidak ditemukan';
                        return;
                    }
                    document.getElementById('info-tanggal').textContent = formatDate(rombongan.date_plan);
                    document.getElementById('info-client').textContent = rombongan.client_name || '-';
                    document.getElementById('info-phone').textContent = rombongan.phone || '-';
                    document.getElementById('info-rombongan-id').textContent = rombongan.rombongan_id || '-';
                    document.getElementById('info-pic').textContent = rombongan.client_pic || '-';
                    document.getElementById('info-hp-pic').textContent = rombongan.phone || '-';
                    document.getElementById('info-sales').textContent = rombongan.marketing || '-';
                    document.getElementById('info-jumlah-pax').textContent = formatNumber(rombongan.jumlah_pax) + ' Orang';
                    document.getElementById('info-alamat').textContent = rombongan.address || '-';
                },
                error: function () {
                    document.getElementById('info-rombongan-id').textContent = 'Gagal memuat data rombongan';
                }
            });

            // Ambil data event_order
            $.ajax({
                url: '/erp-system/assets/modul2.php',
                method: 'GET',
                data: { aksi: 'get_event_order', rombongan_id: rombonganId },
                dataType: 'json',
                success: function (data) {
                    const grouped = {
                        'tiket masuk': [],
                        'operasional': [],
                        'food and beverages': [],
                        'lainnya': [],
                        'catatan': [],
                    };
                    (data || []).forEach(function (item) {
                        const jenis = (item.jenis || '').toLowerCase().trim();
                        if (grouped[jenis]) {
                            grouped[jenis].push(item);
                        } else {
                            grouped['lainnya'].push(item);
                        }
                    });
                    renderEoTable('eo-tiket-masuk', grouped['tiket masuk'], true, 'Kebutuhan Acara', false, false);
                    renderTiketMasuk(grouped['tiket masuk']);
                    renderEoTable('eo-fnb', grouped['food and beverages'], false, 'Menu');
                    renderEoTable('eo-event', grouped['operasional'].concat(grouped['lainnya']), false);

                    // Render notes below each table
                    function renderNotes(containerId, contentId, items) {
                        const notesContainer = document.getElementById(containerId);
                        const notesContent = document.getElementById(contentId);
                        const allNotes = items
                            .filter(function(item) { return item.notes && item.notes.trim() !== ''; })
                            .map(function(item) {
                                return '<div style="margin-bottom:6px;">' + escapeHtml(item.notes) + '</div>';
                            });
                        if (allNotes.length > 0) {
                            notesContent.innerHTML = allNotes.join('');
                            notesContainer.style.display = 'block';
                        } else {
                            notesContainer.style.display = 'none';
                        }
                    }
                    renderNotes('eo-event-notes', 'event-notes-content', grouped['catatan']);

                    // Setelah semua data dimuat, trigger print jika dibuka via iframe
                    if (isIframe) {
                        setTimeout(function () {
                            window.print();
                        }, 500);
                    }
                },
                error: function () {
                    document.getElementById('eo-tiket-masuk').innerHTML = '<div class="no-data text-danger">Gagal memuat data.</div>';
                }
            });

            // Tampilkan tanggal cetak di footer
            const now = new Date();
            const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            const nowStr = now.getDate() + ' ' + months[now.getMonth()] + ' ' + now.getFullYear();
            document.getElementById('footer-info').textContent = nowStr;

            // Tampilkan nama user yang login di kolom "Dibuat oleh"
            document.getElementById('signature-created-by').textContent = '<?= isset($_SESSION['name']) ? $_SESSION['name'] : '-'; ?>';

            // Ambil data manager dari database
            $.ajax({
                url: '/erp-system/assets/modul2.php',
                method: 'GET',
                data: { aksi: 'get_manager' },
                dataType: 'json',
                success: function (manager) {
                    if (manager && manager.name) {
                        document.getElementById('signature-approved-by').textContent = manager.name;
                    } else {
                        document.getElementById('signature-approved-by').textContent = '-';
                    }
                },
                error: function () {
                    document.getElementById('signature-approved-by').textContent = '-';
                }
            });
        }

        $(document).ready(function () {
            loadPrintData();
        });
    </script>
</body>
</html>