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
        <title>Data Sales</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../../css/styles.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css"
        integrity="sha512-SzlrxWUlpfuzQ+pcUCosxcglQRNAq/DZjVsC0lE40xsADsfeQoEypE+enwcOiGjk/bSuGGKHEyjSoQ1zVisanQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"/>"
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

                        <div class="modal fade" id="editSales" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form id="formUdateSales" method="POST">
                                        <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">Data Sales <span id="modalMarketingName"></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                        </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="card">
                                                <div class="card-body">
                                                    <input type="hidden" id="id_sales" name="Employee_ID">
                                                    <div class="mb-3 row">
                                                        <label for="name" class="col-sm-4 col-form-label">Nama</label>
                                                        <div class="col-sm-8">
                                                        <input type="text" class="form-control" id="name" name="name"/>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="departemen" class="col-sm-4 col-form-label">Bagian</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select" id="departemen" name="departemen">
                                                                <option value="SEKOLAH">Sekolah</option>
                                                                <option value="PERUSAHAAN">Perusahaan</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="status" class="col-sm-4 col-form-label">status</label>
                                                        <div class="col-sm-8">
                                                            <select class="form-select" id="status" name="status">
                                                                <option value="1">Aktif</option>
                                                                <option value="0">Tidak Aktif</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3 row">
                                                        <label for="tgl_masuk" class="col-sm-4 col-form-label">Tanggal Masuk</label>
                                                        <div class="col-sm-8">
                                                        <input type="date" class="form-control" id="tgl_masuk" name="tgl_masuk"/>
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

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../../js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../../js/datatables-simple-demo.js"></script>

        <script>
            function formatTanggal(tanggalString) {
                const tgl = new Date(tanggalString);
                const hari = String(tgl.getDate()).padStart(2, '0');
                const bulan = String(tgl.getMonth() + 1).padStart(2, '0'); // Bulan dimulai dari 0
                const tahun = tgl.getFullYear();
                return `${hari}-${bulan}-${tahun}`;
            }
        </script>
        <script>
            const viewSales = <?= json_encode($sales); ?>;
            const tbody = document.getElementById("dtSales");
            console.log(viewSales);
            viewSales.forEach((item, index) =>{
                const row = document.createElement("tr");
                row.innerHTML = `
                <td>${index+1}</td>
                <td>${item.name}</td>
                <td>${item.departemen}</td>
                <td>${item.checked != '1' ? 'Tidak Aktif' : 'Aktif'}</td>
                <td>${formatTanggal(item.tgl_mulai)}</td>
                <td>
                    <button class="btn btn-warning btnEdit" data-bs-toggle="modal" data-bs-target="#editSales"
                        data-id="${item.Employee_ID}" data-name="${item.name}" data-departemen="${item.departemen}"
                        data-status="${item.checked}" data-tgl="${item.tgl_mulai}"><i class="fa-solid fa-file-pen"></i>Edit
                    </button>
                </td>
                `;
                tbody.appendChild(row);
            });
        </script>
        
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const tbody = document.getElementById("dtSales");

                tbody.addEventListener("click", function (e) {
                    const target = e.target.closest(".btnEdit");
                    if (!target) return;
                    
                    const sales_id = target.getAttribute("data-id");
                    const name = target.getAttribute("data-name");
                    const departemen = target.getAttribute("data-departemen");
                    const status = target.getAttribute("data-status");
                    const tgl = target.getAttribute("data-tgl");

                    document.getElementById("id_sales").value = sales_id;
                    document.getElementById("name").value = name;
                    document.getElementById("departemen").value = departemen; 
                    document.getElementById("status").value = status;
                    document.getElementById("tgl_masuk").value = tgl;
                    document.getElementById("modalMarketingName").textContent = name;
                });
            });
        </script>
        <script>
            document.getElementById("formUdateSales").addEventListener("submit", function(e) {
                e.preventDefault();

                const formData = new FormData(this);
                formData.append('aksi', 'update_sales');

                fetch('../../assets/fungsi.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(res => {
                    if (res.status === 'success') {
                        // Tutup modal terlebih dahulu
                        const modal = bootstrap.Modal.getInstance(document.getElementById("editSales"));
                        modal.hide();

                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Data Berhasil Di Perbaharui',
                            showConfirmButton: true, // Tampilkan tombol konfirmasi
                            confirmButtonText: 'Oke', // Teks tombol konfirmasi
                            allowOutsideClick: false, // Tidak bisa menutup dengan klik di luar
                            allowEscapeKey: false // Tidak bisa menutup dengan tombol Escape
                        }).then((result) => {
                            // Jika tombol "Oke" diklik
                            if (result.isConfirmed) {
                                location.reload(); // Refresh halaman
                            }
                        });

                        // Hapus kode pembaruan DOM manual di sini
                        // const id = document.getElementById("id_sales").value;
                        // ... dan seterusnya
                    } else if (res.status === 'nochange') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Tidak Ada Perubahan!',
                            text: 'Tidak Ada Data Yang Berubah',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        const modal = bootstrap.Modal.getInstance(document.getElementById("editSales"));
                        modal.hide();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: res.msg || 'Terjadi kesalahan yang tidak diketahui.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error Jaringan',
                        text: 'Gagal terhubung ke server. Periksa koneksi internet Anda.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });
            });
        </script>
    </body>
</html>