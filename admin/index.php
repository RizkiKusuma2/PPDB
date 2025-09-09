
<?php
// File: admin/index.php
require_once '../config.php';
check_login();
check_role('admin');

$sql = "SELECT p.id, p.nama_lengkap, p.nisn, p.jurusan_pilihan, p.asal_sekolah, p.status_pendaftaran, p.user_id FROM pendaftaran p ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <style>
  body {
    position: relative;
  }

  .background-logo {
    position: fixed; /* agar tetap di belakang dan tidak ikut scroll */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px; /* ukuran logo */
    height: 400px;
    opacity: 0.05; /* transparansi supaya tidak ganggu */
    filter: blur(8px); /* efek blur */
    z-index: 0; /* di bawah konten utama */
    pointer-events: none; /* supaya gak ganggu klik */
    background-image: url('../assets/images/smk2.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: contain;
  }
  

  .dashboard-container {
    position: relative; /* supaya konten di atas background */
    z-index: 1;
  }
</style>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PPDB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    
<div class="dashboard-container">
    <?php include '_admin_nav.php'; ?>
    <main class="dashboard-content">
        <div class="content-header">
            <h2>Data Pendaftar Calon Siswa</h2>
        </div>
        <div class="card" style="background-color: rgba(255, 255, 255, 0.97);">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>NISN</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                        <?php $status_class = 'status-' . strtolower(str_replace(' ', '-', $row['status_pendaftaran'])); ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                            <td><?php echo htmlspecialchars($row['jurusan_pilihan']); ?></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo htmlspecialchars($row['status_pendaftaran']); ?></span></td>
                            <td>
                                <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Detail</a>
                                <a href="chat.php?user_id=<?php echo $row['user_id']; ?>" class="btn btn-sm btn-success">Chat</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center">Belum ada pendaftar.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>