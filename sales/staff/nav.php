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
        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#layoutPrintForm"
            aria-expanded="false" aria-controls="layoutPrintForm">
        <div class="sb-nav-link-icon"><i class="fa-solid fa-file-export"></i></div>
            Print Form
        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
        </a>
        <div class="collapse" id="layoutPrintForm" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
            <nav class="sb-sidenav-menu-nested nav">
                <a class="nav-link" href="frm-print.php">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-print"></i></div>
                    Form Kesepakatan
                </a>
                <a class="nav-link" href="frm-event-order.php">
                    <div class="sb-nav-link-icon"><i class="fa-solid fa-clipboard-list"></i></div>
                    Form Event Order</a>
            </nav>
        </div>
    </div>
</div>
<div class="sb-sidenav-footer">
    <div class="small">Logged in as:</div>
    <?= htmlspecialchars($_SESSION['name'], ENT_QUOTES, 'UTF-8'); ?>
</div>

<script>
    // Tandai menu yang sedang aktif berdasarkan halaman saat ini
    (function() {
        const current = location.pathname.split('/').pop();
        document.querySelectorAll('.sb-sidenav-menu a.nav-link').forEach(function(link) {
            const href = link.getAttribute('href') || '';
            const file = href.split('/').pop();
            if (file && file === current) {
                link.classList.add('active');
                // Buka dropdown induk jika menu berada di dalam collapse
                const collapse = link.closest('.collapse');
                if (collapse) {
                    collapse.classList.add('show');
                    const toggler = document.querySelector('[data-bs-target="#' + collapse.id + '"]');
                    if (toggler) {
                        toggler.classList.remove('collapsed');
                        toggler.setAttribute('aria-expanded', 'true');
                    }
                }
            }
        });
    })();
</script>