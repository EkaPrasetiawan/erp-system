<?php
require '../../assets/fungsi.php'; 

// Ambil rombongan_id dan client_name dari POST
$rombongan_id = $_POST['rombongan_id'] ?? '';
$client_name = $_POST['client_name'] ?? '';

// Panggil fungsi-fungsi Anda. 
$viewBudgeting = getViewBudgetingFK2 ($konek, $rombongan_id);
$rombonganOk = getRombonganOk ($konek, $rombongan_id);
$viewPay = viewPayment ($konek, $rombongan_id);
 
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Group Package Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <style>

        /* =========================
        PRINT — A4 FIX
        ========================= */
        @media print {

            @page {
                margin: 0;
                size: A4 portrait;
            }

            body {
                font-size: 8pt;
                margin: 0;
                padding: 0.5cm;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
                transform: scale(1) !important;
                box-shadow: none !important;
            }

            /* cegah section terpotong */
            .row,
            .border {
                page-break-inside: avoid;
            }

            label.col-form-label {
                font-weight: 600;
            }

            /* sembunyikan tombol dll */
            .no-print {
                display: none !important;
            }
        }


        /* =========================
        HP — SCALE PREVIEW A4
        (tampilan saja, bukan ukuran print)
        ========================= */

        @media (max-width: 768px) {

            body {
                background: #eee;
            }

            .container {

                /* ukuran A4 px */
                width: 794px !important;

                /* skala tampilan di HP */
                transform: scale(0.58);

                transform-origin: top center;

                margin: 0 auto;
                background: white;

                box-shadow: 0 0 10px rgba(0,0,0,0.25);
            }

        }

    </style>
  </head>
  <body>
    <div class="container text-center border border-dark py-3">
        <div class="row align-items-center">
            <div class="col-3 text-start">
                <img src="../../assets/img/logo1.png" alt="Logo" style="height:80px;">
            </div>
            <div class="col-6 text-center">
                <h3 class="m-0">FORMULIR KESEPAKATAN</h3>
            </div>
            <div class="col-3"></div>
        </div>
        <div class="row text-start">
            <div class="col-12">
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Nama Perusahaan / Sekolah</div>
                <div class="col-8">: <span id="nameInstansi"></span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Rencana Kedatangan</div>
                <div class="col-8">: <span id="tgl_plan"></span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Alamat</div>
                <div class="col-8">: <span id="alamat"></span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Phone</div>
                <div class="col-8">: <span id="phone">-</span></div>
              </div>
              <p class="fw-bold mb-1 mt-2">PENANGGUNG JAWAB</p>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Nama</div>
                <div class="col-8">: <span id="namaPic"></span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Posisi</div>
                <div class="col-2">: <span>-</span></div>
                <div class="col fw-semibold text-end">HP</div>
                <div class="col-4">: <span id="hp"></span></div>
              </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="fw-bold mb-1 mt-1 text-start bg-light">PEMESANAN TIKE/PAKET</p>
                <div class="row text-start">
                    <div class="col">
                        <div class="tabel" id="tampil_tiket"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="fw-bold mb-1 mt-1 text-start bg-light">PEMESANAN TAMBAHAN</p>
                <div class="row text-start">
                    <div class="col">
                        <div class="tabel" id="tampilTambahan"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="fw-bold mb-1 text-start bg-light">CARA PEMBAYARAN</p>
                <div class="row text-start">
                    <div class="col">
                        <div class="tabel" id="tampilPayment"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <p class="fw-bold mb-1 text-start bg-light">CATATAN</p>
                <div class="p-3 rounded text-start">
                    <ol class="mb-0 ps-3">
                        <li class="mb-1">
                            Angka tersebut untuk minimal pemesanan, 
                            <span class="fw-bold">dan tidak dapat berkurang.</span>
                        </li>
                        <li class="mb-1">
                            Jika ada penambahan kebutuhan di luar jumlah di atas, 
                            dapat dikonfirmasi hingga H-3 (khusus FnB).
                        </li>
                        <li class="mb-0">
                            Penambahan tiket di hari H (di bawah 10 tiket) akan dikenakan 
                            harga normal dan diarahkan ke loket Ticket Box.
                        </li>
                    </ol>
                </div>
            </div>
        </div>
        <p class="text-start">Bogor, <?= date('d F Y') ?></p>
        <div id="tandaTangan"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        const viewRombonganOk = <?= json_encode($rombonganOk); ?>;
        const viewBudgeting = <?= json_encode($viewBudgeting); ?>;
        const paymentView = <?= json_encode($viewPay); ?>;
        const data = viewRombonganOk.find(item => item.rombongan_id == '<?= $rombongan_id; ?>');

        // format tanggal
        const tanggalDb = new Date(data.date_plan);
        const opsi = { year: 'numeric', month: 'long', day: 'numeric' };
        const tgl = tanggalDb.toLocaleDateString('id-ID', opsi);

        // isi ke HTML
        document.getElementById('nameInstansi').textContent = data.client_name;
        document.getElementById('tgl_plan').textContent = tgl;
        document.getElementById('alamat').textContent = data.address;
        document.getElementById('namaPic').textContent = data.client_pic;
        document.getElementById('hp').textContent = data.phone;

        function renderTable(data, targetId, isLastTable = false, grandTotalAll = 0) {
            let html = `
                <table class="table table-bordered border-dark table-sm">
                    <thead class="table-light">
                        <tr class="text-center">
                            <th>No</th>
                            <th>Item</th>
                            <th>Unit</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            let subTotal = 0;
            data.forEach((item, index) => {
                const total = item.qty * item.price;
                subTotal += total;
                html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td>${item.fasilitas_name}</td>
                        <td class="text-center">${item.unit}</td>
                        <td class="text-center">${item.qty}</td>
                        <td class="text-end">Rp ${item.price.toLocaleString('id-ID')}</td>
                        <td class="text-end">Rp ${total.toLocaleString('id-ID')}</td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-center">SUB TOTAL</th>
                            <th class="text-end">Rp ${subTotal.toLocaleString('id-ID')}</th>
                        </tr>
            `;

            if (isLastTable) {
                html += `
                    <tr>
                        <th colspan="5" class="text-center fw-bold">TOTAL</th>
                        <th class="text-end fw-bold">Rp ${grandTotalAll.toLocaleString('id-ID')}</th>
                    </tr>
                `;
            }

            html += `
                    </tfoot>
                </table>
            `;

            document.getElementById(targetId).innerHTML = html;
            return subTotal;
        }

        const tiket = viewBudgeting.filter(item => 
            item.group_fasilitas.toLowerCase() === 'tiket masuk'
        );

        const tambahan = viewBudgeting.filter(item => 
            item.group_fasilitas.toLowerCase() !== 'tiket masuk'
        );
        const totalTiket = renderTable(tiket, 'tampil_tiket');
        const totalTambahan = renderTable(tambahan, 'tampilTambahan');
        const grandTotalAll = totalTiket + totalTambahan;

        renderTable(tambahan, 'tampilTambahan', true, grandTotalAll);

        const totalPembyaran = grandTotalAll;

        function formatTanggal(tgl){
            if(!tgl) return '-';

            const date = new Date(tgl);

            return date.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            });
        }

        let htmlPayment = '';
        let totalDP = 0;
        let dpCount = 0;
        let isFullPayment = false;
        paymentView.forEach((item, index) => {
            const tanggalPay = formatTanggal(item.date_pay);
            if (item.jenis?.toUpperCase() === 'DP'){
                dpCount++;
                const dpAmount = parseInt(item.price) || 0;
                totalDP += dpAmount;
                htmlPayment += `
                <div class="row mb-1">
                    <div class="col-5">
                        DP ke ${dpCount} dari total Pembayaran
                    </div>
                    <div class="col-5 text-end">
                        Rp ${dpAmount.toLocaleString('id-ID')}
                    </div>
                    <div class="col-2 text-end">
                        Tgl ${tanggalPay}
                    </div>
                </div>
                `;
            }
            if (item.jenis === 'Pelunasan') {
                htmlPayment += `
                <div class="row mb-1">
                    <div class="col-5">
                        Pelunasan
                    </div>
                    <div class="col-5 text-end">
                        Rp ${dpAmount.toLocaleString('id-ID')}
                    </div>
                    <div class="col-2 text-end">
                        Tgl ${tanggalPay}
                    </div>
                </div>
                `;
            }
        });

        // HITUNG SISA (JIKA TIDAK FULL PAYMENT)
        if(!isFullPayment){
            const sisa = totalPembyaran - totalDP;
            htmlPayment += `
                <div class="row mb-1">
                    <div class="col-5">
                        Sisa Pembayaran
                    </div>
                    <div class="col-5 text-end">
                        Rp ${sisa.toLocaleString('id-ID')}
                    </div>
                    <div class="col-2 text-end">
                        Tgl ${formatTanggal(paymentView[paymentView.length - 1]?.date_pay)}
                    </div>
                </div>
            `;
        }
        htmlPayment += `
            <div class="row mb-1">
                <div class="col test-start">
                    Pembayaran Akan dilakukan dengan Metode
                </div>
            </div>
            <div class="row mb-1">
                <div class="col-3">
                    <span>Cash</span>
                </div>
                <div class="col-2">
                    <span>cek/giro<span>
                </div>
                <div class="col-2 text-start">
                    <span>No<span><br>
                    <span>Tgl</span>
                </div>
                <div class="col-2 text-end">
                    <span>Transfer<span>
                </div>
                <div class="col-3 text-end">
                    <span>BANK BRI cab Jatinegara</span><br>
                    <span>A/C No: 1234567890</span><br>
                    <span>Trinitra Wahana Kreasi</span>
                </div>
            </div>
        `;

        document.getElementById('tampilPayment').innerHTML = htmlPayment;

        let htmlTtd = `
            <div class="row text-center">
                <div class="col-6">
                    <div class="semi-bold">Pihak Rombongan</div>
                    <div style="height: 80px;"></div>
                    <div class="semi-bold">${data.client_pic ?? '-'}</div>
                </div>
                <div class="col-6">
                    <div class="semi-bold">Pihak Water Kingdom</div>
                    <div style="height: 80px;"></div>
                    <div class="semi-bold">${data.marketing ?? '-'}</div>
                </div>
            </div>
        `;
        document.getElementById('tandaTangan').innerHTML = htmlTtd;

    </script>

    <script>
        window.onload = function(){
            window.print();
            // setTimeout(function(){
            //     document.getElementById('loading').style.display = 'none';
            //     window.print();
            // }, 1500);
        }
    </script>

  </body>
</html>