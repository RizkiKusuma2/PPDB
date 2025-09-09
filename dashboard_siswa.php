<?php
// File: dashboard_siswa.php
require_once 'config.php';
check_login();
check_role('siswa');

$user_id = $_SESSION['user_id'];

// Ambil data pendaftaran siswa
$stmt = $conn->prepare("SELECT * FROM pendaftaran WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data_siswa = $result->fetch_assoc();
$stmt->close();

if (!$data_siswa) {
    die("Data pendaftaran tidak ditemukan. Hubungi admin.");
}

// Tentukan kelas CSS untuk status
$status_class = 'status-' . strtolower(str_replace(' ', '-', $data_siswa['status_pendaftaran']));
if (isset($_POST['update_dokumen'])) {
    function upload_file_edit($file_input_name, $nisn, $upload_subdir, $current_file) {
        if (!empty($_FILES[$file_input_name]['name'])) {
            $target_dir = 'uploads/' . $upload_subdir . '/';
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_name = $nisn . "_" . basename($_FILES[$file_input_name]["name"]);
            $target_file = $target_dir . $file_name;
            if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
                return $target_file;
            }
        }
        return $current_file;
    }

    // Ambil file lama
    $foto_profil = $data_siswa['file_foto_profil'];
    $kk = $data_siswa['file_kk'];
    $ijazah = $data_siswa['file_ijazah'];
    $shu = $data_siswa['file_shu'];
    $sertifikat = $data_siswa['file_sertifikat']; 

    $nisn_siswa = $data_siswa['nisn'];

    // Upload baru (jika ada)
    $foto_profil = upload_file_edit('file_foto_profil', $nisn_siswa, 'foto', $foto_profil);
    $kk = upload_file_edit('file_kk', $nisn_siswa, 'kk', $kk);
    $ijazah = upload_file_edit('file_ijazah', $nisn_siswa, 'ijazah', $ijazah);
    $shu = upload_file_edit('file_shu', $nisn_siswa, 'shu', $shu);
    $sertifikat = upload_file_edit('file_sertifikat', $nisn_siswa, 'sertifikat', $sertifikat);

    // Update database, tambahkan kolom file_sertifikat
    $stmt = $conn->prepare("UPDATE pendaftaran SET file_foto_profil=?, file_kk=?, file_ijazah=?, file_shu=?, file_sertifikat=? WHERE user_id=?");
    $stmt->bind_param("sssssi", $foto_profil, $kk, $ijazah, $shu, $sertifikat, $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Dokumen berhasil diperbarui!'); window.location='dashboard_siswa.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui dokumen!');</script>";
    }
    $stmt->close();
}


// Update data profil jika form dikirim

if (isset($_POST['update_profil'])) {
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $nisn = escape($_POST['nisn']);
    $jurusan = escape($_POST['jurusan_pilihan']);
    $asal_sekolah = escape($_POST['asal_sekolah']);
    $tempat_lahir = escape($_POST['tempat_lahir']);
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $alamat = escape($_POST['alamat']);
    $nama_ayah = escape($_POST['nama_ayah']);
    $no_hp_ayah = escape($_POST['no_hp_ayah']);
    $pekerjaan_ayah = escape($_POST['pekerjaan_ayah']);
    $nama_ibu = escape($_POST['nama_ibu']);
    $no_hp_ibu = escape($_POST['no_hp_ibu']);
    $pekerjaan_ibu = escape($_POST['pekerjaan_ibu']);
    $jalur = $_POST['jalur'];
    $nilai_skhun_json = $_POST['nilai_skhun'];

    // Correct the SQL query and bind_param
    $stmt = $conn->prepare("UPDATE pendaftaran SET
        nama_lengkap=?, nisn=?, jurusan_pilihan=?, asal_sekolah=?, tempat_lahir=?, tanggal_lahir=?, alamat=?,
        nama_ayah=?, no_hp_ayah=?, pekerjaan_ayah=?, nama_ibu=?, no_hp_ibu=?, pekerjaan_ibu=?, jalur=?, nilai_skhun=?
        WHERE user_id=?");

    $stmt->bind_param("sssssssssssssssi",
        $nama_lengkap, $nisn, $jurusan, $asal_sekolah, $tempat_lahir, $tanggal_lahir, $alamat,
        $nama_ayah, $no_hp_ayah, $pekerjaan_ayah, $nama_ibu, $no_hp_ibu, $pekerjaan_ibu, $jalur, $nilai_skhun_json,
        $user_id
    );

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='dashboard_siswa.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - PPDB SMKS Bina Satria</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="public-page" >
    
    <?php include '_header.php'; ?>

    <main class="dashboard-page" style="background-image: url('assets/images/backgroundweb.png'); background-size: cover; background-position: center;">
        <div class="container" >
            <div class="content-header">
                <h2>Dashboard Calon Siswa</h2>
            </div>
            <div class="card">
                <h3>Selamat Datang, <?php echo htmlspecialchars($data_siswa['nama_lengkap']); ?>!</h3>
                <p>Halaman ini adalah pusat informasi pendaftaran Anda. Silakan periksa status Anda secara berkala.</p>
            </div>
            
            <div class="card">
    <h3>Profil Pendaftaran</h3>

    <div id="profileDisplay">
        <tr>
    
</tr>

        <p><strong>Nama Lengkap:</strong> <?php echo htmlspecialchars($data_siswa['nama_lengkap']); ?></p>
        <p><strong>NISN:</strong> <?php echo htmlspecialchars($data_siswa['nisn']); ?></p>
        <p><strong>Jurusan Pilihan:</strong> <?php echo htmlspecialchars($data_siswa['jurusan_pilihan']); ?></p>
        <p><strong>Asal Sekolah:</strong> <?php echo htmlspecialchars($data_siswa['asal_sekolah']); ?></p>
        <p><strong>Tempat Lahir:</strong> <?php echo htmlspecialchars($data_siswa['tempat_lahir']); ?></p>
        <p><strong>Tanggal Lahir:</strong> <?php echo htmlspecialchars($data_siswa['tanggal_lahir']); ?></p>
        <p><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($data_siswa['alamat'])); ?></p>

        <p><strong>Nama Ayah:</strong> <?php echo htmlspecialchars($data_siswa['nama_ayah']); ?></p>
        <p><strong>No. HP Ayah:</strong> <?php echo htmlspecialchars($data_siswa['no_hp_ayah']); ?></p>
        <p><strong>Pekerjaan Ayah:</strong> <?php echo htmlspecialchars($data_siswa['pekerjaan_ayah']); ?></p>
        <p><strong>Nama Ibu:</strong> <?php echo htmlspecialchars($data_siswa['nama_ibu']); ?></p>
        <p><strong>No. HP Ibu:</strong> <?php echo htmlspecialchars($data_siswa['no_hp_ibu']); ?></p>
        <p><strong>Pekerjaan Ibu:</strong> <?php echo htmlspecialchars($data_siswa['pekerjaan_ibu']); ?></p>
        <p><strong>Jalur Pendaftaran:</strong> <?= htmlspecialchars(ucfirst($data_siswa['jalur'])); ?></p>

        <p><strong>Nilai SKHUN:</strong></p>
        <?php 
$nilai_skhun_json = $data_siswa['nilai_skhun']; 
$nilai_skhun_arr = json_decode($nilai_skhun_json, true);
?>
                
<?php if (is_array($nilai_skhun_arr)): ?>
    <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%; max-width: 400px;">
        <thead>
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

        <button id="editProfileBtn" class="btn btn-warning">Edit</button>
    </div>

    <form id="profileEditForm" method="POST" action="dashboard_siswa.php" style="display:none;">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($data_siswa['nama_lengkap']); ?>" required>
        </div>
        <div class="form-group">
            <label>NISN</label>
            <input type="text" name="nisn" value="<?php echo htmlspecialchars($data_siswa['nisn']); ?>" required>
        </div>
        <div class="form-group">
            <label>Jurusan Pilihan</label>
            <input type="text" name="jurusan_pilihan" value="<?php echo htmlspecialchars($data_siswa['jurusan_pilihan']); ?>" required>
        </div>
        <div class="form-group">
            <label>Asal Sekolah</label>
            <input type="text" name="asal_sekolah" value="<?php echo htmlspecialchars($data_siswa['asal_sekolah']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tempat Lahir</label>
            <input type="text" name="tempat_lahir" value="<?php echo htmlspecialchars($data_siswa['tempat_lahir']); ?>" required>
        </div>
        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" name="tanggal_lahir" value="<?php echo htmlspecialchars($data_siswa['tanggal_lahir']); ?>" required>
        </div>
        <div class="form-group">
            <label>Alamat</label>
            <textarea name="alamat" rows="3" required><?php echo htmlspecialchars($data_siswa['alamat']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Nama Ayah</label>
            <input type="text" name="nama_ayah" value="<?php echo htmlspecialchars($data_siswa['nama_ayah']); ?>" required>
        </div>
        <div class="form-group">
            <label>No. HP Ayah</label>
            <input type="tel" name="no_hp_ayah" value="<?php echo htmlspecialchars($data_siswa['no_hp_ayah']); ?>" required>
        </div>
        <div class="form-group">
            <label>Pekerjaan Ayah</label>
            <input type="text" name="pekerjaan_ayah" value="<?php echo htmlspecialchars($data_siswa['pekerjaan_ayah']); ?>" required>
        </div>
        <div class="form-group">
            <label>Nama Ibu</label>
            <input type="text" name="nama_ibu" value="<?php echo htmlspecialchars($data_siswa['nama_ibu']); ?>" required>
        </div>
        <div class="form-group">
            <label>No. HP Ibu</label>
            <input type="tel" name="no_hp_ibu" value="<?php echo htmlspecialchars($data_siswa['no_hp_ibu']); ?>" required>
        </div>
        <div class="form-group">
            <label>Pekerjaan Ibu</label>
            <input type="text" name="pekerjaan_ibu" value="<?php echo htmlspecialchars($data_siswa['pekerjaan_ibu']); ?>" required>
        </div>

        <div class="form-group">
            <label>Nilai SKHUN (Format JSON)</label>
            <textarea name="nilai_skhun" rows="5"><?php echo htmlspecialchars($data_siswa['nilai_skhun']); ?></textarea>
        </div>

        <div class="form-group">
    <label>Jalur Pendaftaran</label><br>
    <label>
        <input type="radio" name="jalur" value="Mandiri" 
            <?php echo ($data_siswa['jalur'] == 'Mandiri') ? 'checked' : ''; ?>> Mandiri
    </label><br>
    <label>
        <input type="radio" name="jalur" value="Prestasi" 
            <?php echo ($data_siswa['jalur'] == 'Prestasi') ? 'checked' : ''; ?>> Prestasi
    </label>
</div>

        
        <button type="submit" name="update_profil" class="btn btn-success">Simpan</button>
        <button type="button" id="cancelEditBtn" class="btn btn-secondary">Batal</button>
    </form>
</div>

            <div class="card">
                <h3>Dokumen Anda</h3>
                <div class="gallery-docs">
                    <div class="doc-item">
                        <img src="<?php echo htmlspecialchars($data_siswa['file_foto_profil']); ?>" alt="Foto Profil" onclick="openPopup(this)">
                        <p>Foto Profil</p>
                    </div>
                    <div class="doc-item">
                        <img src="<?php echo htmlspecialchars($data_siswa['file_kk']); ?>" alt="Kartu Keluarga" onclick="openPopup(this)">
                        <p>Kartu Keluarga</p>
                    </div>
                    <div class="doc-item">
                        <img src="<?php echo htmlspecialchars($data_siswa['file_ijazah']); ?>" alt="Ijazah" onclick="openPopup(this)">
                        <p>Ijazah</p>
                    </div>
                    <div class="doc-item">
                        <img src="<?php echo htmlspecialchars($data_siswa['file_shu']); ?>" alt="SKHU" onclick="openPopup(this)">
                        <p>SKHU</p>
                    </div>
                    <div class="doc-item">
                        <?php if ($data_siswa['file_sertifikat']): ?>
                            <img src="<?php echo htmlspecialchars($data_siswa['file_sertifikat']); ?>" alt="Sertifikat Prestasi" onclick="openPopup(this)">
                            <p>Sertifikat Prestasi</p>
                        <?php else: ?>
                            <p>Tidak ada Sertifikat Prestasi</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <div class="card">
                <h3>Perbarui Dokumen</h3>
                <form action="dashboard_siswa.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file_foto_profil">Foto Profil (3x4, Latar Biru/Merah)</label>
                        <input type="file" name="file_foto_profil" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label for="file_kk">Scan Kartu Keluarga</label>
                        <input type="file" name="file_kk" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="form-group">
                        <label for="file_ijazah">Scan Ijazah</label>
                        <input type="file" name="file_ijazah" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="form-group">
                        <label for="file_shu">Scan SKHU</label>
                        <input type="file" name="file_shu" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                    <div class="form-group">
                        <label for="file_sertifikat">Sertifikat Prestasi (jika ada)</label>
                        <input type="file" name="file_sertifikat" accept=".jpg,.jpeg,.png,.pdf">
                    </div>

                    <button type="submit" name="update_dokumen" class="btn btn-primary">Update Dokumen</button>
                </form>
            </div>
                
        </div>
    </main>

    <?php include '_footer.php'; ?>
    <div id="imgPopup" class="popup-modal" onclick="closePopup()">
            <span class="popup-close" onclick="closePopup()">&times;</span>
            <img class="popup-content" id="popupImage">
            <div id="popupCaption"></div>
        </div>
    
    <script>
    function openPopup(imgElement) {
        var modal = document.getElementById("imgPopup");
        var modalImg = document.getElementById("popupImage");
        var captionText = document.getElementById("popupCaption");
        modal.style.display = "block";
        modalImg.src = imgElement.src;
        captionText.innerHTML = imgElement.alt;
    }

    function closePopup() {
        document.getElementById("imgPopup").style.display = "none";
    }
    document.getElementById('editProfileBtn').addEventListener('click', function() {
        document.getElementById('profileDisplay').style.display = 'none';
        document.getElementById('profileEditForm').style.display = 'block';
    });

    document.getElementById('cancelEditBtn').addEventListener('click', function() {
        document.getElementById('profileEditForm').style.display = 'none';
        document.getElementById('profileDisplay').style.display = 'block';
    });
    </script>

</body>
</html>