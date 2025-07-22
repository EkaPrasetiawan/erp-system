<?php
require 'koneksi.php';

$cekSales = $konek->query("
    SELECT name, departemen, divisi, tgl_mulai
    FROM employee_card
    WHERE departemen = 'sales', AND inactive = 0
");

if(!$cekSales){
    die("Query Error : ".$konek->error);
}
$sales = [];
while($row = $cekSales->fetch_assoc()){
    $sales[] = $row;
}

?>