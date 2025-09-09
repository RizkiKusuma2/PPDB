<?php
// File: _siswa_nav.php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="dashboard-header">
    <div class="header-title">
        <h3>PPDB SMKS Bina Satria</h3>
        <span>(Calon Siswa)</span>
    </div>
    <nav>
        <a href="dashboard_siswa.php" class="<?php echo ($current_page == 'dashboard_siswa.php') ? 'active' : ''; ?>">Dashboard</a>
        <a href="pengumuman.php" class="<?php echo ($current_page == 'pengumuman.php') ? 'active' : ''; ?>">Pengumuman</a>
        <a href="chat_siswa.php" class="<?php echo ($current_page == 'chat_siswa.php') ? 'active' : ''; ?>">Chat Admin</a>
        <a href="logout.php" class="btn-logout">Logout</a>
    </nav>
</header>
