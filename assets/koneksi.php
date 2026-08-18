<?php

$host   = "localhost";
$user   = "root";
$pass   = "";
$db     = "db";
$konek = new mysqli($host, $user, $pass, $db);
if ($konek->connect_error){
    die("koneksi gagal : ".$konek->connect_error);
}

?>
