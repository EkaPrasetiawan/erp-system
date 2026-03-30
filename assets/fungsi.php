<?php

use LDAP\Result;

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

function getAllClient($konek) {
    $allClient = [];
    $cekAllCLient = $konek->query("SELECT * FROM client");
    if ($cekAllCLient) { // untuk memeriksa queri berhasil atau tidak
        while ($row = $cekAllCLient->fetch_assoc()){
            $allClient[] = $row;
        }
    } else {
        error_log("Error fetching all rombongan: " . $konek->error);
    }
    return $allClient;
}

function viewRombongan($konek){
    $allRom = [];
    $result = $konek->query("SELECT * FROM rombongan_master");
    if($result){
        while ($row = $result->fetch_assoc()){
            $allRom[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $allRom;
}

function viewPayemnt($konek, $client_id){
    $safe_client_id = mysqli_real_escape_string($konek, $client_id);

    if(empty($safe_client_id)){
        return [];
    }

    $viewPay = [];
    $result = $konek->query("SELECT * FROM rombongan_payment WHERE rombongan_id = '$safe_client_id'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewPay[] = $row;
        }
    } else {
        error_log("Error fetching payment data: " . $konek->error);
    }
    return $viewPay;
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

    $stmt = $konek->prepare("SELECT MAX(vendor_id) AS max_id FROM vendor");
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

function getCnc($konek, $date, $client_id) {
    // Amankan input tanggal
    $safe_date = mysqli_real_escape_string($konek, $date);
    $safe_client_id = mysqli_real_escape_string($konek, $client_id);

    // Jika tanggal atau client_id kosong, kembalikan array kosong
    if (empty($safe_date) || empty($safe_client_id)) {
        return [];
    }

    // 1. Subquery: Cari NAMA fasilitas yang sudah terpakai.
    $query_booked_names = "
        SELECT
            DISTINCT rd.fasilitas_name
        FROM
            rombongan_detail rd
        JOIN
            rombongan_master rm ON rd.fasilitas_id = rm.client_id
        WHERE
            rm.date_plan = '{$safe_date}'
            AND rm.client_id != '{$safe_client_id}'
            AND rd.del_status = 0
            AND rd.fasilitas_name IS NOT NULL
    ";

    // 2. Query utama: Ambil semua fasilitas yang namanya TIDAK ada dalam daftar yang sudah terpakai.
    $query_available = "
        SELECT
            f.*
        FROM
            facility f
        WHERE
            f.facility_name NOT IN ({$query_booked_names})
        ORDER BY
            f.facility_name
    ";

    $result = mysqli_query($konek, $query_available);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        // Jika gagal, kembalikan array kosong dan catat error untuk debugging
        error_log("Query Gagal getAvailableFasilitasForDate: " . mysqli_error($konek));
        return [];
    }
}

function getFasilitasWK($konek, $date, $client_id) {
    // Amankan input tanggal
    $safe_date = mysqli_real_escape_string($konek, $date);
    $safe_client_id = mysqli_real_escape_string($konek, $client_id);

    // Jika tanggal atau client_id kosong, kembalikan array kosong
    if (empty($safe_date) || empty($safe_client_id)) {
        return [];
    }

    // 1. Subquery: Cari NAMA fasilitas yang sudah terpakai.
    $query_booked_names = "
        SELECT
            DISTINCT rd.fasilitas_name
        FROM
            rombongan_detail rd
        JOIN
            rombongan_master rm ON rd.fasilitas_id = rm.client_id
        WHERE
            rm.date_plan = '{$safe_date}'
            AND rm.client_id != '{$safe_client_id}'
            AND rd.del_status = 0
            AND rd.fasilitas_name IS NOT NULL
            AND rd.fasilitas_name !='Tiket Masuk'
    ";

    // 2. Query utama: Ambil semua fasilitas yang namanya TIDAK ada dalam daftar yang sudah terpakai.
    $query_available = "
        SELECT
            ms.*
        FROM
            markom_service ms
        WHERE
            ms.group_detail NOT IN ({$query_booked_names})
        ORDER BY
            ms.group_detail
    ";

    $result = mysqli_query($konek, $query_available);

    if ($result) {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        // Jika gagal, kembalikan array kosong dan catat error untuk debugging
        error_log("Query Gagal getAvailableFasilitasForDate: " . mysqli_error($konek));
        return [];
    }
}

function getViewBudgeting ($konek, $client_id){
    $sf_client_id = mysqli_real_escape_string($konek, $client_id);
    
    if(empty($sf_client_id)){
        return[];
    }

    $viewBudgeting = [];
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_client_id' AND del_status = 0 AND point = 1");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}

function getViewBudgetingP ($konek, $client_id){
    $sf_client_id = mysqli_real_escape_string($konek, $client_id);
    
    if(empty($sf_client_id)){
        return[];
    }

    $viewBudgeting = [];
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_client_id' AND del_status = 0 AND point = 0");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}

function viewPayment ($konek, $client_id){
    $sfp_client_id = mysqli_real_escape_string($konek, $client_id);

    if(empty($sfp_client_id)){
        return[];
    }
    $viewPaymentR1 = [];
    $result = $konek->query("SELECT * FROM rombongan_payment WHERE rombongan_id = '$sfp_client_id'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewPaymentR1[] = $row;
        }
    } else {
        error_log("error: ".$konek->error);
    }
    return $viewPaymentR1;
}

function getRombonganOk ($konek, $client_id){
    $rombonganOk = [];
    $result = $konek->query("SELECT date_input, date_plan, client_name, client_pic, phone, marketing, judul, jumlah_pax, hrg_tiket, down_payment, clear_payment, dp_uploaded_at, cp_uploaded_at, client_id
                            FROM rombongan_master WHERE client_id = '$client_id' AND del_status = 0");
    if($result){
        while ($row = $result->fetch_assoc()){
            $rombonganOk[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $rombonganOk;
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

    if($_POST['aksi'] === 'tambah_dataClient'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $alamat = sanitize_text($_POST['alamat']);
        $tanggal = date("Y-m-d H:i:s");
        $marketing_id = '03-004';
        $marketing_name = 'chicko';
        $remark = 'perusahaan';

        $stmt = $konek->prepare("INSERT INTO client(client_id, client_name, address, pic, phone, tanggal, marketing_id, marketing_name, remarks)
                                VALUES(?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssssss", $kode, $instansi, $alamat, $pic, $noTlp, $tanggal, $marketing_id, $marketing_name, $remark);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            error_log("Tambah Rombongan Error: " . $stmt->error); // Log error untuk debugging
            echo json_encode(['status' => 'error']);
        }
        $stmt->close();
        exit;
    }
    if($_POST['aksi'] === 'update_dataClient'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $alamat = sanitize_text($_POST['alamat']);

        //ambil data
        $stmt_cek = $konek->prepare("SELECT client_name, address, pic, phone FROM client WHERE client_id = ?");
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
            $cek['phone'] === $noTlp
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        //update dta
        $stmt_update = $konek->prepare("UPDATE client SET client_name = ?,
                        address = ?, pic = ?, phone = ? WHERE client_id = ?");
        $stmt_update->bind_param("sssss", $instansi, $alamat, $pic, $noTlp, $kode);
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
//akhir bagian client

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_dataRombongan'){
        $idRom = sanitize_text($_POST['id_rom']);
        $nama = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $tanggal_plan = sanitize_text($_POST['tgl_dtng']);
        $gate = sanitize_text($_POST['gate']);
        $jumlah = sanitize_text($_POST['pax']);
        $nominal = sanitize_text($_POST['harga']);
        $judul = sanitize_text($_POST['judul']);
        $tgl_input = date("Y-m-d H:i:s");
        $sales = 'Noer Halimah';

        $stmt = $konek->prepare("INSERT INTO rombongan_master(client_id, client_name, date_input, date_plan, client_pic, phone, jumlah_pax, marketing, gate_in, hrg_tiket, judul)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssissis", $idRom, $nama, $tgl_input, $tanggal_plan, $pic, $noTlp, $jumlah, $sales, $gate, $nominal, $judul);
        if($stmt->execute()){
            $newData = [
                "client_id"   => $idRom,
                "client_name" => $nama,
                "client_pic"  => $pic,
                "phone"       => $noTlp,
                "jumlah_pax"  => $jumlah,
                "marketing"   => $sales,
                "date_plan"   => $tanggal_plan,
                "gate_in"     => $gate,
                "hrg_tiket"   => $nominal,
                "judul"       => $judul
            ];

            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                211,
                "add",                    // jenis aksi
                "rombongan_master",       // nama tabel
                $idRom,                   // ID data
                '',                     // old_value (karena INSERT)
                json_encode($newData)     // new_value
            );
            echo json_encode(['status' => 'success']);
        } else {
            error_log("Error Systen: ".$stmt->error);
            echo json_encode(['status' => 'error']);
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'update_dataRombongan'){
        $idRom = sanitize_text($_POST['up_IDrom']);
        $instansi = sanitize_text($_POST['up_instansi']);
        $pic = sanitize_text($_POST['upPic']);
        $tanggal = sanitize_text($_POST['upTgl_dtng']);
        $gate = sanitize_text($_POST['up_gate']);
        $jumlah = sanitize_text($_POST['up_pax']);
        $price = sanitize_text($_POST['upHarga']);
        $judul = sanitize_text($_POST['up_judul']);

        $stmt_cek = $konek->prepare("SELECT client_id, date_plan, jumlah_pax, gate_in, hrg_tiket, judul FROM rombongan_master WHERE client_id = ?");
        $stmt_cek->bind_param("s", $idRom);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        // $cek = $result_cek->fetch_assoc();
        $oldData = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$oldData){
            echo json_encode(['status' => 'error']);
            exit;
        }

        $changes = [];
        if($oldData['date_plan'] != $tanggal)
            $changes['date_plan'] = ['old'=>$oldData['date_plan'], 'new'=>$tanggal];
        if($oldData['jumlah_pax'] != $jumlah)
            $changes['jumlah_pax'] = ['old'=>$oldData['jumlah_pax'], 'new'=>$jumlah];
        if($oldData['gate_in'] != $gate)
            $changes['gate_in'] = ['old'=>$oldData['gate_in'], 'new'=>$gate];
        if($oldData['hrg_tiket'] != $price)
            $changes['hrg_tiket'] = ['old'=>$oldData['hrg_tiket'], 'new'=>$price];
        if($oldData['judul'] != $judul)
            $changes['judul'] = ['old'=>$oldData['judul'], 'new'=>$judul];
        if(empty($changes)){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE rombongan_master SET date_plan = ?, jumlah_pax = ?, gate_in = ?, hrg_tiket = ?, judul = ? WHERE client_id = ?");
        $stmt_update->bind_param("sissss", $tanggal, $jumlah, $gate, $price, $judul, $idRom);
        if($stmt_update->execute()){
            logActivity(
                $konek,
                // $_SESSION['user_id'],      // siapa yg update
                911,
                "update",                  // aksi
                "rombongan_master",        // tabel
                $idRom,                    // record ID
                json_encode($oldData),     // old_value
                json_encode($changes)      // HANYA value yg berubah
            );
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Update Error: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
        }
        $stmt_update->close();
        exit;
    }
    
    //update dp rombongan
    if($_POST['aksi'] === 'update_dp_rombongan'){
        $id_dpRom = sanitize_text($_POST['up_IDromDP']);
        $dp_amount = $_POST['up_dp'] ?? 0;
        //folder img
        $target_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "downPayment" . DIRECTORY_SEPARATOR;
        $image_path = null;
        $uploaded_at = date('Y-m-d H:i:s'); // Catat waktu upload

            // ========== 1. Ambil data lama dulu ==========
        $qOld = $konek->prepare("SELECT down_payment, img_dp, dp_uploaded_at FROM rombongan_master WHERE client_id = ?");
        $qOld->bind_param("s", $id_dpRom);
        $qOld->execute();
        $result = $qOld->get_result();
        $oldRow = $result->fetch_assoc();
        $qOld->close();

        $oldData = json_encode($oldRow); 

        $has_new_image = false;

        if (isset($_FILES['picDP']) && $_FILES['picDP']['error'] === UPLOAD_ERR_OK) {
            $has_new_image = true;
            $file_name = $_FILES['picDP']['name'];
            $file_tmp = $_FILES['picDP']['tmp_name'];
            $file_size = $_FILES['picDP']['size']; 
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
        
            // Pengecekan Ekstensi dan Ukuran
            if (!in_array($file_ext, $allowed_ext)) {
                echo json_encode(['status' => 'error', 'message' => 'Format file DP tidak diizinkan. Hanya JPG, JPEG, PNG.']);
                exit();
            }
            if ($file_size > 2000000) { // 2MB
                echo json_encode(['status' => 'error', 'message' => 'Ukuran file DP melebihi batas 2MB.']);
                exit();
            }
            // Generate nama unik (misalnya: ID_ROMBONGAN_DP_timestamp.ext)
            $new_file_name = $id_dpRom . '_DP_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                // Path relatif yang akan disimpan di database
                $image_path = 'img/downPayment/' . $new_file_name; 
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan file DP yang diupload.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File gambar DP wajib diupload. Error: ' . $_FILES['picDP']['error']]);
            exit();
        }
        // Update Data ke Database
         if ($has_new_image) {
                $stmt_update = $konek->prepare("
                    UPDATE rombongan_master 
                    SET down_payment = ?, img_dp = ?, dp_uploaded_at = ?
                    WHERE client_id = ?
                ");
                $stmt_update->bind_param("isss", $dp_amount, $image_path, $uploaded_at, $id_dpRom);
            } else {
                $stmt_update = $konek->prepare("
                    UPDATE rombongan_master 
                    SET down_payment = ?
                    WHERE client_id = ?
                ");
                $stmt_update->bind_param("is", $dp_amount, $id_dpRom);
            }
    
        if ($stmt_update->execute()) {

            // ========== Ambil data baru ==========
            $qNew = $konek->prepare("SELECT down_payment, img_dp, dp_uploaded_at
                                    FROM rombongan_master WHERE client_id = ?");
            $qNew->bind_param("s", $id_dpRom);
            $qNew->execute();
            $resultNew = $qNew->get_result();
            $newRow = $resultNew->fetch_assoc();
            $qNew->close();

            $newData = json_encode($newRow);

            // ========== Simpan history log ==========
            logActivity(
                $konek,
                112,
                "update_dp",
                "rombongan_master",
                $id_dpRom,
                $oldData,
                $newData,
            );

            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("Update Error: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
        }
        $stmt_update->close();
        exit;
    }

    //payment rombongan
    if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah_payment') {
        $idPay    = mysqli_real_escape_string($konek, $_POST['idPay']);
        $instansi = mysqli_real_escape_string($konek, $_POST['instansi']);
        $pic      = mysqli_real_escape_string($konek, $_POST['picPay']);
        $jenis    = mysqli_real_escape_string($konek, $_POST['jenis']);
        $metode   = mysqli_real_escape_string($konek, $_POST['metode']);
        $tgl_pay  = mysqli_real_escape_string($konek, $_POST['tgl_pay']);
        $price    = mysqli_real_escape_string($konek, $_POST['price']);
        $tgl_input = date("Y-m-d H:i:s");
        $sales = 'Noer Halimah';

        // VALIDASI FILE
        if (empty($_FILES['imgPay']['name'])) {
            echo json_encode(["status"=>"error","message"=>"File bukti wajib diupload"]);
            exit;
        }

        $allowed = ['jpg','jpeg','png'];
        $nama_file = $_FILES['imgPay']['name'];
        $tmp_file  = $_FILES['imgPay']['tmp_name'];
        $size_file = $_FILES['imgPay']['size'];
        $ekstensi  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $allowed)) {
            echo json_encode(["status"=>"error","message"=>"Format file tidak diizinkan"]);
            exit;
        }

        if ($size_file > 2*1024*1024) {
            echo json_encode(["status"=>"error","message"=>"Ukuran file > 2MB"]);
            exit;
        }

        // INSERT TANPA GAMBAR DULU
        $stmt = $konek->prepare("
            INSERT INTO rombongan_payment
            (rombongan_id, rombongan_name, pic, sales, jenis, date_input, date_pay, price, metode)
            VALUES (?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param(
            "sssssssss", $idPay, $instansi, $pic, $sales, $jenis, $tgl_input, $tgl_pay,
            $price, $metode);

        if (!$stmt->execute()) {
            echo json_encode([
                "status"=>"error",
                "message"=>$stmt->error
            ]);
            exit;
        }

        // AMBIL payment_id
        $payment_id = $konek->insert_id;

        // BUAT NAMA FILE BERBASIS ID
        $namaFileBaru =
            "pay_{$payment_id}_{$idPay}_{$jenis}_" .
            time() . "." . $ekstensi;

        $pathUpload = "../img/payments/" . $namaFileBaru;
        $pathDB     = "img/payments/" . $namaFileBaru;

        // UPLOAD FILE
        if (!move_uploaded_file($tmp_file, $pathUpload)) {

            // rollback record DB jika upload gagal
            $konek->query("DELETE FROM rombongan_payment WHERE id='$payment_id'");

            echo json_encode([
                "status"=>"error",
                "message"=>"Upload file gagal"
            ]);
            exit;
        }

        // UPDATE KOLOM GAMBAR
        $stmt2 = $konek->prepare("
            UPDATE rombongan_payment SET img_payment=?
            WHERE id=?");

        $stmt2->bind_param("ss", $pathDB, $payment_id);
        $stmt2->execute();
        $newData = [
            "payment_id"     => $payment_id,
            "rombongan_id"   => $idPay,
            "rombongan_name" => $instansi,
            "pic"            => $pic,
            "sales"          => $sales,
            "jenis"          => $jenis,
            "metode"         => $metode,
            "date_pay"       => $tgl_pay,
            "price"          => $price,
            "img_payment"    => $pathDB
        ];

        logActivity(
            $konek,
            211,                    // ganti dengan $_SESSION['user_id'] jika ada
            "Insert Pembayaran",
            "rombongan_payment",
            $idPay,
            '',
            json_encode($newData)
        );
        echo json_encode([
            "status"=>"success",
        ]);
        exit;
    }
    //akhir payment rombongan
    //update payment rombongan
    if (isset($_POST['aksi']) && $_POST['aksi'] === 'update_payment') {
        try {
            $id = $_POST['paymentId'] ?? '';
            if ($id=='') throw new Exception("payment_id kosong");
            $id = mysqli_real_escape_string($konek,$id);
            $old = $konek->query("SELECT * FROM rombongan_payment WHERE id='$id'")
                        ->fetch_assoc();

            if (!$old) throw new Exception("Data tidak ditemukan");

            $jenis   = trim($_POST['up_jenis'] ?? '');
            $metode  = trim($_POST['up_metode'] ?? '');
            $price   = $_POST['up_price'] ?? '';
            $tgl_pay = trim($_POST['up_tgl_pay'] ?? '');

            $price = preg_replace('/[^0-9.]/','',$price);

            // VALIDASI: PRICE CHANGE → WAJIB GAMBAR BARU
            $priceLama = (float)$old['price'];
            $priceBaru = (float)$price;

            if ($priceBaru != $priceLama && empty($_FILES['up_imgPay']['name'])) {
                echo json_encode([
                    "status"  => "cek",
                    "message" => "cek lagi"
                ]);
                exit;
            }

            $fields=[];
            $params=[];
            $types="";

            $addField = function(&$fields,&$params,&$types,$name,$value){
                $fields[]="$name=?";
                $params[]=$value;
                $types.="s";
            };

            if ($jenis !== trim($old['jenis']))
                $addField($fields,$params,$types,'jenis',$jenis);

            if ($metode !== trim($old['metode']))
                $addField($fields,$params,$types,'metode',$metode);

            if ($priceBaru != $priceLama)
                $addField($fields,$params,$types,'price',$priceBaru);

            // if ($tgl_pay !== $old['date_pay'])
            //     $addField($fields,$params,$types,'date_pay',$tgl_pay);
            $tglOld = substr($old['date_pay'],0,10);

            if ($tgl_pay !== $tglOld)
                $addField($fields,$params,$types,'date_pay',$tgl_pay);

            // ===== HANDLE IMAGE =====
            if (!empty($_FILES['up_imgPay']['name'])) {

                $ext = strtolower(pathinfo($_FILES['up_imgPay']['name'],PATHINFO_EXTENSION));
                if (!in_array($ext,['jpg','jpeg','png']))
                    throw new Exception("Format gambar salah");

                if ($_FILES['up_imgPay']['size'] > 2*1024*1024)
                    throw new Exception("Gambar > 2MB");

                if ($old['img_payment'] && file_exists("../".$old['img_payment'])) {
                    $info = pathinfo("../".$old['img_payment']);
                    $renameOld =
                        $info['dirname'].'/'.
                        $info['filename'].'_old_'.time().'.'.$info['extension'];

                    rename("../".$old['img_payment'],$renameOld);
                }

                $newName = "pay_{$id}_{$old['rombongan_id']}_{$jenis}_".time().".$ext";
                $uploadPath = "../img/payments/".$newName;
                $dbPath = "img/payments/".$newName;

                if (!move_uploaded_file($_FILES['up_imgPay']['tmp_name'],$uploadPath))
                    throw new Exception("Upload gagal");

                $addField($fields,$params,$types,'img_payment',$dbPath);
            }

            if (empty($fields)) {
                echo json_encode(["status"=>"nochange"]);
                exit;
            }

            $sql = "UPDATE rombongan_payment SET ".implode(',',$fields)." WHERE id=?";
            $types.="s";
            $params[]=$id;

            $stmt = $konek->prepare($sql);
            if (!$stmt) throw new Exception($konek->error);

            $stmt->bind_param($types,...$params);
            if (!$stmt->execute())
                throw new Exception($stmt->error);

            $new = $konek->query("SELECT * FROM rombongan_payment WHERE id='$id'")
                        ->fetch_assoc();

            logActivity(
                $konek,
                211,
                "update",
                "rombongan_payment",
                $id,
                json_encode($old),
                json_encode($new)
            );

            echo json_encode(["status"=>"success"]);
            exit;

        } catch (Throwable $e) {

            echo json_encode([
                "status"=>"error",
                "message"=>$e->getMessage()
            ]);
            exit;
        }
    }
    //akhir update payment rombongan

    if (isset($_POST['aksi']) && $_POST['aksi'] === 'getDetailRombongan') {
        $client_id = mysqli_real_escape_string($konek, $_POST['client_id']);
        $detailMaster = getRombonganOk($konek, $client_id);
        $detailBudget = getViewBudgeting($konek, $client_id);

        echo json_encode([
            "status" => "success",
            "master" => $detailMaster[0] ?? null,
            "budget" => $detailBudget
        ]);
        exit;
    }
}

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
                $newData = [
                "group_head"   => $kategori,
                "group_detail" => $nama,
                "stok"         => $jumlah,
            ];

            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                211,
                "add",                    // jenis aksi
                "markom_service",               // nama tabel
                $kategori . '_' . $nama,                   // ID data
                '',                     // old_value (karena INSERT)
                json_encode($newData)     // new_value
            );
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

        $oldData = json_encode($cek);

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
            $dataNew = $konek->prepare("SELECT group_head, group_detail, stok
                                        FROM markom_service WHERE id_markom = ?");
            $dataNew->bind_param("i", $id_markom);
            $dataNew->execute();
            $resultNew = $dataNew->get_result();
            $newRow = $resultNew->fetch_assoc();
            $dataNew->close();

            $newData = json_encode($newRow);
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update",                    // jenis aksi
                "markom_service",               // nama tabel
                $kategori . '_' . $nama,                   // ID data
                $oldData,                     // old_value (karena UPDATE)
                $newData,                   // new_value
            );      

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
        $kategori = sanitize_text($_POST['ket']);

        $stmt = $konek->prepare("INSERT INTO vendor(kode_vendor, nama_vendor, pic, noTlp, kategori)
                                VALUES(?,?,?,?,?)");
        $stmt->bind_param("sssss", $kdVendor, $nama, $pic, $telephone, $kategori);
        if($stmt->execute()){
            $newData = [
                "kode_vendor"   => $kdVendor,
                "vendor_name" => $nama,
                "vendor_pic"  => $pic,
                "phone"       => $telephone,
                "kategori"    => $kategori
            ];

            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                211,
                "add",                    // jenis aksi
                "vendor",               // nama tabel
                $kdVendor,                   // ID data
                '',                     // old_value (karena INSERT)
                json_encode($newData)     // new_value
            );
            
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
        $kategori = sanitize_text($_POST['up_ket']);

        $stmt_cek = $konek->prepare("SELECT nama_vendor, pic, noTlp, kategori FROM vendor WHERE kode_vendor = ?");
        $stmt_cek->bind_param("s", $kode);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        $oldData = json_encode($cek);

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['nama_vendor'] === $namaVen &&
            $cek['pic'] === $pic &&
            $cek['noTlp'] === $noTlp &&
            $cek['kategori'] === $kategori
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE vendor SET nama_vendor = ?, pic =?, noTlp = ?, kategori = ? WHERE kode_vendor = ?");
        $stmt_update->bind_param("sssss", $namaVen, $pic, $noTlp, $kategori, $kode);
        if($stmt_update->execute()){
            $newData = [
                "nama_vendor" => $namaVen,
                "pic" => $pic,
                "noTlp" => $noTlp,
                "kategori" => $kategori
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update",                    // jenis aksi
                "vendor",               // nama tabel
                $kode,                   // ID data
                $oldData,                     // old_value (karena UPDATE)
                json_encode($newData),     // new_value
            );
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
        $kategori = sanitize_text($_POST['ktgr']);
        $vendor = sanitize_text($_POST['vendorName']);
        $fasilitas = sanitize_text($_POST['fasilitasName']);

        $stmt = $konek->prepare("INSERT INTO vendor_service(vendor_head, vendor_name, vendor_detail)
                                VALUES(?,?,?)");
        $stmt->bind_param("sss", $kategori, $vendor, $fasilitas);
        if($stmt->execute()){
            $newData = [
                "vendor_head"   => $kategori,
                "vendor_name"   => $vendor,
                "vendor_detail" => $fasilitas,
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add",                    // jenis aksi
                "vendor_service",               // nama tabel
                $vendor.'_'.$fasilitas,                   // ID data (null karena insert)
                '',                     // old_value (null karena insert)
                json_encode($newData)     // new_value
            );
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
        $ket = sanitize_text($_POST['up_ktgr']);
        $vendor = sanitize_text($_POST['up_vendorName']);
        $namafas = sanitize_text($_POST['up_fasilitasName']);

        $stmt_cek = $konek->prepare("SELECT vendor_head, vendor_name, vendor_detail FROM vendor_service WHERE id_vendor = ?");
        $stmt_cek->bind_param("i", $id_vend);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        $oldData = json_encode($cek);

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        if(
            $cek['vendor_head'] === $ket &&
            $cek['vendor_name'] === $vendor &&
            $cek['vendor_detail'] === $namafas
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        $stmt_update = $konek->prepare("UPDATE vendor_service SET vendor_head = ?, vendor_name = ?, vendor_detail = ? WHERE id_vendor = ?");
        $stmt_update->bind_param("sssi", $ket, $vendor, $namafas, $id_vend);
        if($stmt_update->execute()){
            $newData = [
                "vendor_head"   => $ket,
                "vendor_name"   => $vendor,
                "vendor_detail" => $namafas,
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update",                    // jenis aksi
                "vendor_service",               // nama tabel
                $id_vend.'_'.$namafas,         // ID data (null karena insert)
                $oldData,                      // old_value (karena UPDATE)
                json_encode($newData)       // new_value
            );
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("update data gagal: " . $stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
        exit;
    }
}

//input data fasilitas ke rombongan detail
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    //query khusus
    if($_POST['aksi'] === 'getView_fasilitas'){

        // $fasil = sanitize_text($_POST['fasilitas_id']);
        $fasil = $_POST['fasilitas_id'] ??'';

        if(!empty($fasil)){
            $stmt = $konek->prepare("SELECT data_id, client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, spec, catatan
            FROM rombongan_detail WHERE fasilitas_id = ? and del_status = 0 and point = 1");

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
        $pairToken = uniqid('FK_');

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $fasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas "' . $fasilitas . '" sudah ada di daftar rombongan ini!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?)");
        $konek->begin_transaction();
        try {
            foreach ([1,0] as $point) {
                $stmt->bind_param("sssiisssis", $headFs, $idClient, $fasilitas, $qty, $harga, $tanggal_input, $sales, $nameClient, $point, $pairToken);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }
            }
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_id"     => $idClient,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $qty,
                "price"            => $harga,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "client_name"      => $nameClient,
                "pair_token"       => $pairToken,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add fasilitas WK",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
        ]);
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

        $stmt_cek = $konek->prepare("SELECT group_fasilitas, fasilitas_id, fasilitas_name, qty, price, pair_token, price_vend
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

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

        if(
            $cek['group_fasilitas'] === $headFs &&
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['qty'] === $qty &&
            $cek['price'] === $harga
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET group_fasilitas = ?, fasilitas_name = ?, qty = ?, price = ?, using_date = ?, employee_name = ? WHERE pair_token = ?");
        $konek->begin_transaction();
        try {
            $stmt_update->bind_param("ssiisss", $headFs, $fasilitas, $qty, $harga, $tanggal_input, $sales, $pairToken);
            if (!$stmt_update->execute()) {
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $qty,
                "price"            => $harga,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "mode"              => "Update by pair_token: $pairToken"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update fasilitas WK",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $kode,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            error_log("Update FAIL: ".$e->getMessage());

            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
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
        $pairToken = uniqid('FV_');

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE client_id = ? AND fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("sss", $vendorName, $idClient, $VenFasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas ' . $VenFasilitas . ' dari vendor ' . $vendorName . ' sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, using_date, employee_name, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $konek->begin_transaction();
        try{
            foreach([1,0] as $point){
                $stmt->bind_param("ssssiiisssis", $vendorName, $vendor, $idClient, $VenFasilitas, $qty, $hargaJual, $hargaVend, $tanggal_input, $sales, $nameClient, $point, $pairToken);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $newData = [
                "client_id"         => $vendorName,
                "group_fasilitas"   => $vendor,
                "fasilitas_id"     => $idClient,
                "fasilitas_name"   => $VenFasilitas,
                "qty"              => $qty,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "client_name"      => $nameClient,
                "pair_token"       => $pairToken,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add fasilitas Vendor",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e){
            $konek->rollback();
            error_log("INSERT Fail: ".$e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message'=>$e->getMessage()
        ]);
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

        $stmt_cek = $konek->prepare("SELECT client_id, fasilitas_name, qty, price, pair_token, price_vend
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

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

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
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET client_id = ?, fasilitas_name = ?, qty = ?, price = ?, price_vend =?, using_date = ?, employee_name = ? WHERE pair_token = ?");
        $konek->begin_transaction();
        try{
            $stmt_update->bind_param("ssiiisss", $vendor, $fasilitas, $jumlah, $hargaJual, $hargaVend, $tanggal_input, $sales, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "client_id"         => $vendorName,
                "fasilitas_name"   => $VenFasilitas,
                "qty"              => $qty,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "pair_token"       => $pairToken,
                "mode"              => "Dobel Update (point 1 & 0)"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "Update fasilitas Vendor",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message' =>$e->getMessage()
            ]);
        }
        $stmt_update->close();
        exit;
    }

    //FnB
    if($_POST['aksi'] === 'tambahFnB'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $vendorFnB = sanitize_text($_POST['fnbVendor']);
        $menu = sanitize_text($_POST['fnbHead']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $harga = sanitize_text($_POST['hargaFnB']);
        $detail = sanitize_text($_POST['ket']);
        $vendor = 'food and beverages';
        $tanggal = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';
        $pairToken = uniqid('FnB_');

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $menu);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Menu ' . $menu . ' sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, spec, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
        $konek->begin_transaction();
        try{
            foreach ([1,0] as $point){
                $stmt->bind_param("ssssiissssis", $vendorFnB, $vendor, $idClient, $menu, $jumlah, $harga, $tanggal, $sales, $detail, $clientName, $point, $pairToken);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $newData = [
                "client_id"       => $vendorFnB,
                "group_fasilitas" => $vendor,
                "fasilitas_id"    => $idClient,
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "using_date"      => $tanggal,
                "employee_name"   => $sales,
                "spec"            => $detail,
                "client_name"     => $clientName
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add FnB",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message'=>$e->getMessage()
            ]);
            exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'updateFnB'){
        $id = sanitize_text($_POST['idFnB']);
        $menu = sanitize_text($_POST['up_fnbHead']);
        $jumlah = sanitize_text($_POST['up_jumlah']);
        $harga = sanitize_text($_POST['up_hargaFnB']);
        $keterangan = sanitize_text($_POST['up_ket']);

        $stmt_cek = $konek->prepare("SELECT fasilitas_name, qty, price, spec
                                    FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $id);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

        if(
            $cek['fasilitas_name'] === $menu &&
            $cek['qty'] === $jumlah &&
            $cek['price'] === $harga &&
            $cek['spec'] === $keterangan
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name =?, qty = ?, price = ?, spec = ?
                                        WHERE pair_token = ?");
        $konek->begin_transaction();
        try{
            $stmt_update->bind_param("siiss", $menu, $jumlah, $harga, $keterangan, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "spec"            => $keterangan
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update FnB",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $id,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
            exit;
        }
        $stmt_update->close();
        exit;
    }

    //cabana and cabin
    if($_POST['aksi'] === 'tambah_cabanaAndcabin'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $fsHead = 'cabana and cabin';
        $namaFasilitas = sanitize_text($_POST['fcnc']);
        $pemakai = sanitize_text($_POST['nPeng']);
        $tanggal = date("Y-m-d H:i:s");
        $sales = 'Noer Halimah';
        $pairToken = uniqid('CnC_');

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $namaFasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas ini sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, using_date, employee_name, client_name, catatan, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?)");
        $konek->begin_transaction();
        try{
            foreach ([1,0] as $point){
                $stmt->bind_param("sssssssis", $fsHead, $idClient, $namaFasilitas, $tanggal, $sales, $clientName, $pemakai, $point, $pairToken);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $newData = [
                "group_fasilitas" => $fsHead,
                "fasilitas_id"    => $idClient,
                "fasilitas_name"  => $namaFasilitas,
                "using_date"      => $tanggal,
                "employee_name"   => $sales,
                "client_name"     => $clientName,
                "catatan"         => $pemakai
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],
                212,
                "add CnC",
                "rombongan_detail",
                $idClient,
                '',
                json_encode($newData)
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message' =>$e->getMessage()
            ]);
            exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'update_cabanaAndcabin'){
        $idCnC = ($_POST['cncId']);
        $pengguna = sanitize_text($_POST['up_nPeng']);
        $fasilitas = sanitize_text($_POST['up_fcnc']);

        $stmt_cek = $konek->prepare("SELECT fasilitas_name, catatan, pair_token FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $idCnC);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

        if(
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['catatan'] === $pengguna
        ){
            echo json_encode(['sttus' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name = ?, catatan = ? WHERE pair_token = ?");
        $konek->begin_transaction();
        try{
            $stmt_update->bind_param("sss", $fasilitas, $pengguna, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "fasilitas_name"  => $fasilitas,
                "catatan"         => $pengguna,
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update CnC",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idCnC,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message' =>$e->getMessage()
            ]);
            exit();
        }
        $stmt_update->close();
        exit;
    }

}

//input data fasilitas ke rombongan detail Final
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'getView_fasilitasP'){
        // $fasil = sanitize_text($_POST['fasilitas_id']);
        $fasil = $_POST['fasilitas_id'] ??'';

        if(!empty($fasil)){
            $stmt = $konek->prepare("SELECT data_id, client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, spec, catatan
            FROM rombongan_detail WHERE fasilitas_id = ? and del_status = 0 and point = 0");

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
    if($_POST['aksi'] === 'tambah_fasilitasWKP'){
        $idClient = sanitize_text($_POST['cId']);
        $nameClient = sanitize_text($_POST['cName']);
        $headFs = sanitize_text($_POST['kategori']);
        $fasilitas = sanitize_text($_POST['fasilitas']);
        $qty = sanitize_text($_POST['qty']);
        $harga = sanitize_text($_POST['hargaWk']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';
        $pairToken = uniqid('FK_');
        $point = 0;

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $fasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas "' . $fasilitas . '" sudah ada di daftar rombongan ini!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        try {
        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?)");
            $konek->begin_transaction();
            $stmt->bind_param("sssiisssis", $headFs, $idClient, $fasilitas, $qty, $harga, $tanggal_input, $sales, $nameClient, $point, $pairToken);
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_id"     => $idClient,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $qty,
                "price"            => $harga,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "client_name"      => $nameClient,
                "pair_token"       => $pairToken,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add fasilitas WK",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
        ]);
        exit;
        }
        $stmt->close();
        exit;
    }

    //update data fasilitas ke rombongan
    if($_POST['aksi'] === 'update_fasilitasWKP'){
        $kode = sanitize_text($_POST['idf']);
        $headFs = sanitize_text($_POST['up_kategori']);
        $fasilitas = sanitize_text($_POST['up_fsl']);
        $qty = sanitize_text($_POST['up_qty']);
        $harga = sanitize_text($_POST['up_hargaWk']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt_cek = $konek->prepare("SELECT group_fasilitas, fasilitas_id, fasilitas_name, qty, price, pair_token, price_vend
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

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

        if(
            $cek['group_fasilitas'] === $headFs &&
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['qty'] === $qty &&
            $cek['price'] === $harga
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET group_fasilitas = ?, fasilitas_name = ?, qty = ?, price = ?, using_date = ?, employee_name = ? WHERE data_id = ? AND point = 0");
        $konek->begin_transaction();
        try {
            $stmt_update->bind_param("ssiissi", $headFs, $fasilitas, $qty, $harga, $tanggal_input, $sales, $kode);
            if (!$stmt_update->execute()) {
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_name"    => $fasilitas,
                "qty"               => $qty,
                "price"             => $harga,
                "using_date"        => $tanggal_input,
                "employee_name"     => $sales,
                "mode"              => "Update pair_token: $pairToken but by data_id and point=0"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update fasilitas WK",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $kode,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            error_log("Update FAIL: ".$e->getMessage());

            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
            exit;
        }
        $stmt_update->close();
        exit;
    }

    //tambah fasilitas vendor ke rombongan
    if($_POST['aksi'] === 'tambah_fasilitasVendP'){
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
        $pairToken = uniqid('FV_');
        $point = 0;

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE client_id = ? AND fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("sss", $vendorName, $idClient, $VenFasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas ' . $VenFasilitas . ' dari vendor ' . $vendorName . ' sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, using_date, employee_name, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssiiisssis", $vendorName, $vendor, $idClient, $VenFasilitas, $qty, $hargaJual, $hargaVend, $tanggal_input, $sales, $nameClient, $point, $pairToken);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $newData = [
                "client_id"         => $vendorName,
                "group_fasilitas"   => $vendor,
                "fasilitas_id"     => $idClient,
                "fasilitas_name"   => $VenFasilitas,
                "qty"              => $qty,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "client_name"      => $nameClient,
                "pair_token"       => $pairToken,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add fasilitas Vendor",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e){
            $konek->rollback();
            error_log("INSERT Fail: ".$e->getMessage());

        echo json_encode([
            'status' => 'error',
            'message'=>$e->getMessage()
        ]);
        exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'update_fasilitasVendP'){
        $idFas = sanitize_text($_POST['idFv']);
        $vendor = sanitize_text($_POST['up_vendorHead']);
        $fasilitas = sanitize_text($_POST['up_namaFasilitas']);
        $jumlah = sanitize_text(($_POST['up_qtyV']));
        $hargaJual = sanitize_text(($_POST['up_harga']));
        $hargaVend = sanitize_text($_POST['up_hargaVend']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt_cek = $konek->prepare("SELECT client_id, fasilitas_name, qty, price, pair_token, price_vend
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

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

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
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET client_id = ?, fasilitas_name = ?, qty = ?, price = ?, price_vend =?, using_date = ?, employee_name = ? WHERE data_id = ?");
            $stmt_update->bind_param("ssiiissi", $vendor, $fasilitas, $jumlah, $hargaJual, $hargaVend, $tanggal_input, $sales, $idFas);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "client_id"         => $vendor,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $jumlah,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "pair_token"       => $pairToken,
                "mode"              => "Update final Budgeting"
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "Update fasilitas Vendor",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idFas,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message' =>$e->getMessage()
            ]);
        }
        $stmt_update->close();
        exit;
    }

    //FnB
    if($_POST['aksi'] === 'tambahFnBP'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $vendorFnB = sanitize_text($_POST['fnbVendor']);
        $menu = sanitize_text($_POST['fnbHead']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $harga = sanitize_text($_POST['hargaFnB']);
        $detail = sanitize_text($_POST['ket']);
        $vendor = 'food and beverages';
        $tanggal = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';
        $pairToken = uniqid('FnB_');
        $point = 0;

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $menu);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Menu ' . $menu . ' sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, spec, client_name, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssiissssis", $vendorFnB, $vendor, $idClient, $menu, $jumlah, $harga, $tanggal, $sales, $detail, $clientName, $point, $pairToken);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $newData = [
                "client_id"       => $vendorFnB,
                "group_fasilitas" => $vendor,
                "fasilitas_id"    => $idClient,
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "using_date"      => $tanggal,
                "employee_name"   => $sales,
                "spec"            => $detail,
                "client_name"     => $clientName
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "add FnB",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idClient,         // ID data (null karena insert)
                '',                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message'=>$e->getMessage()
            ]);
            exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'updateFnBP'){
        $id = sanitize_text($_POST['idFnB']);
        $menu = sanitize_text($_POST['up_fnbHead']);
        $jumlah = sanitize_text($_POST['up_jumlah']);
        $harga = sanitize_text($_POST['up_hargaFnB']);
        $keterangan = sanitize_text($_POST['up_ket']);

        $stmt_cek = $konek->prepare("SELECT fasilitas_name, qty, price, spec, pair_token
                                    FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $id);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }

        $pairToken = $cek['pair_token'];
        $oldData = json_encode($cek);

        if(
            $cek['fasilitas_name'] === $menu &&
            $cek['qty'] === $jumlah &&
            $cek['price'] === $harga &&
            $cek['spec'] === $keterangan
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try{
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name =?, qty = ?, price = ?, spec = ?
                                            WHERE data_id = ?");
            $konek->begin_transaction();
            $stmt_update->bind_param("siisi", $menu, $jumlah, $harga, $keterangan, $id);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "spec"            => $keterangan
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update FnB",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $id,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message'=>$e->getMessage()
            ]);
            exit;
        }
        $stmt_update->close();
        exit;
    }

    //cabana and cabin
    if($_POST['aksi'] === 'tambah_cabanaAndcabinP'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $fsHead = 'cabana and cabin';
        $namaFasilitas = sanitize_text($_POST['fcnc']);
        $pemakai = sanitize_text($_POST['nPeng']);
        $tanggal = date("Y-m-d H:i:s");
        $sales = 'Noer Halimah';
        $pairToken = uniqid('CnC_');
        $point = 0;

        $checkStmt = $konek->prepare("SELECT data_id FROM rombongan_detail WHERE fasilitas_id = ? AND fasilitas_name = ? AND del_status = 0");
        $checkStmt->bind_param("ss", $idClient, $namaFasilitas);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            // Jika data sudah ada, kirim respon 'exists'
            echo json_encode([
                'status' => 'exists', 
                'message' => 'Fasilitas ini sudah ada di daftar rombongan!'
            ]);
            $checkStmt->close();
            exit;
        }
        $checkStmt->close();

        try{
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, using_date, employee_name, client_name, catatan, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?)");
            $konek->begin_transaction();
            $stmt->bind_param("sssssssis", $fsHead, $idClient, $namaFasilitas, $tanggal, $sales, $clientName, $pemakai, $point, $pairToken);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $newData = [
                "group_fasilitas" => $fsHead,
                "fasilitas_id"    => $idClient,
                "fasilitas_name"  => $namaFasilitas,
                "using_date"      => $tanggal,
                "employee_name"   => $sales,
                "client_name"     => $clientName,
                "catatan"         => $pemakai
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],
                212,
                "add CnC",
                "rombongan_detail",
                $idClient,
                '',
                json_encode($newData)
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status' => 'error',
                'message' =>$e->getMessage()
            ]);
            exit;
        }
        $stmt->close();
        exit;
    }

    if($_POST['aksi'] === 'update_cabanaAndcabinP'){
        $idCnC = ($_POST['cncId']);
        $pengguna = sanitize_text($_POST['up_nPeng']);
        $fasilitas = sanitize_text($_POST['up_fcnc']);

        $stmt_cek = $konek->prepare("SELECT fasilitas_name, catatan FROM rombongan_detail WHERE data_id = ?");
        $stmt_cek->bind_param("i", $idCnC);
        $stmt_cek->execute();
        $result_cek = $stmt_cek->get_result();
        $cek = $result_cek->fetch_assoc();
        $stmt_cek->close();

        if(!$cek){
            echo json_encode(['status' => 'error']);
            exit;
        }
        $oldData = json_encode($cek);

        if(
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['catatan'] === $pengguna
        ){
            echo json_encode(['sttus' => 'nochange']);
            exit;
        }
        $konek->begin_transaction();
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name = ?, catatan = ? WHERE data_id = ?");
        try{
            $stmt_update->bind_param("ssi", $fasilitas, $pengguna, $idCnC);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $newData = [
                "fasilitas_name"  => $fasilitas,
                "catatan"         => $pengguna,
            ];
            logActivity(
                $konek,
                // $_SESSION['user_id'],     // id user yang sedang login
                212,
                "update CnC",                    // jenis aksi
                "rombongan_detail",               // nama tabel
                $idCnC,         // ID data (null karena insert)
                $oldData,                      // old_value (karena INSERT)
                json_encode($newData)       // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode([
                'status'=>'error',
                'message' =>$e->getMessage()
            ]);
            exit();
        }
        $stmt_update->close();
        exit;
    }
}

// fungsi hapus
function softDelete($konek, $tabel, $kolom_id, $id_value) {
    $tabel = mysqli_real_escape_string($konek, $tabel);
    $kolom_id = mysqli_real_escape_string($konek, $kolom_id);
    $id_value = mysqli_real_escape_string($konek, $id_value);

    // CEK APAKAH TABEL PUNYA KOLOM pair_token
    $cekKolom = mysqli_query($konek,
        "SHOW COLUMNS FROM `$tabel` LIKE 'pair_token'"
    );

    $punyaPair = ($cekKolom && mysqli_num_rows($cekKolom) > 0);

    // DEFAULT QUERY
    $query = "UPDATE `$tabel` SET del_status = 1
              WHERE `$kolom_id` = '$id_value' AND del_status = 0";

    // JIKA ADA pair_token → DELETE PASANGAN
    if ($punyaPair) {
        $q = mysqli_query($konek,
            "SELECT pair_token FROM `$tabel`
             WHERE `$kolom_id` = '$id_value'
             LIMIT 1"
        );
        if ($q && $row = mysqli_fetch_assoc($q)) {
            $pair = $row['pair_token'] ?? '';
            if (!empty($pair)) {
                $pair = mysqli_real_escape_string($konek, $pair);
                $query = "UPDATE `$tabel`
                          SET del_status = 1
                          WHERE pair_token = '$pair'
                          AND del_status = 0";
            }
        }
    }
    // EXECUTE
    if (mysqli_query($konek, $query)) {
        return "success";
    } else {
        return mysqli_error($konek);
    }
    // Mengubah del_status menjadi 1
    // $query = "UPDATE $tabel SET del_status = 1 WHERE $kolom_id = '$id_value'";
    
    // if (mysqli_query($konek, $query)) {
    //     return "success";
    // } else {
    //     return mysqli_error($konek);
    // }
}


if (isset($_POST['aksi']) && $_POST['aksi'] === 'hapus_data_generik') {
    $tabel = $_POST['tabel'];
    $id = $_POST['id'];
    $kolom = $_POST['kolom'];
    $nama_item = $_POST['nama_item']; // Dikirim dari JS untuk keterangan log
    
    // 1. Jalankan Soft Delete
    $result = softDelete($konek, $tabel, $kolom, $id);

    if ($result === "success") {
        // 2. Ambil User ID dari Session (Sesuaikan dengan nama session Anda)
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
        
        // 3. Panggil Fungsi logActivity Anda
        logActivity(
            $konek, 
            // $user_id,
            211, 
            'DELETE', 
            $tabel, 
            $id, 
            "Fasilitas: $nama_item", // old_value
            "del_status: 1"           // new_value
        );
        
        echo "success";
    } else {
        echo $result;
    }
    exit;
}

function getAvailableFacilities($konek, $rombongan_id) {
    $sql = "
        SELECT f.facility_name
        FROM facility f
        WHERE f.fasilitas_id NOT IN (
            SELECT rd.fasilitas_name
            FROM rombongan_detail rd
            JOIN rombongan_master rm 
                ON rd.fasilitas_id = rm.client_id
            WHERE rm.date_plan = (
                SELECT date_plan
                FROM rombongan_master 
                WHERE client_id = ?
            )
        )
    ";

    $stmt = $konek->prepare($sql);
    if (!$stmt) {
        die('Prepare failed: ' . $konek->error);
    }
    $stmt->bind_param("i", $rombongan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $facilities = [];
    while ($row = $result->fetch_assoc()) {
        $facilities[] = $row;
    }

    $stmt->close();
    return $facilities;
}

function logActivity($konek, $user_id, $action, $table_name, $record_id, $old_value = null, $new_value = null){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $konek->prepare("INSERT INTO log_act
        (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("issssssss", 
        $user_id, 
        $action,
        $table_name,
        $record_id,
        $old_value,
        $new_value,
        $ip_address,
        $user_agent,
        $created_at
    );

    $stmt->execute();
    $stmt->close();
}



?>