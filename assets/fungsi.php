<?php
require 'koneksi.php';
date_default_timezone_set("Asia/Jakarta");

function sanitize_text($input, $strict = false) {
    $input = trim($input);
    if ($strict) {
        return preg_replace("/[^a-zA-Z0-9\s]/", "", $input);
    } else {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}

function getAllSales($konek){
    $sales = [];
    $cekSales = $konek->query(" SELECT Employee_ID, name, departemen, divisi, tgl_mulai, checked
    FROM employee_card
    WHERE divisi = 'sales' AND inactive = 0");
    if($cekSales){
        while ($row = $cekSales->fetch_assoc()) {
        $row['checked'] = ($row['checked'] == 1) ? '1' : '0'; 
        $sales[] = $row;
        }
    } else {
        error_log("Error Data Sales: " . $konek->error);
    }
    return $sales;
}

function getAllRombongan($konek) {
    $allRom = [];
    $cekAllRom = $konek->query("SELECT * FROM client");
    if ($cekAllRom) { // untuk memeriksa queri berhasil atau tidak
        while ($row = $cekAllRom->fetch_assoc()){
            $allRom[] = $row;
        }
    } else {
        error_log("Error fetching all rombongan: " . $konek->error);
    }
    return $allRom;
}

function getAllFasilitas($konek){
    $fasilitas = [];
    $cekFs = $konek->query("SELECT * FROM markom_service");
    if($cekFs){
        while ($row = $cekFs->fetch_assoc()){
            $fasilitas[] = $row;
        }
    } else {
        error_log("error data fasilitas: " . $konek->error);
    }
    return $fasilitas;
}

function getvendor($konek){
    $vendor = [];
    $cekFs = $konek->query("SELECT * FROM vendor");
    if($cekFs){
        while ($row = $cekFs->fetch_assoc()){
            $vendor[] = $row;
        }
    } else {
        error_log("error data fasilitas: " . $konek->error);
    }
    return $vendor;
}

function getViewVendor($konek){
    $viewVend = [];
    $cekFs = $konek->query("SELECT * FROM vendor_service");
    if($cekFs){
        while ($row = $cekFs->fetch_assoc()){
            $viewVend[] = $row;
        }
    } else {
        error_log("error data fasilitas: " . $konek->error);
    }
    return $viewVend;
}

function getFasilitasWK($konek){
    $dataFs = [];
    $result =$konek->query("SELECT group_head, group_detail FROM markom_service WHERE group_head != 'Food and Beverages'");
    if($result){
        while($row = $result->fetch_assoc()){
            $dataFs[] = $row;
        }
    } else {
        error_log("error data tidak ditemukan: " . $konek->error);
    }
    return $dataFs;
}

function getKategoriFst($konek) {
    $kategori = [];
    // Menggunakan DISTINCT untuk memastikan hanya mengambil nilai unik
    $result = $konek->query("SELECT DISTINCT group_head FROM markom_service WHERE group_head != 'Food and Beverages'");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $kategori[] = $row['group_head'];
        }
    } else {
        error_log("Error fetching facility categories: " . $konek->error);
    }
    return $kategori;
}

function getKodeVen(mysqli $konek): string {
    $prefix = "VEN";
    $lastId = 0;
    $bulan  = date("m");

    $stmt = $konek->prepare("SELECT MAX(id_ven) AS max_id FROM vendor");
    if ($stmt) {
        if ($stmt->execute()) {
            $stmt->bind_result($maxid);
            if ($stmt->fetch()) {
                $lastId = (int)$maxid;
            }
        } else {
            error_log("Eksekusi query gagal: " . $stmt->error);
        }
        $stmt->close();
    } else {
        error_log("Prepare query gagal: " . $konek->error);
    }

    $nextId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);
    return $prefix . $bulan . $nextId;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'update_sales') {
    // Gunakan prepared statements untuk mencegah SQL Injection
    $id         = trim($_POST['Employee_ID']);
    $name       = strtoupper(trim($_POST['name']));
    $departemen = trim($_POST['departemen']);
    $status     = trim($_POST['status']);
    $tgl_masuk  = trim($_POST['tgl_masuk']);

    // Ambil data lama untuk cek perubahan menggunakan prepared statement
    $stmt_cek = $konek->prepare("SELECT Employee_ID, name, departemen, checked, tgl_mulai FROM employee_card WHERE Employee_ID = ?");
    $stmt_cek->bind_param("s", $id); // "s" untuk string, sesuaikan dengan tipe Employee_ID Anda
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();
    $cek = $result_cek->fetch_assoc();
    $stmt_cek->close();

    if (!$cek) {
        echo json_encode(['status' => 'error', 'msg' => 'Data tidak ditemukan.']);
        exit;
    }

    // Cek apakah ada perubahan data
    if (
        $cek['name'] === $name &&
        $cek['departemen'] === $departemen &&
        $cek['checked'] == $status && // Tetap perbandingan longgar karena 'checked' di database bisa jadi int atau string
        $cek['tgl_mulai'] === $tgl_masuk
    ) {
        echo json_encode(['status' => 'nochange', 'msg' => 'Tidak ada data yang diubah.']);
        exit;
    }

    // Update data menggunakan prepared statement
    $stmt_update = $konek->prepare("
        UPDATE employee_card SET 
            name = ?,
            departemen = ?,
            checked = ?,
            tgl_mulai = ?
        WHERE Employee_ID = ?
    ");
    // "ssiss" -> string, string, integer (untuk checked), string, string (sesuaikan dengan tipe kolom Anda)
    // Asumsi checked adalah integer (0 atau 1) di database. Jika string, gunakan "sssss".
    $stmt_update->bind_param("ssiss", $name, $departemen, $status, $tgl_masuk, $id); 
    
    if ($stmt_update->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        // Log error ini
        error_log("Update Error: " . $stmt_update->error);
        echo json_encode(['status' => 'error']);
    }
    $stmt_update->close();
    exit;
}

//bagian Rombongan
//create coe
function generateKodeClient($konek) {
    $result = $konek->query("SELECT MAX(urut_client) AS max_id FROM client");
    $row = $result->fetch_assoc();
    $lastId = (int)$row['max_id'];

    $prefix = "WK";
    $bulan  = date("m");
    $tahun  = date("y");
    $nextId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);

    return $prefix . $bulan . $tahun . $nextId;
}

$code = generateKodeClient($konek);

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_dataRombongan'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $tgl_kunjungan = sanitize_text($_POST['tgl_kunjungan']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $gate = sanitize_text($_POST['gate']);
        $alamat = sanitize_text($_POST['alamat']);
        $marketing_id = '02-001';
        $marketing_name = 'eka';
        $remark = 'perusahaan';

        $stmt = $konek->prepare("INSERT INTO client(client_id, client_name, address, pic, phone, tgl_kunjungan, jumlah, gate, marketing_id, marketing_name, remarks)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssissss", $kode, $instansi, $alamat, $pic, $noTlp, $tgl_kunjungan, $jumlah, $gate, $marketing_id, $marketing_name, $remark);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            error_log("Tambah Rombongan Error: " . $stmt->error); // Log error untuk debugging
            echo json_encode(['status' => 'error']);
        }
        $stmt->close();
        exit;
    }
    if($_POST['aksi'] === 'update_dataRombongan'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $tglKunjungan = sanitize_text($_POST['tgl_kunjungan']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $gate = sanitize_text($_POST['gate']);
        $alamat = sanitize_text($_POST['alamat']);

        //ambil data
        $stmt_cek = $konek->prepare("SELECT client_name, address, pic, phone, tgl_kunjungan, jumlah, gate FROM client WHERE client_id = ?");
        $stmt_cek->bind_param("s",$kode);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }

        //cek perubahan
        if(
            $cek['client_name'] === $instansi &&
            $cek['address'] === $alamat &&
            $cek['pic'] === $pic &&
            $cek['phone'] === $noTlp &&
            $cek['tgl_kunjungan'] === $tglKunjungan &&
            $cek['jumlah'] === $jumlah &&
            $cek['gate'] === $gate
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        //update dta
        $stmt_update = $konek->prepare("UPDATE client SET client_name = ?,
                        address = ?, pic = ?, phone = ?, tgl_kunjungan = ?, jumlah = ?, gate = ? WHERE client_id = ?");
        $stmt_update->bind_param("sssssiss", $instansi, $alamat, $pic, $noTlp, $tglKunjungan, $jumlah, $gate, $kode);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        }else{
            error_log("update rombongan error :" . $stmt_update->error);
            echo json_encode(['status' => 'error']);
        }
        $stmt_update->close();
        exit;
    }

}
//akhir bagian rombongan

//bagian fasilitas
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_fasilitas'){
        $kategori = sanitize_text($_POST['kategori']);
        $nama = sanitize_text($_POST['fasilitas']);
        $jumlah = sanitize_text($_POST['jumlah']);

        $stmt = $konek->prepare("INSERT INTO markom_service(group_head, group_detail, stok)
                VALUES(?,?,?)");
        $stmt->bind_param("ssi", $kategori, $nama, $jumlah);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            error_log("Tambah Rombongan Error: " . $stmt->error); // Log error untuk debugging
            echo json_encode(['status' => 'error']);
        }
        $stmt->close();
        exit;
    }
    if($_POST['aksi'] === 'update_fasilitas'){
        $id_markom = sanitize_text($_POST['up_id']);
        $kategori = sanitize_text($_POST['kategori']);
        $nama = sanitize_text($_POST['fasilitas']);
        $jumlah = sanitize_text(($_POST['qty']));

        $stmt_cek = $konek->prepare("SELECT group_head, group_detail, stok FROM markom_service WHERE id_markom = ?");
        $stmt_cek->bind_param("i", $id_markom);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        //cek perubahan
        if(
            $cek['group_head'] === $kategori &&
            $cek['group_detail'] === $nama &&
            $cek['stok'] === $jumlah
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE markom_service SET group_head = ?, group_detail = ?, stok = ? WHERE id_markom = ?");
        $stmt_update->bind_param("sssi", $kategori, $nama, $jumlah, $id_markom);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("update dta error: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }

}

//bagin vendor
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    //tambah data
    if($_POST['aksi'] === 'tambah_vendor'){
        $kdVendor = sanitize_text($_POST['kdVendor']);
        $nama = sanitize_text($_POST['namaVen']);
        $pic = sanitize_text($_POST['pic']);
        $telephone = sanitize_text($_POST['noTlp']);

        $stmt = $konek->prepare("INSERT INTO vendor(kode_vendor, nama_vendor, pic, noTlp)
                                VALUES(?,?,?,?)");
        $stmt->bind_param("ssss", $kdVendor, $nama, $pic, $telephone);
        if($stmt->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Tambah Data error :" . $stmt->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt->close();
        exit;
    }
    if($_POST['aksi'] === 'update_vendor'){
        $kode = sanitize_text($_POST['up_kdVendor']);
        $namaVen = sanitize_text($_POST['up_namaVen']);
        $pic = sanitize_text($_POST['up_pic']);
        $noTlp = sanitize_text($_POST['up_noTlp']);

        $stmt_cek = $konek->prepare("SELECT nama_vendor, pic, noTlp FROM vendor WHERE kode_vendor = ?");
        $stmt_cek->bind_param("s", $kode);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['nama_vendor'] === $namaVen &&
            $cek['pic'] === $pic &&
            $cek['noTlp'] === $noTlp
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE vendor SET nama_vendor = ?, pic =?, noTlp = ? WHERE kode_vendor = ?");
        $stmt_update->bind_param("ssss", $namaVen, $pic, $noTlp, $kode);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Update Data Error: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }
    //tambah data fasilitas vendor
    if($_POST['aksi'] === 'tambah_fasilitasVendor'){
        $vendor = sanitize_text($_POST['vendorName']);
        $fasilitas = sanitize_text($_POST['fasilitasName']);
        $qty = sanitize_text($_POST['jumlah']);

        $stmt = $konek->prepare("INSERT INTO vendor_service(vendor_head, vendor_detail, stok)
                                VALUES(?,?,?)");
        $stmt->bind_param("ssi", $vendor, $fasilitas, $qty);
        if($stmt->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("tambah data error : " . $stmt->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt->close();
        exit;
    }
    if($_POST['aksi'] === 'update_fasilitasVendor'){
        $id_vend = $_POST['up_id'];
        $vendor = sanitize_text($_POST['up_vendorName']);
        $namafas = sanitize_text($_POST['up_fasilitasName']);
        $qty = $_POST['up_qty'];

        $stmt_cek = $konek->prepare("SELECT vendor_head, vendor_detail, stok FROM vendor_service WHERE id_vendor = ?");
        $stmt_cek->bind_param("i", $id_vend);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['vendor_head'] === $vendor &&
            $cek['vendor_detail'] === $vendor &&
            $cek['stok'] === $qty
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE vendor_service SET vendor_head = ?, vendor_detail = ?, stok = ? WHERE id_vendor = ?");
        $stmt_update->bind_param("ssii", $vendor, $namafas, $qty, $id_vend);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("uodate data gagal: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }
}

//input data fasilitas ke rombongan
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    //query khusus
    if($_POST['aksi'] === 'getView_fasilitas'){

        // $fasil = sanitize_text($_POST['fasilitas_id']);
        $fasil = $_POST['fasilitas_id'] ??'';

        if(!empty($fasil)){
            $stmt = $konek->prepare("SELECT data_id, client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend
            FROM rombongan_detail WHERE fasilitas_id = ?");

            // $stmt = $konek->prepare("SELECT * FROM rombongan_detail WHERE fasilitas_id = ?");

            $stmt->bind_param("s", $fasil);
            $stmt->execute();
            $result = $stmt->get_result();

            $viewData_detail = [];
            if($result){
                while($row = $result->fetch_assoc()){
                    $viewData_detail[] = $row;
                }
            }
            $stmt->close();
            header('Content-Type: application/json');
            echo json_encode($viewData_detail);
            exit;
        }
    }

    //tambah data fsilitas ke rombongan
    if($_POST['aksi'] === 'tambah_fasilitasWK'){
        $idClient = sanitize_text($_POST['cId']);
        $nameClient = sanitize_text($_POST['cName']);
        $headFs = sanitize_text($_POST['kategori']);
        $fasilitas = sanitize_text($_POST['fasilitas']);
        $qty = sanitize_text($_POST['qty']);
        $harga = sanitize_text($_POST['hargaWk']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name)
                                VALUES(?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssiisss", $headFs, $idClient, $fasilitas, $qty, $harga, $tanggal_input, $sales, $nameClient);
        if($stmt->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("gagal: " . $stmt->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt->close();
        exit;
    }

    //update data fasilitas ke rombongan
    if($_POST['aksi'] === 'update_fasilitasWK'){
        $kode = sanitize_text($_POST['idf']);
        $headFs = sanitize_text($_POST['up_kategori']);
        $fasilitas = sanitize_text($_POST['up_fsl']);
        $qty = sanitize_text($_POST['up_qty']);
        $harga = sanitize_text($_POST['up_hargaWk']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt_cek = $konek->prepare("SELECT group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend
            FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $kode);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['group_fasilitas'] === $headFs &&
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['qty'] === $qty &&
            $cek['price'] === $harga
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET group_fasilitas = ?, fasilitas_name = ?, qty = ?, price = ?, using_date = ?, employee_name = ? WHERE data_id = ?");
        $stmt_update->bind_param("ssiissi", $headFs, $fasilitas, $qty, $harga, $tanggal_input, $sales, $kode);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Update data Error: ". $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }

    //tambah fasilitas vendor ke rombongan
    if($_POST['aksi'] === 'tambah_fasilitasVend'){
        $idClient = sanitize_text($_POST['cId']);
        $nameClient = sanitize_text($_POST['cName']);
        $vendorName = sanitize_text($_POST['vendorHead']);
        $VenFasilitas = sanitize_text($_POST['namaFasilitas']);
        $qty = sanitize_text($_POST['qty']);
        $hargaJual = sanitize_text($_POST['harga']);
        $hargaVend = sanitize_text($_POST['hargaVend']);
        $vendor = 'vendor';
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, using_date, employee_name, client_name)
                                VALUES(?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssiiisss", $vendorName, $vendor, $idClient, $VenFasilitas, $qty, $hargaJual, $hargaVend, $tanggal_input, $sales, $nameClient);
        if($stmt->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("gagal: " . $stmt->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'update_fasilitasVend'){
        $idFas = sanitize_text($_POST['idFv']);
        $vendor = sanitize_text($_POST['up_vendorHead']);
        $fasilitas = sanitize_text($_POST['up_namaFasilitas']);
        $jumlah = sanitize_text(($_POST['up_qtyV']));
        $hargaJual = sanitize_text(($_POST['up_harga']));
        $hargaVend = sanitize_text($_POST['up_hargaVend']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt_cek = $konek->prepare("SELECT client_id, fasilitas_name, qty, price, price_vend
                                    FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $idFas);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['client_id'] === $vendor &&
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['qty'] === $jumlah &&
            $cek['price'] === $hargaJual &&
            $cek['price_vend'] === $hargaVend
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        } 
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET client_id = ?, fasilitas_name = ?, qty = ?, price = ?, price_vend =?, using_date = ?, employee_name = ? WHERE data_id = ?");
        $stmt_update->bind_param("ssiiissi", $vendor, $fasilitas, $jumlah, $hargaJual, $hargaVend, $tanggal_input, $sales, $idFas);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Update data Error: ". $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }
}


?>