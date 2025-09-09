
<?php
// File: pengumuman.php
require_once 'config.php';
check_login();
// Bisa diakses admin dan siswa
if (!in_array($_SESSION['role'], ['admin', 'siswa'])) {
    redirect('login.php');
}


$sql = "SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - PPDB SMKS Bina Satria</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="public-page">
    
    <?php include '_header.php'; ?>

    <main class="dashboard-page">
        <div class="container">
            <div class="content-header">
                <h2>Pengumuman Pendaftaran</h2>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($row['judul']); ?></h3>
                    <p class="text-muted">Dipublikasikan pada: <?php echo date('d F Y, H:i', strtotime($row['tanggal_dibuat'])); ?></p>
                    <hr>
                    <div class="announcement-content">
                        <?php echo $row['isi']; // Tampilkan HTML langsung dari database ?>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="card">
                    <p style="text-align:center;">Belum ada pengumuman yang dipublikasikan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '_footer.php'; ?>
</body>
</html>
