<?php
require 'koneksi.php';

function sanitize_text($input, $strict = false) {
    $input = trim($input);
    if ($strict) {
        return preg_replace("/[^a-zA-Z0-9\s]/", "", $input);
    } else {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }
}


$cekSales = $konek->query("
    SELECT Employee_ID, name, departemen, divisi, tgl_mulai, checked
    FROM employee_card
    WHERE divisi = 'sales' AND inactive = 0
");

$sales = [];
while ($row = $cekSales->fetch_assoc()) {
    $row['checked'] = ($row['checked'] == 1) ? '1' : '0'; 
    $sales[] = $row;
}

//ambil data rombongan dari database
$cekAllRom = $konek->query("
    SELECT * FROM client
");
$allRom = [];
while ($row = $cekAllRom->fetch_assoc()){
    $allRom[] = $row;
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

// if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah_dataRombongan'){
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_dataRombongan'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $tgl_kunjungan = sanitize_text($_POST['tgl_kunjungan']);
        $gate = sanitize_text($_POST['gate']);
        $alamat = sanitize_text($_POST['alamat']);
        $marketing_id = '02-001';
        $marketing_name = 'eka';
        $remark = 'perusahaan';

        $stmt = $konek->prepare("INSERT INTO client(client_id, client_name, address, pic, phone, tgl_kunjungan, gate, marketing_id, marketing_name, remarks)
                                VALUES(?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssssss", $kode, $instansi, $alamat, $pic, $noTlp, $tgl_kunjungan, $gate, $marketing_id, $marketing_name, $remark);
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
        $gate = sanitize_text($_POST['gate']);
        $alamat = sanitize_text($_POST['alamat']);

        //ambil data
        $stmt_cek = $konek->prepare("SELECT client_name, address, pic, phone, tgl_kunjungan, gate FROM client WHERE client_id = ?");
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
            $cek['gate'] === $gate
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        //update dta
        $stmt_update = $konek->prepare("UPDATE client SET client_name = ?,
                        address = ?, pic = ?, phone = ?, tgl_kunjungan = ?, gate = ? WHERE client_id = ?");
        $stmt_update->bind_param("sssssss", $instansi, $alamat, $pic, $noTlp, $tglKunjungan, $gate, $kode);
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

?>