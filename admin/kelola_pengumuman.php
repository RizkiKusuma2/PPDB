<?php
// File: admin/kelola_pengumuman.php
require_once '../config.php';
check_login();
check_role('admin');

$message = '';
$message_type = 'success';

// Ambil daftar jurusan dari tabel pendaftaran (distinct)
$list_jurusan = $conn->query("SELECT DISTINCT jurusan_pilihan FROM pendaftaran ORDER BY jurusan_pilihan ASC");

// Proses Generate Pengumuman Kelulusan
if (isset($_POST['generate_announcement'])) {
    $jurusan = $_POST['filter_jurusan'] ?? 'all';
    $status = $_POST['filter_status'] ?? 'Diterima';

    // Query dasar
    $sql = "SELECT nama_lengkap, nisn, asal_sekolah, jurusan_pilihan 
            FROM pendaftaran 
            WHERE 1=1";

    if ($jurusan !== 'all') {
        $sql .= " AND jurusan_pilihan = '" . $conn->real_escape_string($jurusan) . "'";
    }
    if ($status !== 'all') {
        $sql .= " AND status_pendaftaran = '" . $conn->real_escape_string($status) . "'";
    }

    $sql .= " ORDER BY nama_lengkap ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $isi_pengumuman = '<h3>Daftar Calon Siswa ' . ($status === 'all' ? '' : $status) .
                          ' ' . ($jurusan === 'all' ? '' : 'Jurusan ' . $jurusan) . '</h3>';
        $isi_pengumuman .= '<p>Berikut adalah daftar calon siswa sesuai kriteria yang dipilih.</p>';
        $isi_pengumuman .= '<table class="table-pengumuman">';
        $isi_pengumuman .= '<thead><tr><th>No</th><th>Nama Lengkap</th><th>NISN</th><th>Asal Sekolah</th><th>Jurusan</th></tr></thead><tbody>';
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            $isi_pengumuman .= '<tr>';
            $isi_pengumuman .= '<td>' . $no++ . '</td>';
            $isi_pengumuman .= '<td>' . htmlspecialchars($row['nama_lengkap']) . '</td>';
            $isi_pengumuman .= '<td>' . htmlspecialchars($row['nisn']) . '</td>';
            $isi_pengumuman .= '<td>' . htmlspecialchars($row['asal_sekolah']) . '</td>';
            $isi_pengumuman .= '<td>' . htmlspecialchars($row['jurusan_pilihan']) . '</td>';
            $isi_pengumuman .= '</tr>';
        }
        $isi_pengumuman .= '</tbody></table>';

        $judul = "Pengumuman " . ($status === 'all' ? 'PPDB' : $status) . " " . date('Y');
        $stmt = $conn->prepare("INSERT INTO pengumuman (judul, isi) VALUES (?, ?)");
        $stmt->bind_param("ss", $judul, $isi_pengumuman);
        $stmt->execute();
        $stmt->close();
        $message = "Pengumuman berhasil dibuat.";
    } else {
        $message = "Tidak ada data siswa dengan filter yang dipilih.";
        $message_type = 'danger';
    }
}


// Proses Tambah/Edit Pengumuman Manual
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $id = (int)($_POST['id'] ?? 0);

    if (empty($judul) || empty($isi)) {
        $message = "Judul dan isi pengumuman tidak boleh kosong.";
        $message_type = 'danger';
    } else {
        if ($id > 0) {
            $stmt = $conn->prepare("UPDATE pengumuman SET judul = ?, isi = ? WHERE id = ?");
            $stmt->bind_param("ssi", $judul, $isi, $id);
            $message = "Pengumuman berhasil diperbarui.";
        } else {
            $stmt = $conn->prepare("INSERT INTO pengumuman (judul, isi) VALUES (?, ?)");
            $stmt->bind_param("ss", $judul, $isi);
            $message = "Pengumuman berhasil ditambahkan.";
        }
        $stmt->execute();
        $stmt->close();
    }
}

// Proses Hapus
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    redirect('kelola_pengumuman.php');
}

// Ambil data untuk form edit
$edit_data = ['id' => 0, 'judul' => '', 'isi' => ''];
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM pengumuman WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $edit_data = $result->fetch_assoc();
    }
    $stmt->close();
}

// Ambil semua pengumuman
$list_pengumuman = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal_dibuat DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengumuman - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
</head>
<body class="admin-body">
<div class="dashboard-container">
    <?php include '_admin_nav.php'; ?>
    <main class="dashboard-content">
        <div class="content-header">
            <h2>Kelola Pengumuman</h2>
        </div>

        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="card">
            <h3>Buat Pengumuman Kelulusan</h3>
            <form action="kelola_pengumuman.php" method="POST" 
                onsubmit="return confirm('Buat pengumuman dengan filter yang dipilih?');">
                
                <div class="form-group">
                    <label for="filter_jurusan">Pilih Jurusan</label>
                    <select name="filter_jurusan" id="filter_jurusan">
                        <option value="all">-- Semua Jurusan --</option>
                        <?php if ($list_jurusan && $list_jurusan->num_rows > 0): ?>
                            <?php while($row = $list_jurusan->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($row['jurusan_pilihan']); ?>">
                                    <?php echo htmlspecialchars($row['jurusan_pilihan']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="filter_status">Status Pendaftaran</label>
                    <select name="filter_status" id="filter_status">
                        <option value="all">-- Semua Status --</option>
                        <option value="Diterima">Diterima</option>
                        <option value="Cadangan">Cadangan</option>
                        <option value="Ditolak">Ditolak</option>
                    </select>
                </div>

                <button type="submit" name="generate_announcement" class="btn btn-success">
                    Buat & Publikasikan
                </button>
            </form>
        </div>


        <div class="card">
            <h3><?php echo ($edit_data['id'] > 0) ? 'Edit' : 'Tambah'; ?> Pengumuman Manual</h3>
            <form action="kelola_pengumuman.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($edit_data['judul']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="isi">Isi Pengumuman</label>
                    <textarea name="isi" id="isi" rows="10"><?php echo htmlspecialchars($edit_data['isi']); ?></textarea>
                    <script>
                        ClassicEditor.create(document.querySelector('#isi')).catch(error => console.error(error));
                    </script>
                </div>
                <button type="submit" name="save" class="btn btn-primary">Simpan</button>
                <?php if ($edit_data['id'] > 0): ?>
                    <a href="kelola_pengumuman.php" class="btn btn-secondary">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h3>Daftar Pengumuman</h3>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($list_pengumuman && $list_pengumuman->num_rows > 0): ?>
                        <?php $no = 1; while($row = $list_pengumuman->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($row['judul']); ?></td>
                                <td><?php echo date('d M Y H:i', strtotime($row['tanggal_dibuat'])); ?></td>
                                <td>
                                    <a href="kelola_pengumuman.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="kelola_pengumuman.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus pengumuman ini?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">Belum ada pengumuman.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>
</body>
</html>
