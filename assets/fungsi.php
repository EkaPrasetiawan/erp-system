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

// function getFasilitasWK($konek){
//     $dataFs = [];
//     $result =$konek->query("SELECT group_head, group_detail FROM markom_service WHERE group_head != 'Food and Beverages'");
//     if($result){
//         while($row = $result->fetch_assoc()){
//             $dataFs[] = $row;
//         }
//     } else {
//         error_log("error data tidak ditemukan: " . $konek->error);
//     }
//     return $dataFs;
// }

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

function getFnB($konek){
    $fnb = [];
    $result = $konek->query("SELECT group_head, group_detail FROM markom_service WHERE group_head = 'Food and Beverages'");
    if($result){
        while($row = $result->fetch_assoc()){
            $fnb[] = $row;
        }
    } else {
        error_log("Data tidak ada: ".$result->error);
    }
    return $fnb;
}

// function getCnc($konek){
//     $cnc = [];
//     $result = $konek->query("SELECT urut, facility_name FROM facility");
//     if($result){
//         while($row = $result->fetch_assoc())
//             $cnc[] = $row;
//     } else {
//         error_log("Data Kosong".$result->error);
//     }
//     return $cnc;
// }

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
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_client_id'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}
function getRombonganOk ($konek, $client_id){
    $rombonganOk = [];
    $result = $konek->query("SELECT date_input, date_plan, client_name, client_pic, phone, marketing, judul, jumlah_pax, hrg_tiket, client_id
                            FROM rombongan_master WHERE client_id = '$client_id'");
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
        $marketing_id = '02-001';
        $marketing_name = 'eka';
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

    //clear payment
    if($_POST['aksi'] === 'update_cp_rombongan'){
        $id_cpRom = sanitize_text($_POST['up_IDromCP']);
        $cp_amount = $_POST['up_cp'] ?? 0;
        //folder img
        // $target_dir = dirname(dirname(__DIR__)) . "../img/downPayment/";
        $target_dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "img" . DIRECTORY_SEPARATOR . "clearPayment" . DIRECTORY_SEPARATOR;
        $image_path = null;
        $uploaded_at = date('Y-m-d H:i:s'); // Catat waktu upload

            // ========== 1. Ambil data lama dulu ==========
        $qOld = $konek->prepare("SELECT clear_payment, img_cp, cp_uploaded_at FROM rombongan_master WHERE client_id = ?");
        $qOld->bind_param("s", $id_cpRom);
        $qOld->execute();
        $result = $qOld->get_result();
        $oldRow = $result->fetch_assoc();
        $qOld->close();

        $oldData = json_encode($oldRow); 

        $has_new_image = false;

        if (isset($_FILES['picCP']) && $_FILES['picCP']['error'] === UPLOAD_ERR_OK) {
            $has_new_image = true;
            $file_name = $_FILES['picCP']['name'];
            $file_tmp = $_FILES['picCP']['tmp_name'];
            $file_size = $_FILES['picCP']['size']; 
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png'];
        
            // Pengecekan Ekstensi dan Ukuran
            if (!in_array($file_ext, $allowed_ext)) {
                echo json_encode(['status' => 'error', 'message' => 'Format file tidak diizinkan. Hanya JPG, JPEG, PNG.']);
                exit();
            }
            if ($file_size > 2000000) { // 2MB
                echo json_encode(['status' => 'error', 'message' => 'Ukuran file melebihi batas 2MB.']);
                exit();
            }
            // Generate nama unik (misalnya: ID_ROMBONGAN_CP_timestamp.ext)
            $new_file_name = $id_cpRom . '_CP_' . time() . '.' . $file_ext;
            $target_file = $target_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $target_file)) {
                // Path relatif yang akan disimpan di database
                // Contoh: 'img/imgDP/ID_ROMBONGAN_DP_timestamp.jpg'
                $image_path = 'img/clearPayment/' . $new_file_name; 
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Gagal memindahkan file yang diupload.']);
                exit();
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'File gambar wajib diupload. Error: ' . $_FILES['picDP']['error']]);
            exit();
        }
        // Update Data ke Database
         if ($has_new_image) {
                $stmt_update = $konek->prepare("
                    UPDATE rombongan_master 
                    SET clear_payment = ?, img_cp = ?, cp_uploaded_at = ?
                    WHERE client_id = ?
                ");
                $stmt_update->bind_param("isss", $cp_amount, $image_path, $uploaded_at, $id_cpRom);
            } else {
                $stmt_update = $konek->prepare("
                    UPDATE rombongan_master 
                    SET clear_payment = ?
                    WHERE client_id = ?
                ");
                $stmt_update->bind_param("is", $cp_amount, $id_cpRom);
            }
    
        if ($stmt_update->execute()) {

            // ========== Ambil data baru ==========
            $qNew = $konek->prepare("SELECT clear_payment, img_cp, cp_uploaded_at
                                    FROM rombongan_master WHERE client_id = ?");
            $qNew->bind_param("s", $id_cpRom);
            $qNew->execute();
            $resultNew = $qNew->get_result();
            $newRow = $resultNew->fetch_assoc();
            $qNew->close();

            $newData = json_encode($newRow);

            // ========== Simpan history log ==========
            logActivity(
                $konek,
                112,
                "update clear payment",
                "rombongan_master",
                $id_cpRom,
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

//input data fasilitas ke rombongan detail
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    //query khusus
    if($_POST['aksi'] === 'getView_fasilitas'){

        // $fasil = sanitize_text($_POST['fasilitas_id']);
        $fasil = $_POST['fasilitas_id'] ??'';

        if(!empty($fasil)){
            $stmt = $konek->prepare("SELECT data_id, client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, spec, catatan
            FROM rombongan_detail WHERE fasilitas_id = ?");

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

    //FnB
    if($_POST['aksi'] === 'tambahFnB'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $fnb = sanitize_text($_POST['fnb']);
        $menu = sanitize_text($_POST['fnbHead']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $harga = sanitize_text($_POST['hargaFnB']);
        $detail = sanitize_text($_POST['ket']);
        $tanggal = date("Y-m-d H:i:s");
        $sales = 'Noer halimah';

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, spec, client_name)
                                VALUES(?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sssiissss", $fnb, $idClient, $menu, $jumlah, $harga, $tanggal, $sales, $detail, $clientName);
        if($stmt->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("gagal: ". $stmt->error);
            echo json_encode(['status' => 'error']);
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
                                        WHERE data_id = ?");
        $stmt_update->bind_param("siisi", $menu, $jumlah, $harga, $keterangan, $id);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("update data error: ".$stmt_update->error);
            echo json_encode(['status' => 'error']);
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

        $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, using_date, employee_name, client_name, catatan)
                                VALUES(?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss", $fsHead, $idClient, $namaFasilitas, $tanggal, $sales, $clientName, $pemakai);
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

    if($_POST['aksi'] === 'update_cabanaAndcabin'){
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
        if(
            $cek['fasilitas_name'] === $fasilitas &&
            $cek['catatan'] === $pengguna
        ){
            echo json_encode(['sttus' => 'nochange']);
            exit;
        }
        $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name = ?, catatan = ? WHERE data_id = ?");
        $stmt_update->bind_param("ssi", $fasilitas, $pengguna, $idCnC);
        if($stmt_update->execute()){
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            error_log("update data error: ".$stmt_update->error);
            echo json_encode(['status' => 'error']);
            exit;
        }
        $stmt_update->close();
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