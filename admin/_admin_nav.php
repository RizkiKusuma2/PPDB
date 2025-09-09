<?php
// File: admin/_admin_nav.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="dashboard-header">
    <div class="header-title">
        <h3>Admin Panel</h3>
    </div>
    <nav>
        <a href="../index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Halaman Utama</a>
        <a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">Data Pendaftar</a>
        <a href="kelola_pengumuman.php" class="<?php echo ($current_page == 'kelola_pengumuman.php') ? 'active' : ''; ?>">Pengumuman</a>
        <a href="laporan.php" class="<?php echo ($current_page == 'laporan.php') ? 'active' : ''; ?>">Cetak Laporan</a>
    </nav>
</header>
