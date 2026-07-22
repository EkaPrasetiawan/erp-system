<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

function getAllClient($konek) {
    $allClient = [];
    $cekAllCLient = $konek->query("SELECT * FROM client WHERE remarks = '$_SESSION[bagian]'");
    if ($cekAllCLient) { // untuk memeriksa queri berhasil atau tidak
        while ($row = $cekAllCLient->fetch_assoc()){
            $allClient[] = $row;
        }
    } else {
        error_log("Error fetching all client: " . $konek->error);
    }
    return $allClient;
}

function viewRombongan($konek){
    $allRom = [];
    $result = $konek->query("SELECT * FROM rombongan_master WHERE del_status = 0 AND (status != 2 AND status != 3) AND marketing = '$_SESSION[name]'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $allRom[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $allRom;
}

function viewRombongan2($konek){
    $allRom = [];
    $result = $konek->query("SELECT * FROM rombongan_master WHERE del_status = 0 AND marketing = '$_SESSION[name]'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $allRom[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $allRom;
}

//create code client
function generateKodeClient($konek) {
    $result = $konek->query("SELECT MAX(urut_client) AS max_id FROM client");
    $row = $result->fetch_assoc();
    $lastId = (int)$row['max_id'];

    $prefix = "CLI";
    $bulan  = date("m");
    $tahun  = date("y");
    $nextId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);

    return $prefix . $bulan . $tahun . $nextId;
}

$code = generateKodeClient($konek);

//create code client
function generateKodeRombongan($konek) {
    $result = $konek->query("SELECT MAX(data_id) AS max_id FROM rombongan_master");
    $row = $result->fetch_assoc();
    $lastId = (int)$row['max_id'];

    $prefix = "ROM";
    $bulan  = date("m");
    $tahun  = date("y");
    $nextId = str_pad($lastId + 1, 3, "0", STR_PAD_LEFT);

    return $prefix . $bulan . $tahun . $nextId;
}

$cdr = generateKodeRombongan($konek);

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

function getRombonganOk ($konek, $rombongan_id){
    $rombonganOk = [];
    $result = $konek->query("SELECT data_id, date_input, date_plan, client_name, client_pic, address, rombongan_id, phone, marketing, judul, jumlah_pax, hrg_tiket, oleh, clear_payment, dp_uploaded_at, cp_uploaded_at, rombongan_id, category
                            FROM rombongan_master WHERE rombongan_id = '$rombongan_id' AND del_status = 0");
    if($result){
        while ($row = $result->fetch_assoc()){
            $rombonganOk[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $rombonganOk;
}

function getFasilitasWK($konek, $date, $rombongan_id) {
    // Amankan input tanggal
    $safe_date = mysqli_real_escape_string($konek, $date);
    $safe_rombongan_id = mysqli_real_escape_string($konek, $rombongan_id);

    // Jika tanggal atau rombongan_id kosong, kembalikan array kosong
    if (empty($safe_date) || empty($safe_rombongan_id)) {
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
            AND rm.rombongan_id != '{$safe_rombongan_id}'
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

function getFasilitasWKP($konek, $date, $rombongan_id) {
    // Amankan input tanggal
    $safe_date = mysqli_real_escape_string($konek, $date);
    $safe_rombongan_id = mysqli_real_escape_string($konek, $rombongan_id);

    // Jika tanggal atau rombongan_id kosong, kembalikan array kosong
    if (empty($safe_date) || empty($safe_rombongan_id)) {
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
            AND rm.rombongan_id != '{$safe_rombongan_id}'
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

function getCnc($konek, $date, $rombongan_id) {
    // Amankan input tanggal
    $safe_date = mysqli_real_escape_string($konek, $date);
    $safe_rombongan_id = mysqli_real_escape_string($konek, $rombongan_id);

    // Jika tanggal atau rombongan_id kosong, kembalikan array kosong
    if (empty($safe_date) || empty($safe_rombongan_id)) {
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
            AND rm.rombongan_id != '{$safe_rombongan_id}'
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

function getViewBudgetingP ($konek, $rombongan_id){
    $sf_rombongan_id = mysqli_real_escape_string($konek, $rombongan_id);
    
    if(empty($sf_rombongan_id)){
        return[];
    }

    $viewBudgeting = [];
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_rombongan_id' AND del_status = 0 AND point = 0");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}

function viewPayment ($konek, $rombongan_id){
    $sfp_client_id = mysqli_real_escape_string($konek, $rombongan_id);

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

function getViewBudgetingFK ($konek, $client_id){
    $sf_client_id = mysqli_real_escape_string($konek, $client_id);
    
    if(empty($sf_client_id)){
        return[];
    }

    $viewBudgeting = [];
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_client_id'
                            AND del_status = 0 AND point = 1 AND group_fasilitas != 'cabana and cabin'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}

function getViewBudgetingFK2 ($konek, $client_id){
    $sf_client_id = mysqli_real_escape_string($konek, $client_id);
    
    if(empty($sf_client_id)){
        return[];
    }

    $viewBudgeting = [];
    $result = $konek->query("SELECT * FROM rombongan_detail WHERE fasilitas_id = '$sf_client_id'
                            AND del_status = 0 AND point = 0 AND group_fasilitas != 'cabana and cabin'");
    if($result){
        while ($row = $result->fetch_assoc()){
            $viewBudgeting[] = $row;
        }
    } else {
        error_log("error: " . $konek->error);
    }
    return $viewBudgeting;
}


//bagian tambah Client
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_dataClient'){
        $kode = sanitize_text($_POST['kode']);
        $instansi = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $alamat = sanitize_text($_POST['alamat']);
        $tanggal = date("Y-m-d H:i:s");
        $marketing_id = $_SESSION['Employee_ID'];
        $marketing_name = $_SESSION['name'];
        $remark = $_SESSION['bagian'];

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

//bagian rombongan
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'tambah_dataRombongan'){
        $kdRom = sanitize_text($_POST['kdRom']);
        $idClient = sanitize_text($_POST['id_cli']);
        $nama = sanitize_text($_POST['instansi']);
        $pic = sanitize_text($_POST['pic']);
        $noTlp = sanitize_text($_POST['noTlp']);
        $tanggal_plan = sanitize_text($_POST['tgl_dtng']);
        $gate = sanitize_text($_POST['gate']);
        $alamat = sanitize_text($_POST['alamat']);
        $jumlah = sanitize_text($_POST['pax']);
        $nominal = sanitize_text($_POST['harga']);
        $judul = sanitize_text($_POST['judul']);
        $jenis = sanitize_text($_POST['jenis']);
        $tgl_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
        $bagian = $_SESSION['bagian'];

        $htm = ($jenis === 'htm_only') ? 1 : 0;

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_master(client_id, client_name, rombongan_id, date_input, date_plan, client_pic, phone, address, jumlah_pax, marketing, gate_in, jenis, hrg_tiket, category, htm_only, judul)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssssssisssisis", $idClient, $nama, $kdRom, $tgl_input, $tanggal_plan, $pic, $noTlp, $alamat, $jumlah, $sales, $gate, $bagian, $nominal, $jenis, $htm, $judul);
            if(!$stmt->execute()){
                throw  new Exception($stmt->error);
            }

            $stmt->close();

            // ====== INSERT DETAIL (KHUSUS HTM ONLY) ======
            if($jenis === 'htm_only' && $htm == 1){

                $pair_token = 'FK_' . strtoupper(uniqid());
                $using_date = date("Y-m-d H:i:s");

                $group      = 'Tiket Masuk';
                $fasilitas  = 'Tiket masuk';
                $unit       = 'PAX';

                $stmtDetail = $konek->prepare("
                    INSERT INTO rombongan_detail
                    (group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name, point, pair_token, unit)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                if(!$stmtDetail){
                    throw new Exception($konek->error);
                }

                $stmtDetail->bind_param("sssissssiss", $group, $kdRom, $fasilitas, $jumlah, $nominal, $using_date, $sales, $nama, $point, $pair_token, $unit);

                // loop 2 baris (point 1 & 0)
                foreach ([1, 0] as $point) {
                    if(!$stmtDetail->execute()){
                        throw new Exception($stmtDetail->error);
                    }
                }
                $stmtDetail->close();
            }
            $newData = [
                "client_id"     => $idClient,
                "client_name"   => $nama,
                "rombongan_id"  => $kdRom,
                "client_pic"    => $pic,
                "phone"         => $noTlp,
                "marketing"     => $sales,
                "date_plan"     => $tanggal_plan,
                "gate_in"       => $gate,
                "alamat"        => $alamat,
                "category"      => $jenis,
                "judul"         => $judul,
                "jenis"         => $bagian
            ];

            logActivity(
                $konek,
                $_SESSION['Employee_ID'],     // id user yang sedang login
                "add data rombongan",                    // jenis aksi
                "rombongan_master",       // nama tabel
                $kdRom,                   // ID data
                '',                     // old_value (karena INSERT)
                json_encode($newData)     // new_value
            );
            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            error_log("Error Systen: ".$e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' =>$e->getMessage()
            ]);
            exit;
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
        $alamat = sanitize_text($_POST['upAlamat']);
        $judul = sanitize_text($_POST['up_judul']);
        $jenis = sanitize_text($_POST['up_jenis']);

        if ($jenis === 'htm_only') {
        $jumlah   = sanitize_text($_POST['up_pax']);
        $price    = sanitize_text($_POST['upHarga']);
        } else {
            $jumlah   = 0;
            $price    = 0;
        }

        // KEAMANAN: Validasi nilai drop-down Jenis (Server-side Whitelist)
        $allowed_jenis = ['htm_only', 'paket', 'custom'];
        if (!in_array($jenis, $allowed_jenis)) {
            echo json_encode(['status' => 'error', 'message' => 'Pilihan jenis tidak valid.']);
            exit;
        }

        $stmt_cek = $konek->prepare("SELECT rombongan_id, date_plan, address, jumlah_pax, gate_in, hrg_tiket, category, judul FROM rombongan_master WHERE rombongan_id = ?");
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
        if($oldData['address'] != $alamat)
            $changes['address'] = ['old'=>$oldData['address'], 'new'=>$alamat];
        if($oldData['gate_in'] != $gate)
            $changes['gate_in'] = ['old'=>$oldData['gate_in'], 'new'=>$gate];
        if($oldData['category'] != $jenis)
            $changes['category'] = ['old'=>$oldData['category'], 'new'=>$jenis];
        if($oldData['judul'] != $judul)
            $changes['judul'] = ['old'=>$oldData['judul'], 'new'=>$judul];
        if($oldData['jumlah_pax'] != $jumlah)
            $changes['jumlah_pax'] = ['old'=>$oldData['jumlah_pax'], 'new'=>$jumlah];
        if($oldData['hrg_tiket'] != $price)
            $changes['hrg_tiket'] = ['old'=>$oldData['hrg_tiket'], 'new'=>$price];

        if(empty($changes)){
            echo json_encode(['status' => 'nochange']);
            exit;
        }

        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_master SET date_plan = ?, address = ?, jumlah_pax = ?, gate_in = ?, hrg_tiket = ?, category = ?, judul = ? WHERE rombongan_id = ?");
            $stmt_update->bind_param("ssisisss", $tanggal, $alamat, $jumlah, $gate, $price, $jenis, $judul, $idRom);

            if(!$stmt_update->execute()){
                throw new Exception ($stmt_update->error);
            }
            $stmt_update->close();

            // update jika jenis tereteksi perubahan pada jumlah dan harga
            if($jenis === 'htm_only'){
                if(isset($changes['jumlah_pax']) || isset($changes['hrg_tiket'])){

                $stmt_detail_update = $konek->prepare("UPDATE rombongan_detail SET qty = ?, price = ? WHERE fasilitas_id = ?");
                if(!$stmt_detail_update){
                    throw new Exception($konek->error);
                }

                $stmt_detail_update->bind_param("iis", $jumlah, $price, $idRom);
                
                if(!$stmt_detail_update->execute()){
                    throw new Exception($stmt_detail_update->error);
                }
                $stmt_detail_update->close();

                // Catat informasi tambahan pada array logs untuk audit trail
                $changes['rombongan_detail_sync'] = ['status' => 'updated', 'qty' => $jumlah, 'price' => $price];
                }
            }
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
                "update data rombongan",
                "rombongan_master",
                $idRom,
                json_encode($oldData),
                json_encode($changes)
            );

            $konek->commit();
            echo json_encode(['status' => 'success']);
            exit;
        } catch (Exception $e) {
            $konek->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Update failed.']);
            exit;
        }
    }
}

//bagian payment
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah_payment') {
        $idPay    = mysqli_real_escape_string($konek, $_POST['idPay']);
        $instansi = mysqli_real_escape_string($konek, $_POST['instansi']);
        $pic      = mysqli_real_escape_string($konek, $_POST['picPay']);
        $jenis    = mysqli_real_escape_string($konek, $_POST['jenis']);
        $metode   = mysqli_real_escape_string($konek, $_POST['metode']);
        $tgl_pay  = mysqli_real_escape_string($konek, $_POST['tgl_pay']);
        $price    = mysqli_real_escape_string($konek, $_POST['price']);
        $tgl_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];

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
            $_SESSION['Employee_ID'],                    // ganti dengan $_SESSION['user_id'] jika ada
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
                $_SESSION['Employee_ID'],
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

    if ($_POST['aksi'] === 'update_status') {
        $id = $_POST['id'];
        $status = $_POST['status'];
    
        // mapping ke angka
        $statusMap = [
            "open" => 0,
            "on process" => 1,
            "done" => 2,
            "batal" => 3
        ];
        $statusAngka = $statusMap[$status] ?? 0;
        $stmt = $konek->prepare("UPDATE rombongan_master SET status=? WHERE rombongan_id=?");
        $stmt->bind_param("is", $statusAngka, $id);
    
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Status berhasil diupdate"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Gagal update"
            ]);
        }
    }
}
//akhir bagian payment rombongan

//bagian rombongn Request
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
        $satuan = sanitize_text($_POST['satuan']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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

        try {
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name, point, pair_token, unit)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            foreach ([1,0] as $point) {
                $stmt->bind_param("sssiisssiss", $headFs, $idClient, $fasilitas, $qty, $harga, $tanggal_input, $sales, $nameClient, $point, $pairToken, $satuan);
                if (!$stmt->execute()) {
                    throw new Exception($stmt->error);
                }
            }
            $stmt->close();
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
                "unit"             => $satuan,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],     // id user yang sedang login
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
    }

    //update data fasilitas ke rombongan
    if($_POST['aksi'] === 'update_fasilitasWK'){
        $kode = sanitize_text($_POST['idf']);
        $headFs = sanitize_text($_POST['up_kategori']);
        $fasilitas = sanitize_text($_POST['up_fsl']);
        $qty = sanitize_text($_POST['up_qty']);
        $harga = sanitize_text($_POST['up_hargaWk']);
        $tanggal_input = date("Y-m-d H:i:s");
        $unit = sanitize_text($_POST['up_satuan']);
        $sales = $_SESSION['name'];

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
            $cek['group_fasilitas'] == $headFs &&
            $cek['fasilitas_name'] == $fasilitas &&
            $cek['qty'] == $qty &&
            $cek['price'] == $harga
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try {
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET group_fasilitas = ?, fasilitas_name = ?, qty = ?, price = ?, using_date = ?, employee_name = ?, unit = ? WHERE pair_token = ?");
            $stmt_update->bind_param("ssiissss", $headFs, $fasilitas, $qty, $harga, $tanggal_input, $sales, $unit, $pairToken);
            if (!$stmt_update->execute()) {
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $qty,
                "price"            => $harga,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "unit"             => $unit,
                "mode"              => "Update by pair_token: $pairToken"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],     // id user yang sedang login
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
    }
    //akhir bagian Fasilitas wk

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
        $unit = sanitize_text($_POST['satuanV']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, using_date, employee_name, client_name, point, pair_token, unit)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            foreach([1,0] as $point){
                $stmt->bind_param("ssssiiisssiss", $vendorName, $vendor, $idClient, $VenFasilitas, $qty, $hargaJual, $hargaVend, $tanggal_input, $sales, $nameClient, $point, $pairToken, $unit);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $stmt->close();
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
                "unit"             => $unit,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }

    if($_POST['aksi'] === 'update_fasilitasVend'){
        $idFas = sanitize_text($_POST['idFv']);
        $vendor = sanitize_text($_POST['up_vendorHead']);
        $fasilitas = sanitize_text($_POST['up_namaFasilitas']);
        $jumlah = sanitize_text(($_POST['up_qtyV']));
        $hargaJual = sanitize_text(($_POST['up_harga']));
        $hargaVend = sanitize_text($_POST['up_hargaVend']);
        $unit = sanitize_text($_POST['up_satuanV']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];

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
            $cek['client_id'] == $vendor &&
            $cek['fasilitas_name'] == $fasilitas &&
            $cek['qty'] == $jumlah &&
            $cek['price'] == $hargaJual &&
            $cek['price_vend'] == $hargaVend
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        } 
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET client_id = ?, fasilitas_name = ?, qty = ?, price = ?, price_vend =?, using_date = ?, employee_name = ?, unit = ? WHERE pair_token = ?");
            $stmt_update->bind_param("ssiiissss", $vendor, $fasilitas, $jumlah, $hargaJual, $hargaVend, $tanggal_input, $sales, $unit, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "client_id"         => $vendor,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $jumlah,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "pair_token"       => $pairToken,
                "unit"             => $unit,
                "mode"              => "Dobel Update (point 1 & 0)"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],     // id user yang sedang login
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
            exit;
        }
    }
    //akhir bagian Fasilitas Vendor

    //FnB
    if($_POST['aksi'] === 'tambahFnB'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $vendorFnB = sanitize_text($_POST['fnbVendor']);
        $menu = sanitize_text($_POST['fnbHead']);
        $jumlah = sanitize_text($_POST['jumlah']);
        $harga = sanitize_text($_POST['hargaFnB']);
        $detail = sanitize_text($_POST['ket']);
        $unit = sanitize_text($_POST['satuanFnB']);
        $vendor = 'food and beverages';
        $tanggal = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, spec, client_name, point, pair_token, unit)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            foreach ([1,0] as $point){
                $stmt->bind_param("ssssiissssiss", $vendorFnB, $vendor, $idClient, $menu, $jumlah, $harga, $tanggal, $sales, $detail, $clientName, $point, $pairToken, $unit);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $stmt->close();
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
                "client_name"     => $clientName,
                "unit"            => $unit
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],     // id user yang sedang login
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
    }

    if($_POST['aksi'] === 'updateFnB'){
        $id = sanitize_text($_POST['idFnB']);
        $menu = sanitize_text($_POST['up_fnbHead']);
        $jumlah = sanitize_text($_POST['up_jumlah']);
        $harga = sanitize_text($_POST['up_hargaFnB']);
        $keterangan = sanitize_text($_POST['up_ket']);
        $unit = sanitize_text($_POST['up_satuanFnB']);

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
            $cek['fasilitas_name'] == $menu &&
            $cek['qty'] == $jumlah &&
            $cek['price'] == $harga &&
            $cek['spec'] == $keterangan
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name =?, qty = ?, price = ?, spec = ?, unit = ?
                                            WHERE pair_token = ?");
            $stmt_update->bind_param("siisss", $menu, $jumlah, $harga, $keterangan, $unit, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "spec"            => $keterangan,
                "unit"            => $unit
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }
    //akhir FnB

    //cabana and cabin
    if($_POST['aksi'] === 'tambah_cabanaAndcabin'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $fsHead = 'cabana and cabin';
        $namaFasilitas = sanitize_text($_POST['fcnc']);
        $pemakai = sanitize_text($_POST['nPeng']);
        $tanggal = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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

        try{
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, using_date, employee_name, client_name, catatan, point, pair_token)
                                    VALUES(?,?,?,?,?,?,?,?,?)");
            foreach ([1,0] as $point){
                $stmt->bind_param("sssssssis", $fsHead, $idClient, $namaFasilitas, $tanggal, $sales, $clientName, $pemakai, $point, $pairToken);
                if(!$stmt->execute()){
                    throw new Exception($stmt->error);
                }
            }
            $stmt->close();
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
                $_SESSION['Employee_ID'],
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
            $cek['fasilitas_name'] == $fasilitas &&
            $cek['catatan'] == $pengguna
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name = ?, catatan = ? WHERE pair_token = ?");
            $stmt_update->bind_param("sss", $fasilitas, $pengguna, $pairToken);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "fasilitas_name"  => $fasilitas,
                "catatan"         => $pengguna,
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
                "update CnC",
                "rombongan_detail",
                $idCnC,
                $oldData,
                json_encode($newData)
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
    }
    //akhir bagian cabana dan cabin
}
//akhir bagian rombongan Request

//rombongan Request final
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['aksi'])){

    if($_POST['aksi'] === 'getView_fasilitasP'){
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
        $sales = $_SESSION['name'];
        $pairToken = uniqid('FK_');
        $unit = sanitize_text($_POST['satuan']);
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
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, client_name, point, pair_token, unit)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssiisssiss", $headFs, $idClient, $fasilitas, $qty, $harga, $tanggal_input, $sales, $nameClient, $point, $pairToken, $unit);
            if (!$stmt->execute()) {
                throw new Exception($stmt->error);
            }
            $stmt->close();
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
                "satuan"           => $unit,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }

    //update data fasilitas ke rombongan
    if($_POST['aksi'] === 'update_fasilitasWKP'){
        $kode = sanitize_text($_POST['idf']);
        $headFs = sanitize_text($_POST['up_kategori']);
        $fasilitas = sanitize_text($_POST['up_fsl']);
        $qty = sanitize_text($_POST['up_qty']);
        $harga = sanitize_text($_POST['up_hargaWk']);
        $unit = sanitize_text($_POST['up_satuan']);
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];

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
            $cek['group_fasilitas'] == $headFs &&
            $cek['fasilitas_name'] == $fasilitas &&
            $cek['qty'] == $qty &&
            $cek['price'] == $harga
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try {
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET group_fasilitas = ?, fasilitas_name = ?, qty = ?, price = ?, using_date = ?, employee_name = ?, unit = ? WHERE data_id = ? AND point = 0");
            $stmt_update->bind_param("ssiisssi", $headFs, $fasilitas, $qty, $harga, $tanggal_input, $sales, $unit, $kode);
            if (!$stmt_update->execute()) {
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "group_fasilitas"   => $headFs,
                "fasilitas_name"    => $fasilitas,
                "qty"               => $qty,
                "price"             => $harga,
                "using_date"        => $tanggal_input,
                "employee_name"     => $sales,
                "satuan"            => $unit,
                "mode"              => "Update pair_token: $pairToken but by data_id and point=0"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
        $unit = sanitize_text($_POST['satuanV']);
        $vendor = 'vendor';
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, price_vend, using_date, employee_name, client_name, point, pair_token, unit)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssiiisssiss", $vendorName, $vendor, $idClient, $VenFasilitas, $qty, $hargaJual, $hargaVend, $tanggal_input, $sales, $nameClient, $point, $pairToken, $unit);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $stmt->close();
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
                "satuan"            => $unit,
                "mode"              => "Dobel Insert (point 1 & 0)"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }

    if($_POST['aksi'] === 'update_fasilitasVendP'){
        $idFas = sanitize_text($_POST['idFv']);
        $vendor = sanitize_text($_POST['up_vendorHead']);
        $fasilitas = sanitize_text($_POST['up_namaFasilitas']);
        $jumlah = sanitize_text(($_POST['up_qtyV']));
        $hargaJual = sanitize_text(($_POST['up_harga']));
        $hargaVend = sanitize_text($_POST['up_hargaVend']);
        $unit = sanitize_text(($_POST['up_satuanV']));
        $tanggal_input = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];

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
            $cek['client_id'] == $vendor &&
            $cek['fasilitas_name'] == $fasilitas &&
            $cek['qty'] == $jumlah &&
            $cek['price'] == $hargaJual &&
            $cek['price_vend'] == $hargaVend
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        } 
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET client_id = ?, fasilitas_name = ?, qty = ?, price = ?, price_vend =?, using_date = ?, employee_name = ?, unit = ? WHERE data_id = ?");
            $stmt_update->bind_param("ssiiisssi", $vendor, $fasilitas, $jumlah, $hargaJual, $hargaVend, $tanggal_input, $sales, $unit, $idFas);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "client_id"         => $vendor,
                "fasilitas_name"   => $fasilitas,
                "qty"              => $jumlah,
                "price"            => $hargaJual,
                "price_vend"       => $hargaVend,
                "using_date"       => $tanggal_input,
                "employee_name"    => $sales,
                "pair_token"       => $pairToken,
                "satuan"            => $unit,
                "mode"              => "Update final Budgeting"
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
        if(isset($stmt_update)){
            $stmt_update->close();
            exit;
        }
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
        $sales = $_SESSION['name'];
        $pairToken = uniqid('FnB_');
        $unit = sanitize_text($_POST['satuanFnB']);
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
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(client_id, group_fasilitas, fasilitas_id, fasilitas_name, qty, price, using_date, employee_name, spec, client_name, point, pair_token, unit)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("ssssiissssiss", $vendorFnB, $vendor, $idClient, $menu, $jumlah, $harga, $tanggal, $sales, $detail, $clientName, $point, $pairToken, $unit);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $stmt->close();
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
                "satuan"           => $unit,
                "client_name"     => $clientName
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }

    if($_POST['aksi'] === 'updateFnBP'){
        $id = sanitize_text($_POST['idFnB']);
        $menu = sanitize_text($_POST['up_fnbHead']);
        $jumlah = sanitize_text($_POST['up_jumlah']);
        $harga = sanitize_text($_POST['up_hargaFnB']);
        $keterangan = sanitize_text($_POST['up_ket']);
        $unit = sanitize_text($_POST['up_satuanFnB']);

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
            $cek['fasilitas_name'] == $menu &&
            $cek['qty'] == $jumlah &&
            $cek['price'] == $harga &&
            $cek['spec'] == $keterangan
        ){
            echo json_encode(['status' => 'nochange']);
            exit;
        }
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name =?, qty = ?, price = ?, spec = ?, unit = ?
                                            WHERE data_id = ?");
            $stmt_update->bind_param("siissi", $menu, $jumlah, $harga, $keterangan, $unit, $id);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "fasilitas_name"  => $menu,
                "qty"             => $jumlah,
                "price"           => $harga,
                "spec"            => $keterangan,
                "satuan"          => $unit
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }

    //cabana and cabin
    if($_POST['aksi'] === 'tambah_cabanaAndcabinP'){
        $idClient = sanitize_text($_POST['cId']);
        $clientName = sanitize_text($_POST['cName']);
        $fsHead = 'cabana and cabin';
        $namaFasilitas = sanitize_text($_POST['fcnc']);
        $pemakai = sanitize_text($_POST['nPeng']);
        $tanggal = date("Y-m-d H:i:s");
        $sales = $_SESSION['name'];
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
            $konek->begin_transaction();
            $stmt = $konek->prepare("INSERT INTO rombongan_detail(group_fasilitas, fasilitas_id, fasilitas_name, using_date, employee_name, client_name, catatan, point, pair_token)
                                VALUES(?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssssis", $fsHead, $idClient, $namaFasilitas, $tanggal, $sales, $clientName, $pemakai, $point, $pairToken);
            if(!$stmt->execute()){
                throw new Exception($stmt->error);
            }
            $stmt->close();
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
                $_SESSION['Employee_ID'],
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
        try{
            $konek->begin_transaction();
            $stmt_update = $konek->prepare("UPDATE rombongan_detail SET fasilitas_name = ?, catatan = ? WHERE data_id = ?");
            $stmt_update->bind_param("ssi", $fasilitas, $pengguna, $idCnC);
            if(!$stmt_update->execute()){
                throw new Exception($stmt_update->error);
            }
            $stmt_update->close();
            $newData = [
                "fasilitas_name"  => $fasilitas,
                "catatan"         => $pengguna,
            ];
            logActivity(
                $konek,
                $_SESSION['Employee_ID'],
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
    }
}

// fungsi hapus
function softDelete($konek, $tabel, $kolom_id, $id_value) {
    // 1. KEAMANAN KETAT: Whitelist tabel dan kolom yang diizinkan untuk mencegah SQL Injection pada nama identifier
    $allowed_tables = ['rombongan_master', 'rombongan_detail'];
    $allowed_columns = ['rombongan_id', 'data_id', 'fasilitas_id'];

    if (!in_array($tabel, $allowed_tables) || !in_array($kolom_id, $allowed_columns)) {
        return "Akses tabel/kolom ilegal.";
    }

    // Variabel penampung untuk kebutuhan log audit eksternal
    $deleted_data = [
        'mode' => 'single',
        'pair_token' => '',
        'old_rows' => []
    ];

    // 2. CEK APAKAH TABEL PUNYA KOLOM pair_token (Gunakan Prepared Statement agar aman)
    $cekKolom = $konek->prepare("SHOW COLUMNS FROM `$tabel` LIKE 'pair_token'");
    $cekKolom->execute();
    $resultKolom = $cekKolom->get_result();
    $punyaPair = ($resultKolom && $resultKolom->num_rows > 0);
    $cekKolom->close();

    // 3. JIKA ADA pair_token → CARI DATA PASANGANNYA UNTUK DIHAPUS BERSAMA
    if ($punyaPair) {
        $stmt_get_pair = $konek->prepare("SELECT pair_token FROM `$tabel` WHERE `$kolom_id` = ? LIMIT 1");
        $stmt_get_pair->bind_param("s", $id_value);
        $stmt_get_pair->execute();
        $res_pair = $stmt_get_pair->get_result()->fetch_assoc();
        $stmt_get_pair->close();

        $pair = $res_pair['pair_token'] ?? '';

        if (!empty($pair)) {
            // Tarik semua data lama yang berpasangan untuk kebutuhan log aktivitas yang akurat
            $stmt_old = $konek->prepare("SELECT * FROM `$tabel` WHERE pair_token = ? AND del_status = 0");
            $stmt_old->bind_param("s", $pair);
            $stmt_old->execute();
            $result_old = $stmt_old->get_result();
            while($row = $result_old->fetch_assoc()){
                $deleted_data['old_rows'][] = $row;
            }
            $stmt_old->close();

            // Jalankan update massal berbasis token berpasangan
            $stmt_del = $konek->prepare("UPDATE `$tabel` SET del_status = 1 WHERE pair_token = ? AND del_status = 0");
            $stmt_del->bind_param("s", $pair);
            $success = $stmt_del->execute();
            $stmt_del->close();

            if ($success) {
                $deleted_data['mode'] = 'pair';
                $deleted_data['pair_token'] = $pair;
                return ['status' => 'success', 'log_data' => $deleted_data];
            }
            return $konek->error;
        }
    }

    // 4. JALUR DEFAULT: Jika tidak punya pair_token (Hanya hapus baris tunggal)
    // Ambil data lama sebelum dihapus
    $stmt_old = $konek->prepare("SELECT * FROM `$tabel` WHERE `$kolom_id` = ? AND del_status = 0");
    $stmt_old->bind_param("s", $id_value);
    $stmt_old->execute();
    if($row = $stmt_old->get_result()->fetch_assoc()){
        $deleted_data['old_rows'][] = $row;
    }
    $stmt_old->close();

    $stmt_del = $konek->prepare("UPDATE `$tabel` SET del_status = 1 WHERE `$kolom_id` = ? AND del_status = 0");
    $stmt_del->bind_param("s", $id_value);
    $success = $stmt_del->execute();
    $stmt_del->close();

    if ($success) {
        return ['status' => 'success', 'log_data' => $deleted_data];
    }
    return $konek->error;
}



if (isset($_POST['aksi']) && $_POST['aksi'] === 'hapus_data_generik') {
    $tabel = $_POST['tabel'];
    $id = $_POST['id'];
    $kolom = $_POST['kolom'];
    $nama_item = $_POST['nama_item']; // Dikirim dari JS untuk keterangan log
    
    // 1. Jalankan Soft Delete
    $result = softDelete($konek, $tabel, $kolom, $id);

    if (is_array($result) && $result['status'] === "success"){
        $user_id  = isset($_SESSION['Employee_ID']) ? $_SESSION['Employee_ID'] : 0;
        $log_info = $result['log_data'];
        
        // Panggil Fungsi logActivity dengan data yang jauh lebih transparan dan akurat
        logActivity(
            $konek, 
            $user_id,
            'SOFT_DELETE', 
            $tabel, 
            $id, 
            json_encode($log_info['old_rows']), // Mencatat seluruh isi data asli sebelum terhapus (Sangat disukai saat audit)
            json_encode(['del_status' => 1, 'delete_mode' => $log_info['mode']])
        );
        
        echo "success";
    } else {
        error_log("Gagal melakukan penghapusan generik: " . (is_array($result) ? $result['status'] : $result));
        echo "error";
    }
    exit;
}

function logActivity($konek, $user_id, $action, $table_name, $record_id, $old_value = null, $new_value = null){
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $created_at = date("Y-m-d H:i:s");

    $stmt = $konek->prepare("INSERT INTO log_act
        (user_id, action, table_name, record_id, old_value, new_value, ip_address, user_agent, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("sssssssss", 
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