
<?php
// File: _header.php
?>
<header class="public-header">
    <div class="container">
        <a href="index.php" class="header-logo">
        <img src="assets/images/smk2.png" alt="Logo SMKS Bina Satria">
        SMKS Bina Satria
        </a>
        <nav class="public-nav">
            
            <a href="gallery.php">Galeri</a>
            <a href="index.php#profil">Profil Sekolah</a>
            <a href="index.php#jurusan">Jurusan</a>
            <a href="index.php#kontak">Kontak</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php 
                    // Tentukan URL dashboard berdasarkan role
                    $dashboard_url = ($_SESSION['role'] === 'admin') ? 'admin/index.php' : 'dashboard_siswa.php';
                ?>
                <a href="<?php echo $dashboard_url; ?>" class="btn btn-info btn-sm">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-secondary btn-sm">Login</a>
                <a href="register.php" class="btn btn-primary btn-sm">Daftar PPDB</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
