<?php
require '../../assets/fungsi.php'; 

// Ambil client_id dan client_name dari POST
$client_id = $_POST['client_id'] ?? '';
$client_name = $_POST['client_name'] ?? '';

// Panggil fungsi-fungsi Anda. 
$viewBudgeting = getViewBudgeting ($konek, $client_id);
$rombonganOk = getRombonganOk ($konek, $client_id); 
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print Group Package Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <!-- <style>
        /* Gaya tambahan untuk cetak yang lebih rapi */
        @media print {
            body { font-size: 8pt; }
            .container { max-width: 100%; }
        }
        .row div {
            min-height: 1.2em; /* Memastikan baris terlihat rapi */
        }
    </style> -->
    <style>
        @media print {
            /* 1. Atur margin cetak ke nol. Ini 'mendorong' header/footer browser ke area yang tidak terlihat. */
            @page {
                margin: 0;
                size: A4 portrait; /* Opsional: Mengatur ukuran kertas */
            }

            /* 2. Beri padding pada body/container untuk memastikan konten Anda tetap memiliki margin visual */
            body {
                /* Nilai padding ini (misalnya 1cm atau 2cm) akan menjadi margin konten Anda */
                font-size: 8pt;
                padding-top: 0.25cm;
                padding-bottom: 0.25cm;
                padding-left: 0.25cm;
                padding-right: 0.25cm;
                
                margin: 0; /* Pastikan margin body juga nol */
            }

            .container { max-width: 100%; }
            
            /* Optional: Hilangkan elemen non-konten (misalnya tombol) */
            .no-print {
                display: none !important;
            }
        }
    </style>
  </head>
  <body>
    <div class="container text-center">
      <h3>GROUP PACKAGE CONFIRMATION FORM</h3>
        <div class="row text-start">
            <div class="col-5">
              <div class="row">
                  <label for="" class="col-sm col-form-label">Date Of Visit/Day</label>
                  <div class="col-sm col-form-label">
                    <span id="tgl_plan"></span>
                  </div>
              </div>
              <div class="row">
                  <label for="" class="col-sm col-form-label">Name Of Group</label>
                  <div class="col-sm col-form-label">
                    <span id="instansi"></span>
                  </div>
              </div>
              <div class="row">
                  <label for="" class="col-sm col-form-label">Contact Person</label>
                  <div class="col-sm col-form-label">
                    <span id="pic"></span>
                  </div>
              </div>
            </div>
            <div class="col-5">
              <div class="row">
                  <label for="" class="col-sm col-form-label">Telphone</label>
                  <div class="col-sm col-form-label">
                    <span id="tlp"></span>
                  </div>
              </div>
              <div class="row">
                  <label for="" class="col-sm col-form-label">Faxcimile</label>
                  <div class="col-sm col-form-label">
                    <span id="">: -</span>
                  </div>
              </div>
              <div class="row">
                  <label for="" class="col-sm col-form-label">PIC</label>
                  <div class="col-sm col-form-label">
                    <span id="sales"></span>
                  </div>
              </div>
            </div>
            <div class="col-2 text-end">
                <p class="fw-bold">Estimated Cost</p>
                <div class="small">
                    ID Client: <span id="idCl" class="fw-bold"></span>
                </div>
                <div class="small">
                    Tgl Input: <span id="tgl_in" class="fw-bold"></span>
                </div>
                <div class="small">
                    Tema: <span id="tema" class="fw-bold"></span>
                </div>
                <div class="small">
                    Pax: <span id="jumlah" class="fw-bold"></span>
                </div>
            </div>
        </div>

        <!-- <div class="row border border-dark fw-bold bg-light p-1">
            <div class="col-10">
                <div class="row">
                    <div class="col-5">Fasilitas / Group</div>
                    <div class="col-2 text-center">Qty</div>
                    <div class="col-1 text-center"> </div>
                    <div class="col-2 text-end">Price / Cost</div>
                    <div class="col-2 text-end">Subtotal</div>
                </div>
            </div>
            <div class="col-2 text-center">REMARK</div>
        </div> -->

        <!-- bagian pendapatan dan pengeluaran -->
        <div class="row border border-dark">
            <div class="row text-start">
                <div class="col-10 border border-dark">
                    <div class="row">
                        <p class="fw-bold mt-2 mb-1">1. PENDAPATAN</p>
                        <div id="detail_pendapatan"></div>
                        <div class="row text-end mt-2">
                            <div class="col-7"></div>
                            <div class="col-3"><strong>TOTAL PENDAPATAN :</strong></div>
                            <div class="col-2"><strong id="total_pendapatan"></strong></div>
                        </div>
                    </div>
                    <div class="row">
                        <p class="fw-bold mt-2 mb-1">2. BIAYA</p>
                        <div id="detail_pengeluaran"></div> 
                        <div class="row text-end mt-2">
                            <div class="col-7"></div>
                            <div class="col-3"><strong>TOTAL BIAYA :</strong></div>
                            <div class="col-2"><strong id="total_pengeluaran"></strong></div>
                        </div>
                    </div>
                    <div class="row text-end mt-2 mb-3">
                        <div class="col-8 text-start"><strong>3. PROFIT</strong></div>
                        <div class="col-2"></div>
                        <div class="col-2"><strong id="gross_profit"></strong></div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="row">
                        <p class="fw-bold mt-2 mb-1 text-center">REMARK</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row border border-dark">
            <div class="row text-start">
                <div class="col-10 border border-dark">
                    <div class="row">
                        <p class="fw-bold mt-2 mb-1">PAYMENT</p>
                    </div>
                </div>
                <div class="col-2">
                      <div class="row">
                        <p class="fw-bold mt-2 mb-1">Total Payment</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row border border-dark">
            <div class="row">
                <div class="col-2 border border-dark">Dibuat Oleh,</div>
                <div class="col-8 border border-dark">Diperiksa</div>
                <div class="col-2"></div>
            </div>
        </div>
        <div class="row border border-dark">
            <div class="row">
                <div class="col-2 border border-dark">Sales</div>
                <div class="col-2 border border-dark">Asst Sales Manager</div>
                <div class="col-2 border border-dark">Purchasing</div>
                <div class="col-4 border border-dark">ACC & FAT</div>
                <div class="col-2">Director</div>
            </div>
        </div>
        <div class="row border border-dark" style="height:160px">
            <div class="row">
                <div class="col-2 border border-dark"></div>
                <div class="col-2 border border-dark"></div>
                <div class="col-2 border border-dark"></div>
                <div class="col-2 border border-dark"></div>
                <div class="col-2 border border-dark"></div>
                <div class="col-2"></div>
            </div>
        </div>
        <div class="row border border-dark">
            <div class="row">
                <div class="col-2 border border-dark">Dedi</div>
                <div class="col-2">Septian Adi</div>
                <div class="col-2 border border-dark">Rahman J Subita</div>
                <div class="col-2 border border-dark">Nanda</div>
                <div class="col-2 border border-dark">Nur Walidi</div>
                <div class="col-2">S. Widi Karyaningsih</div>
            </div>
        </div>
        
        <!-- <div class="row text-end mt-2">
            <div class="col-7"></div>
            <div class="col-3"><strong>TOTAL Pendapatan:</strong></div>
            <div class="col-2"><strong id="total_pendapatan1"></strong></div>
        </div>
        <div class="row text-end mt-2">
            <div class="col-7"></div>
            <div class="col-3"><strong>TOTAL PENGELUARAN:</strong></div>
            <div class="col-2"><strong id="total_pengeluaran1"></strong></div>
        </div> -->

        <!-- <div class="row text-end mt-3 border-top border-dark pt-2">
            <div class="col-7"></div>
            <div class="col-3"><strong>GROSS PROFIT:</strong></div>
            <div class="col-2"><strong id="gross_profit"></strong></div>
        </div> -->

        <!--akhir bagian pendapatan dan pengeluaran -->
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <script>
        const viewRombonganOk = <?= json_encode($rombonganOk); ?>;
        const viewBudgeting = <?= json_encode($viewBudgeting); ?>; 

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
            const groupOrder = ['Operasional', 'Event', 'Vendor', 'Food and Beverages', 'cabana and cabin'];

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
            const groupOrder = ['Operasional', 'Event', 'Vendor', 'Food and Beverages', 'cabana and cabin'];

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


        $(document).ready(function() {
            
            // --- 1. MENGISI DETAIL ROMBONGAN (Header) ---
            let dataRombongan = {};
            if (viewRombonganOk && viewRombonganOk.length > 0) {
                dataRombongan = viewRombonganOk[0];
                
                // ... (Logika pengisian data rombongan di sini) ...
                
                let formattedDate = '-';
                if (dataRombongan.date_plan) {
                    const datePlan = new Date(dataRombongan.date_plan);
                    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }; 
                    formattedDate = datePlan.toLocaleDateString('id-ID', options); 
                }
                let formatDateInput = '-';
                if (dataRombongan.date_input){
                    const dateInput = new Date(dataRombongan.date_input);
                    const day = dateInput.getDate().toString().padStart(2, '0');
                    const year = dateInput.getFullYear();
                    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
                    const monthShort = monthNames[dateInput.getMonth()];
                    formatDateInput = `${day}-${monthShort}-${year}`;
                }

                $('#tgl_plan').text(': ' + formattedDate);
                $('#instansi').text(': ' + dataRombongan.client_name || '-');
                $('#pic').text(': ' + dataRombongan.client_pic || '-');
                $('#tlp').text(': ' + dataRombongan.phone || '-');
                $('#sales').text(': ' + dataRombongan.marketing || '-'); 

                $('#idCl').text(dataRombongan.client_id || '-'); 
                $('#tgl_in').text(formatDateInput); 
                $('#tema').text(dataRombongan.judul || '-'); 
                $('#jumlah').text(dataRombongan.jumlah_pax +' pax' || '-'); 
            }


            // --- 2. PROSES PENDAPATAN & PENGELUARAN ---
            let totalPendapatan = 0;
            let totalPengeluaran = 0;
            let initialRevenue = 0;
            let initialRevenueHtml = '';

            // A. HITUNG DAN SIAPKAN TIKET UTAMA (ROMBONGAN_MASTER)
            if (dataRombongan.jumlah_pax && dataRombongan.hrg_tiket) {
                const pax = parseInt(dataRombongan.jumlah_pax || 0);
                const ticketPrice = parseFloat(dataRombongan.hrg_tiket || 0);
                
                initialRevenue = pax * ticketPrice;
                totalPendapatan += initialRevenue;
                totalPengeluaran += initialRevenue;
                
                // HTML untuk item Harga Tiket
                initialRevenueHtml = `
                    <div class="row ps-4 align-items-center" style="line-height: 1.2;">
                        <div class="col-4"><strong>Tiket Rombongan</strong></div>
                        <div class="col-2 text-center">${pax}</div>
                        <div class="col-1 text-end">x</div>
                        <div class="col-2 text-end">${formatRupiah(ticketPrice)}</div>
                        <div class="col-1 text-end">=</div>
                        <div class="col-2 text-end">${formatRupiah(initialRevenue)}</div>
                    </div>`;
            } else {
                initialRevenueHtml = '<p class="ps-4">Data tiket utama tidak ditemukan.</p>';
            }

            //Vendor
            if (viewBudgeting.qty && viewBudgeting.price_vend) {
                const qty = parseInt(viewBudgeting.qty || 0);
                const hrg_vend = parseFloat(viewBudgeting.price_vend || 0);
                
                initialCost = qty * hrg_vend;
                totalPengeluaran += initialCost += initialRevenue;
            } else {
                initialCost = '<p class="ps-4">Data tiket utama tidak ditemukan.</p>';
            }

            // B. PROSES DATA FASILITAS (ROMBONGAN_DETAIL)
            if (viewBudgeting && viewBudgeting.length > 0) {
                
                // 1. PENDAPATAN FASILITAS (menggunakan kolom 'price')
                const revenueResult = displayBudgetingDetails(viewBudgeting, 'price');
                totalPendapatan += revenueResult.total;

                // Gabungkan HTML Tiket Utama dan Fasilitas
                $('#detail_pendapatan').html(initialRevenueHtml + revenueResult.html);
                $('#total_pendapatan').text(formatRupiah(totalPendapatan));
                $('#total_pendapatan1').text(formatRupiah(totalPendapatan));


                // 2. PENGELUARAN FASILITAS (menggunakan kolom 'price_vend')
                const costResult = displayBudgetingDetailsVend(viewBudgeting, 'price');
                totalPengeluaran += costResult.total;
                $('#detail_pengeluaran').html(initialRevenueHtml + costResult.html);
                $('#total_pengeluaran').text(formatRupiah(totalPengeluaran));
                $('#total_pengeluaran1').text(formatRupiah(totalPengeluaran));
                
                // Hitung Gross Profit
                const grossProfit = totalPendapatan - totalPengeluaran;
                $('#gross_profit').text(formatRupiah(grossProfit));

            } else {
                // Jika hanya ada data tiket utama
                $('#detail_pendapatan').html(initialRevenueHtml);
                $('#total_pendapatan').text(formatRupiah(totalPendapatan));
                
                // Data budgeting kosong
                $('#detail_pengeluaran').html('<p class="ps-4">Data fasilitas pengeluaran tidak ditemukan.</p>');
                $('#total_pengeluaran').text(formatRupiah(0));
                $('#gross_profit').text(formatRupiah(totalPendapatan)); 
            }
        });
    </script>

  </body>
</html>