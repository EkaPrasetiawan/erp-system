<?php
require 'koneksi.php';

$cekSales = $konek->query("
    SELECT Employee_ID, name, departemen, divisi, tgl_mulai, checked
    FROM employee_card
    WHERE divisi = 'sales' AND inactive = 0
");

if(!$cekSales){
    die("Query Error : ".$konek->error);
}
$sales = [];
while($row = $cekSales->fetch_assoc()){
    $row['checked'] = ($row['checked'] == 1) ? 'Aktif' : 'Tidak Aktif';
    $sales[] = $row;
}

?>