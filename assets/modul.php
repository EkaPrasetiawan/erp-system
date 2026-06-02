<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Jakarta');
require 'koneksi.php';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
    $username = $_POST['userID'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $konek->prepare("SELECT * FROM employee_card WHERE alamat_email=?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);

            // if ($password === $row['pwd'] && $level === $row['granting_level'] ) {
            if ($password === $row['pwd'] ) {
                // Simpan data ke session
                $_SESSION['Employee_ID'] = $row['Employee_ID'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['level'] = $row['grade'];
                $_SESSION['jabatan'] = $row['jabatan'];
                
                // Tentukan redirect berdasarkan level
                $redirect = 'index.php'; // default fallback jika level tidak dikenali

                if ($row['grade'] == '3' && $row['jabatan'] == 'Manager') {
                    $redirect = 'sales/manager/home.php';
                } elseif ($row['grade'] == '3' && $row['jabatan'] == 'SPV') {
                    $redirect = 'sales/spv/home.php';
                } elseif ($row['grade'] == '3' && $row['jabatan'] == 'Staff') {
                    $redirect = 'sales/staff/home.php';
                }

                error_log("Login berhasil: " . $_SESSION['name'] . " (Level: " . $row['granting_level'] . ")");

                echo json_encode([
                    'status' => 'success',
                    'redirect' => $redirect
                ]);
                exit;
            }
        }

        echo json_encode(['status' => 'error', 'message' => 'Username atau password salah']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak lengkap']);
    }
    exit;
}


?>