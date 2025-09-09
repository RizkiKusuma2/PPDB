
<?php
// File: admin/detail.php
require_once '../config.php';
check_login();
check_role('admin');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('index.php');
}
$pendaftar_id = (int)$_GET['id'];

// Proses update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = escape($_POST['status_pendaftaran']);
    $stmt_update = $conn->prepare("UPDATE pendaftaran SET status_pendaftaran = ? WHERE id = ?");
    $stmt_update->bind_param("si", $status, $pendaftar_id);
    $stmt_update->execute();
    $stmt_update->close();
    // Refresh halaman untuk melihat perubahan
    redirect("detail.php?id=$pendaftar_id");
}

// Ambil data pendaftar
$stmt = $conn->prepare("SELECT * FROM pendaftaran WHERE id = ?");
$stmt->bind_param("i", $pendaftar_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Data pendaftar tidak ditemukan.");
}
$data = $result->fetch_assoc();
$stmt->close();
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detail Pendaftar - Admin</title>
        <link rel="stylesheet" href="../assets/css/style.css">
        <style>
/* Modal */
                .modal {
                    display: none;
                    position: fixed;
                    z-index: 9999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    overflow: auto;
                    background-color: rgba(0,0,0,0.8);
                    padding: 20px;  /* beri jarak sedikit */
                    text-align: center;
                }

                .modal-content {
                    display: block;
                    margin: auto;
                    width: auto;
                    height: auto;
                    max-width: 188vw;   /* Gunakan 98% lebar viewport */
                    max-height: 185vh;  /* Gunakan 95% tinggi viewport */
                    border-radius: 5px;
                    object-fit: contain;
                }


                .close {
                    position: absolute;
                    top: 20px;
                    right: 35px;
                    color: #fff;
                    font-size: 40px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .close:hover {
                    color: #f00;
                }
                #downloadLink {
                    display: block;
                    text-align: center;
                    margin: 15px auto;
                    padding: 10px 20px;
                    background-color: #28a745;
                    color: #fff;
                    text-decoration: none;
                    border-radius: 5px;
                    max-width: 200px;
                }
                </style>

    </head>
    <body class="admin-body">

    <div class="dashboard-container">
        <?php include '_admin_nav.php'; ?>
        <main class="dashboard-content">
            <div class="content-header">
                <h2>Detail Pendaftar: <?php echo htmlspecialchars($data['nama_lengkap']); ?></h2>
                <a href="index.php" class="btn btn-secondary">Kembali</a>
            </div>
            <div class="detail-grid">
                <div class="card">
                    <h3><img src="../<?php echo htmlspecialchars($data['file_foto_profil']); ?>" alt="Foto Profil" style="width:100px; height:auto; border-radius:5px; float:right;">Data Siswa</h3>
                    <ul class="detail-list">
                        <li><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($data['nama_lengkap']); ?></li>
                        <li><strong>NISN:</strong> <?php echo htmlspecialchars($data['nisn']); ?></li>
                        <li><strong>Tempat, Tgl Lahir:</strong> <?php echo htmlspecialchars($data['tempat_lahir'] . ', ' . date('d F Y', strtotime($data['tanggal_lahir']))); ?></li>
                        <li><strong>Jenis Kelamin:</strong> <?php echo htmlspecialchars($data['jenis_kelamin']); ?></li>
                        <li><strong>Agama:</strong> <?php echo htmlspecialchars($data['agama']); ?></li>
                        <li><strong>Alamat:</strong> <?php echo htmlspecialchars($data['alamat']); ?></li>
                    </ul>
                </div>
                <div class="card">
                    <h3>Data Akademik & Pilihan</h3>
                    <ul class="detail-list">
                        <li><strong>Asal Sekolah:</strong> <?php echo htmlspecialchars($data['asal_sekolah']); ?></li>
                        <li><strong>Jurusan Pilihan:</strong> <?php echo htmlspecialchars($data['jurusan_pilihan']); ?></li>
                    </ul>
                </div>
                <div class="card">
    <h3>Data Orang Tua</h3>
    <ul class="detail-list">
        <li><strong>Nama Ayah:</strong> <?php echo htmlspecialchars($data['nama_ayah']); ?></li>
        <li><strong>Pekerjaan Ayah:</strong> <?php echo htmlspecialchars($data['pekerjaan_ayah']); ?></li>
        <li><strong>No. HP Ayah:</strong> <?php echo htmlspecialchars($data['no_hp_ayah']); ?></li>
        <li><strong>Nama Ibu:</strong> <?php echo htmlspecialchars($data['nama_ibu']); ?></li>
        <li><strong>Pekerjaan Ibu:</strong> <?php echo htmlspecialchars($data['pekerjaan_ibu']); ?></li>
        <li><strong>No. HP Ibu:</strong> <?php echo htmlspecialchars($data['no_hp_ibu']); ?></li>
    </ul>
</div>

<div class="card">
    <h3>Nilai SKHUN</h3>
    <?php 
    $nilai_skhun_arr = json_decode($data['nilai_skhun'], true);
    if (is_array($nilai_skhun_arr)): ?>
        <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 400px;">
            <thead>
                <tr>
                    <td><strong>Jalur Pendaftaran</strong></td>
                    <td> <?= ucfirst($data['jalur']); ?></td>
                </tr>

                <tr>
                    <th>Mata Pelajaran</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($nilai_skhun_arr as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['mapel']); ?></td>
                    <td><?php echo htmlspecialchars($item['nilai']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Data nilai SKHUN tidak valid atau belum diinput.</p>
    <?php endif; ?>
</div>

                <div class="card">
                    <h3>Verifikasi Pendaftaran</h3>
                    <form action="detail.php?id=<?php echo $pendaftar_id; ?>" method="POST">
                        <div class="form-group">
                            <label for="status_pendaftaran">Ubah Status Pendaftaran:</label>
                            <select name="status_pendaftaran" id="status_pendaftaran">
                                <option value="Menunggu Verifikasi" <?php if($data['status_pendaftaran'] == 'Menunggu Verifikasi') echo 'selected'; ?>>Menunggu Verifikasi</option>
                                <option value="Diterima" <?php if($data['status_pendaftaran'] == 'Diterima') echo 'selected'; ?>>Diterima</option>
                                <option value="Cadangan" <?php if($data['status_pendaftaran'] == 'Cadangan') echo 'selected'; ?>>Cadangan</option>
                                <option value="Ditolak" <?php if($data['status_pendaftaran'] == 'Ditolak') echo 'selected'; ?>>Ditolak</option>
                            </select>

                        </div>
                        <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                    </form>
                    <hr>
                    <h3>Berkas Terlampir</h3>
                            <div class="berkas-list">
                                <button type="button" class="btn btn-info btn-sm" onclick="openModal('../<?php echo htmlspecialchars($data['file_kk']); ?>')">Lihat KK</button>
                                <button type="button" class="btn btn-info btn-sm" onclick="openModal('../<?php echo htmlspecialchars($data['file_shu']); ?>')">Lihat SKHU</button>
                                <button type="button" class="btn btn-info btn-sm" onclick="openModal('../<?php echo htmlspecialchars($data['file_ijazah']); ?>')">Lihat Ijazah</button>
                                <?php if (!empty($data['file_sertifikat'])): ?>
                                    <button type="button" class="btn btn-info btn-sm" onclick="openModal('../<?php echo htmlspecialchars($data['file_sertifikat']); ?>')">Lihat Sertifikat Prestasi</button>
                                <?php endif; ?>

                            </div>

                            <!-- Modal Pop-up -->
                            <div id="fileModal" class="modal">
                                <span class="close" onclick="closeModal()">&times;</span>
                                <img class="modal-content" id="filePreview" alt="Preview Berkas">
                                <a id="downloadLink" class="btn btn-success btn-sm" download>Download</a>
                            </div>

                </div>
            </div>
        </main>
    </div>
        <script>
            function openModal(filePath) {
                const modal = document.getElementById("fileModal");
                const preview = document.getElementById("filePreview");
                const downloadLink = document.getElementById("downloadLink");

                preview.src = filePath;
                downloadLink.href = filePath;
                modal.style.display = "block";
            }

            function closeModal() {
                document.getElementById("fileModal").style.display = "none";
            }
        </script>

    </body>
    </html>
