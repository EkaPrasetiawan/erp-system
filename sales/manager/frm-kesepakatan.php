<?php
require '../../assets/fungsi.php'; 

// Ambil rombongan_id dan client_name dari POST
$rombongan_id = $_POST['rombongan_id'] ?? '';
$client_name = $_POST['client_name'] ?? '';

// Panggil fungsi-fungsi Anda. 
$viewBudgeting = getViewBudgetingP ($konek, $rombongan_id);
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
                <div class="col-8">: <span id="tgl_plan"></span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Rencana Kedatangan</div>
                <div class="col-8">: <span>sjhfskb</span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Alamat</div>
                <div class="col-8">: <span>sjhfskb</span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Phone</div>
                <div class="col-8">: <span>sjhfskb</span></div>
              </div>
              <p class="fw-bold mb-1 mt-2">PENANGGUNG JAWAB</p>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Nama</div>
                <div class="col-8">: <span>sjhfskb</span></div>
              </div>
              <div class="row mb-1">
                <div class="col-4 fw-semibold">Posisi</div>
                <div class="col-2">: <span>sjhfskb</span></div>
                <div class="col fw-semibold text-end">HP</div>
                <div class="col-4">: <span>sjhfskb</span></div>
              </div>
            </div>
        </div>

        <!-- bagian pendapatan dan pengeluaran -->
        <div class="row">
            <div class="col-12">
                <p class="fw-bold mb-1 mt-3 text-start">PEMESANAN TIKE/PAKET</p>
                <div class="row text-start">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="row text-start">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        const viewRombonganOk = <?= json_encode($rombonganOk); ?>;
        const viewBudgeting = <?= json_encode($viewBudgeting); ?>;
        const paymentView = <?= json_encode($viewPay); ?>;

        const isHtmOnly = !viewBudgeting || viewBudgeting.length === 0;
        const totalBox = document.getElementById('box_total_biaya');

        // Fungsi helper untuk format mata uang Rupiah
        const formatRupiah = (angka) => {
            if (angka === null || angka === undefined || angka === '') return 'Rp. 0';
            const number = parseFloat(angka);
            if (isNaN(number)) return 'Rp. 0';
            
            return new Intl.NumberFormat('id-ID', { 
                style: 'currency', 
                currency: 'IDR',
                minimumFractionDigits: 0 
            }).format(number).replace('IDR', 'Rp.');
        };
        
        // Fungsi utama untuk memproses dan mengelompokkan data Fasilitas
        // Fungsi ini diubah agar MENGEMBALIKAN HTML dan Total, BUKAN menampilkannya langsung.
        const displayBudgetingDetails = (data, priceColumn) => {
            let htmlContent = '';
            let currentGroup = null;
            let total = 0;
            
            // Tentukan urutan kelompok fasilitas yang diinginkan
            const groupOrder = ['Tiket Masuk','Operasional', 'Event', 'Vendor', 'Food and Beverages', 'cabana and cabin'];

            // Fungsi untuk mendapatkan indeks urutan. Jika grup tidak ada di list, taruh di akhir (99).
            const getGroupIndex = (groupName) => {
                // Pastikan grup yang tidak terisi (null/empty) diperlakukan sebagai 'LAINNYA'
                const name = groupName ? groupName.toLowerCase() : 'lainnya'; 
                const index = groupOrder.findIndex(g => g.toLowerCase() === name);
                return index !== -1 ? index : 99;
            };

            // Urutkan data berdasarkan indeks urutan yang ditetapkan
            data.sort((a, b) => {
                const indexA = getGroupIndex(a.group_fasilitas);
                const indexB = getGroupIndex(b.group_fasilitas);
                
                // Bandingkan indeks urutan
                if (indexA < indexB) return -1;
                if (indexA > indexB) return 1;
                
                // Jika indeks sama, urutkan berdasarkan nama fasilitas (opsional)
                if (a.fasilitas_name < b.fasilitas_name) return -1;
                if (a.fasilitas_name > b.fasilitas_name) return 1;
                return 0;
            });

            data.forEach(item => {
                const price = parseFloat(item[priceColumn] || 0);
                const qty = parseInt(item.qty || 0);

                // Hanya proses jika harga/cost item ini lebih dari 0
                if (price > 0 && qty > 0) {
                    const subtotal = qty * price;
                    total += subtotal;
                    const groupName = item.group_fasilitas ? item.group_fasilitas.toUpperCase() : 'LAINNYA';

                    // Jika Group Fasilitas berubah, buat header baru
                    if (groupName !== currentGroup) {
                        htmlContent += `
                            <div class="row mt-2 ps-4">
                                <div class="col-12"><strong>${groupName}</strong></div>
                            </div>`;
                        currentGroup = groupName;
                    }

                    // Baris detail Fasilitas
                    htmlContent += `
                        <div class="row ps-4 align-items-center" style="line-height: 1.2;">
                            <div class="col-4">${item.fasilitas_name}</div>
                            <div class="col-2 text-center">${item.qty}</div>
                            <div class="col-1 text-end">x</div>
                            <div class="col-2 text-end">${formatRupiah(price)}</div>
                            <div class="col-1 text-end">=</div>
                            <div class="col-2 text-end">${formatRupiah(subtotal)}</div>
                        </div>`;
                }
            });
            
            // Mengembalikan hasil (HTML dan Total)
            return {
                html: htmlContent || `<p class="ps-4">Data fasilitas tidak ditemukan.</p>`,
                total: total
            };
        };

        const displayBudgetingDetailsVend = (data, priceColumn) => {
            let htmlContent = '';
            let currentGroup = null;
            let total = 0;
            let currentGroupSubtotal = 0; 
            
            // Tentukan urutan kelompok fasilitas yang diinginkan
            const groupOrder = ['Tiket Masuk','Operasional', 'Event', 'Vendor', 'Food and Beverages', 'cabana and cabin'];

            // Fungsi untuk mendapatkan indeks urutan.
            const getGroupIndex = (groupName) => {
                const name = groupName ? groupName.toLowerCase() : 'lainnya'; 
                const index = groupOrder.findIndex(g => g.toLowerCase() === name);
                return index !== -1 ? index : 99;
            };

            // Urutkan data berdasarkan indeks urutan yang ditetapkan
            data.sort((a, b) => {
                const indexA = getGroupIndex(a.group_fasilitas);
                const indexB = getGroupIndex(b.group_fasilitas);
                
                if (indexA < indexB) return -1;
                if (indexA > indexB) return 1;
                
                if (a.fasilitas_name < b.fasilitas_name) return -1;
                if (a.fasilitas_name > b.fasilitas_name) return 1;
                return 0;
            });

            data.forEach((item, index) => {
                const groupNameText = item.group_fasilitas ? item.group_fasilitas.toUpperCase() : 'LAINNYA';
                
                let finalPrice = 0;
                
                // LOGIKA PENENTUAN HARGA BARU
                if (priceColumn === 'price') { // Jika ini adalah bagian PENDAPATAN
                    if (groupNameText === 'VENDOR') {
                        // Untuk grup VENDOR, ambil dari price_vend
                        finalPrice = parseFloat(item.price_vend || 0);
                    } else {
                        // Untuk grup lain, ambil dari price (seperti biasa)
                        finalPrice = parseFloat(item.price || 0);
                    }
                } else { // Jika ini adalah bagian PENGELUARAN (price_ven)
                    finalPrice = parseFloat(item.price_vend || 0);
                }

                const price = finalPrice;
                const qty = parseInt(item.qty || 0);

                if (price > 0 && qty > 0) {
                    
                    const subtotal = qty * price;

                    // 1. Cek pergantian grup
                    if (groupNameText !== currentGroup && currentGroup !== null) {
                        // Tampilkan subtotal grup sebelumnya
                        htmlContent += `
                            <div class="row ps-4 pt-1 pb-1 bg-light fw-bold border-bottom border-secondary pb-1">
                                <div class="col-8 text-end">SUBTOTAL ${currentGroup}</div>
                                <div class="col-2 text-end">=</div>
                                <div class="col-2 text-end">${formatRupiah(currentGroupSubtotal)}</div>
                            </div>`;
                        currentGroupSubtotal = 0; // Reset subtotal
                    }
                    
                    // 2. Tampilkan header grup baru
                    if (groupNameText !== currentGroup) {
                        htmlContent += `
                            <div class="row mt-2 ps-4">
                                <div class="col-12"><strong>${groupNameText}</strong></div>
                            </div>`;
                        currentGroup = groupNameText;
                    }
                    
                    // 3. Tambahkan baris detail fasilitas
                    htmlContent += `
                        <div class="row ps-4 align-items-center" style="line-height: 1.2;">
                            <div class="col-4">${item.fasilitas_name}</div>
                            <div class="col-2 text-center">${item.qty}</div>
                            <div class="col-1 text-end">x</div>
                            <div class="col-2 text-end">${formatRupiah(price)}</div>
                            <div class="col-1 text-end">=</div>
                            <div class="col-2 text-end">${formatRupiah(subtotal)}</div>
                        </div>`;

                    // 4. Update total keseluruhan dan subtotal grup
                    total += subtotal;
                    currentGroupSubtotal += subtotal;

                    // 5. Cek jika ini adalah item terakhir, tampilkan subtotal grup terakhir
                    if (index === data.length - 1) {
                        htmlContent += `
                            <div class="row ps-4 pt-1 pb-1 bg-light fw-bold border-bottom border-secondary pb-1">
                                <div class="col-8 text-end">SUBTOTAL ${currentGroup}</div>
                                <div class="col-2 text-end">=</div>
                                <div class="col-2 text-end">${formatRupiah(currentGroupSubtotal)}</div>
                            </div>`;
                    }
                }
            });
    
            // Mengembalikan hasil (HTML dan Total)
            return {
                html: htmlContent || `<p class="ps-4">Data fasilitas tidak ditemukan.</p>`,
                total: total
            };
        };

        const isMobile = window.innerWidth < 768;
        if (isMobile) {
            document.body.classList.add('mobile-print');
        }


        $(document).ready(function() {
            if (isHtmOnly) {
                totalBox.style.display = 'none';
            } else {
                totalBox.style.display = 'flex'; // atau 'block' sesuai layout
            }

            function formatTanggal(dateString) {
                if (!dateString || dateString === "0000-00-00 00:00:00") 
                    return "-";
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return "-"; // Cek jika tanggal tidak valid
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            }
            
            // --- 1. MENGISI DETAIL ROMBONGAN (Header) ---
            // hitung jumlah tiket masuk dari viewBudgeting
            let jumlahTiketMasuk = 0;

            if (Array.isArray(viewBudgeting)) {
                viewBudgeting.forEach(item => {
                    if (
                        item.fasilitas_name &&
                        item.fasilitas_name.toLowerCase() === 'tiket masuk' &&
                        parseInt(item.point) === 1
                    ) {
                        jumlahTiketMasuk += parseInt(item.qty || 0);
                    }
                });
            }
            //tampilkan data dari Rombongan ok
            let dataRombongan = {};
            if (viewRombonganOk && viewRombonganOk.length > 0) {
                dataRombongan = viewRombonganOk[0];
                
                // ... (Logika pengisian data rombongan di sini) ...

                $('#tgl_plan').text(': ' + formatTanggal(dataRombongan.date_plan));
                $('#instansi').text(': ' + (dataRombongan.client_name || '-'));
                $('#pic').text(': ' + (dataRombongan.client_pic || '-'));
                $('#tlp').text(': ' + (dataRombongan.phone || '-'));
                $('#sales').text(': ' + (dataRombongan.marketing || '-')); 

                $('#idCl').text(dataRombongan.client_id || '-'); 
                $('#tgl_in').text(formatTanggal(dataRombongan.date_input)); 
                $('#tema').text(dataRombongan.judul || '-'); 
                $('#jumlah').text(jumlahTiketMasuk > 0 ? jumlahTiketMasuk + ' pax' : '-'); 
            }


            // --- 2. PROSES PENDAPATAN & PENGELUARAN ---
            
            let totalPendapatan = 0;
            let totalPengeluaran = 0;
            let totalDP = 0;

            // B. HITUNG FASILITAS TAMBAHAN
            let hasFasilitas = Array.isArray(viewBudgeting) && viewBudgeting.length > 0;

            if (hasFasilitas) {

                // pendapatan fasilitas
                const revenueResult = displayBudgetingDetails(viewBudgeting, 'price');
                totalPendapatan += revenueResult.total;

                // biaya fasilitas
                const costResult = displayBudgetingDetailsVend(viewBudgeting, 'price');
                totalPengeluaran += costResult.total;

                $('#detail_pendapatan').html(revenueResult.html);
                $('#detail_pengeluaran').html(costResult.html);

            } else {

                // HTM only
                $('#detail_pengeluaran').html('<p class="ps-4">HTM ONLY</p>');
            }

            //Tampilkan Nama venor
            let vendorList = [];
            const allowedGroups = ['vendor', 'food and beverages'];
            if (Array.isArray(viewBudgeting)) {
                viewBudgeting.forEach(item => {
                    if (
                        item.group_fasilitas &&
                        allowedGroups.includes(item.group_fasilitas.toLowerCase()) &&
                        item.client_id
                    ) {
                        vendorList.push(item.client_id);
                    }
                });
            }
            const uniqueVendors = [...new Set(vendorList)];

            let vendorHtml = '';
            if (uniqueVendors.length > 0) {
                vendorHtml += `<div class="fw-bold mb-1">Vendor Acara</div>`;
                uniqueVendors.forEach(name => {
                    vendorHtml += `<div>${name}</div>`;
                });

            } else {
                vendorHtml = '-';
            }

            $('#vendor_nameHead').html(vendorHtml);

            // C. TOTAL TAMPIL
            $('#total_pendapatan').text(formatRupiah(totalPendapatan));
            $('#total_pendapatan1').text(formatRupiah(totalPendapatan));
            $('#total_pengeluaran').text(formatRupiah(totalPengeluaran));

            // D. HITUNG PROFIT
            let grossProfit = hasFasilitas
                ? totalPendapatan - totalPengeluaran
                : 0;

            $('#gross_profit').text(formatRupiah(grossProfit));

            // 3. PAYMENT / DP

            let paymentHtml = '';
            let no = 1;

            if (Array.isArray(paymentView) && paymentView.length > 0){

                paymentView.forEach(pay => {

                    // filter hanya DP jika perlu
                    if (pay.jenis && pay.jenis.toUpperCase() !== 'DP') return;

                    const nilai = parseFloat(pay.price || 0);
                    totalDP += nilai;

                    paymentHtml += `
                        <div class="row small text-start">
                            <div class="col-1">${no++}</div>
                            <div class="col-3">${pay.jenis}</div>
                            <div class="col-2">${pay.metode || '-'}</div>
                            <div class="col-3">${formatTanggal(pay.date_pay)}</div>
                            <div class="col-3 text-end">${formatRupiah(nilai)}</div>
                        </div>`;
                });

                // paymentHtml += `
                //     <div class="row fw-bold border-top mt-1">
                //         <div class="col-9 text-end">TOTAL DP</div>
                //         <div class="col-3 text-end">${formatRupiah(totalDP)}</div>
                //     </div>`;

            } else {
                paymentHtml = `<div class="row text-center"><div class="col-12">Belum ada DP</div></div>`;
            }

            $('#sisa_payment').html(paymentHtml);

            // 4. HITUNG SISA BAYAR

            let sisaBayar = totalPendapatan - totalDP;
            if (sisaBayar < 0) sisaBayar = 0;

            $('#sisa_bayar').text(formatRupiah(sisaBayar));
            let bayarHtml='';
            bayarHtml = `
                <div class="row small fw-bold text-start">
                    <div class="col-6">Total Payment :</div>
                    <div class="col-6 text-end">${formatRupiah(totalPendapatan)}</div>
                </div>
                <div class="row small text-start mt-4">
                    <div class="col-6">Sisa Payment :</div>
                    <div class="col-6 text-end">${formatRupiah(sisaBayar)}</div>
                </div>`;
            $('#bayarLog').html(bayarHtml);
        });

    </script>

    <!-- <script>
    window.onload = function(){
        setTimeout(function(){
            document.getElementById('loading').style.display = 'none';
            window.print();
        }, 1500);
    }
    </script> -->

  </body>
</html>