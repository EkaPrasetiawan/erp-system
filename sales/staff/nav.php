<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['SCRIPT_NAME']);

?>

<div class="sb-sidenav-menu">
    <div class="nav">
        <div class="sb-sidenav-menu-heading">Utama</div>
        <a class="nav-link <?= ($currentPage === 'home.php') ? 'active' : ''; ?>" href="home.php">
            <div class="sb-nav-link-icon"><i class="fa-solid fa-house"></i></div>
            Home
        </a>
        <div class="sb-sidenav-menu-heading">Work</div>
        <a class="nav-link <?= ($currentPage === 'client.php') ? 'active' : ''; ?>" href="client.php">
            <div class="sb-nav-link-icon"><i class="fa-regular fa-id-badge"></i></div>
            Client
        </a>
        <a class="nav-link <?= ($currentPage === 'rombongan.php') ? 'active' : ''; ?>" href="rombongan.php">
            <div class="sb-nav-link-icon"><i class="fa-solid fa-people-pulling"></i></div>
            Rombongan
        </a>
        <a class="nav-link <?= ($currentPage === 'rombongan-detail.php') ? 'active' : ''; ?>" href="rombongan-detail.php">
            <div class="sb-nav-link-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            Rombongan Detail
        </a>
        <a class="nav-link <?= ($currentPage === 'frm-print.php') ? 'active' : ''; ?>" href="frm-print.php">
            <div class="sb-nav-link-icon"><i class="fa-solid fa-print"></i></div>
            Print Form
        </a>
    </div>
</div>
<div class="sb-sidenav-footer">
    <div class="small">Logged in as:</div>
    <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>
</div>