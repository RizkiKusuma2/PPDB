<?php
// File: admin/laporan.php
require_once '../config.php';
check_login();
check_role('admin');

$filter_jurusan = $_GET['jurusan'] ?? 'semua';
// Kita abaikan filter status untuk menampilkan khusus diterima di tabel utama
// $filter_status = $_GET['status'] ?? 'semua';

// Statistik tanpa menampilkan menunggu verifikasi & cadangan
$where_clause = "1=1";
if ($filter_jurusan !== 'semua') {
    $where_clause .= " AND jurusan_pilihan = '" . $conn->real_escape_string($filter_jurusan) . "'";
}

$total_all = $conn->query("SELECT COUNT(*) AS total FROM pendaftaran WHERE $where_clause")->fetch_assoc()['total'];
$total_diterima = $conn->query("SELECT COUNT(*) AS total FROM pendaftaran WHERE $where_clause AND status_pendaftaran='Diterima'")->fetch_assoc()['total'];
// Hilangkan statistik menunggu verifikasi dan cadangan
$total_ditolak = $conn->query("SELECT COUNT(*) AS total FROM pendaftaran WHERE $where_clause AND (status_pendaftaran='Ditolak' OR status_pendaftaran='Cadangan')")->fetch_assoc()['total'];

// Ambil data siswa yang statusnya Diterima saja untuk tabel utama
$sql_utama = "SELECT * FROM pendaftaran WHERE status_pendaftaran='Diterima'";
$params_utama = [];
$types_utama = '';
if ($filter_jurusan !== 'semua') {
    $sql_utama .= " AND jurusan_pilihan = ?";
    $params_utama[] = $filter_jurusan;
    $types_utama .= 's';
}
$sql_utama .= " ORDER BY nama_lengkap ASC";

$stmt_utama = $conn->prepare($sql_utama);
if (!empty($params_utama)) {
    $stmt_utama->bind_param($types_utama, ...$params_utama);
}
$stmt_utama->execute();
$result_utama = $stmt_utama->get_result();

// Ambil data Cadangan & Ditolak untuk tabel khusus
$sql_cadangan = "SELECT * FROM pendaftaran WHERE status_pendaftaran IN ('Cadangan','Ditolak')";
$params_cadangan = [];
$types_cadangan = '';
if ($filter_jurusan !== 'semua') {
    $sql_cadangan .= " AND jurusan_pilihan = ?";
    $params_cadangan[] = $filter_jurusan;
    $types_cadangan .= 's';
}
$sql_cadangan .= " ORDER BY nama_lengkap ASC";

$stmt_cadangan = $conn->prepare($sql_cadangan);
if (!empty($params_cadangan)) {
    $stmt_cadangan->bind_param($types_cadangan, ...$params_cadangan);
}
$stmt_cadangan->execute();
$result_cadangan = $stmt_cadangan->get_result();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pendaftaran Siswa Baru - SMKS Bina Satria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .print-container {
    
    z-index: 1;
    max-width: 1000px;
    margin: 20px auto;
    padding: 20px;
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}


        /* ======== Kop Surat ======== */
        .print-header {
            text-align: left;
            margin-bottom: 1rem;
        }

        .print-header div {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .print-header img {
            max-width: 90px;
            height: auto;
        }

        .print-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }

        .print-header p {
            margin: 2px 0;
            font-size: 0.95rem;
            line-height: 1.3;
        }

        .print-header hr {
            margin-top: 10px;
            border-top: 2px solid #333;
        }

        /* ======== Tabel ======== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 0.95rem;
            background: #fafafa;
        }

        table thead {
            background-color: #007bff;
            color: #fff;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: center;
            vertical-align: middle;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        /* Statistik */
        .statistik {
            margin-top: 1.5rem;
            font-size: 0.95rem;
            background: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
        }

        .statistik p {
            margin: 5px 0;
        }

        /* ======== Filter Form ======== */
        .filter-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }

        .filter-form select,
        .filter-form button {
            padding: 6px 8px;
            font-size: 0.9rem;
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 3px;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-sm {
            font-size: 0.85rem;
        }

        .btn-primary {
            background-color: #007bff;
            color: #fff;
            border: none;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            border: none;
        }

        .btn-success {
            background-color: #28a745;
            color: #fff;
            border: none;
        }

        .btn:hover {
            opacity: 0.9;
        }

        /* ======== Mode Cetak ======== */
        @media print {
            body {
                background: #fff;
            }

            .no-print {
                display: none;
            }

            .print-container {
                box-shadow: none;
                border: none;
                padding: 0;
            }

            table {
                font-size: 10pt;
            }

            h1, h2, p {
                margin-bottom: 0.3rem;
            }

            @page {
                size: A4;
                margin: 20mm;
            }
        }

        /* ======== TTD dan Tanggal ======== */
        .ttd-container {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 0 20px;
            flex-wrap: wrap;
        }

        .tanggal-ttd {
            width: 100%;
            text-align: right;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }

        .ttd-left, .ttd-right {
            text-align: center;
            width: 40%;
            position: relative;
        }

        .cap-container {
            position: absolute;
            top: -10px;
            right: 30px;
            opacity: 0.2;
        }

        .logo-background {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 0;
    opacity: 0.15;
    filter: blur(2px);
    pointer-events: none;
}

.logo-background img {
    width: 500px;
    max-width: 90vw;
    height: auto;
}


        .cap-container img {
            max-width: 120px;
            height: auto;
        }

        @media print {
            .ttd-container {
                margin-top: 70px;
            }
            .cap-container {
                opacity: 0.3;
            }
        }
    </style>
</head>
<body>
    <div class="logo-background">
    <img src="../smk2.png" alt="Background Logo">
</div>

<div class="print-container">
    <div class="no-print" style="padding: 20px; background-color: #f4f4f4; border-bottom: 1px solid #ddd; margin-bottom: 2rem; border-radius: 5px;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h3>Filter Laporan</h3>
            <a href="index.php" class="btn btn-secondary btn-sm">Kembali ke Dashboard</a>
        </div>
        <hr style="margin: 1rem 0;">
        <form action="laporan.php" method="GET" class="filter-form">
            <select name="jurusan" class="form-control">
                <option value="semua" <?php if($filter_jurusan == 'semua') echo 'selected'; ?>>Semua Jurusan</option>
                <option value="Administrasi Perkantoran" <?php if($filter_jurusan == 'Administrasi Perkantoran') echo 'selected'; ?>>Administrasi Perkantoran</option>
                <option value="Teknik Kendaraan Ringan (Automotif)" <?php if($filter_jurusan == 'Teknik Kendaraan Ringan (Automotif)') echo 'selected'; ?>>Teknik Kendaraan Ringan</option>
                <option value="Teknik Komputer dan Jaringan" <?php if($filter_jurusan == 'Teknik Komputer dan Jaringan') echo 'selected'; ?>>Teknik Komputer dan Jaringan</option>
                <option value="Teknik Sepeda Motor" <?php if($filter_jurusan == 'Teknik Sepeda Motor') echo 'selected'; ?>>Teknik Sepeda Motor</option>
            </select>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <button onclick="window.print()" type="button" class="btn btn-success btn-sm">Cetak Laporan</button>
        </form>
    </div>

  <!-- Kop Surat -->
<div class="print-header" style="margin-bottom: 1rem; align-items: center; ">
    <div style="display: flex; align-items: center; gap: 15px;">
        <img src="../smk2.png" alt="Logo Sekolah" style="width: 80px; height: auto;">
        <div style="text-align: left;">
            <h1 style="margin: 0;">SMKS Bina Satria</h1>
            <div style="font-size: 14px;">
                Jl. Marelan Raya Ps. I Rel No.1, Tanah Enam Ratus, Kec. Medan Marelan, Kota Medan,<br>
                Sumatera Utara 20245<br>
                No. Telepon: 0616853280 | Email: info@smksbinasatria.sch.id
            </div>
        </div>
    </div>
    <hr style="margin-top: 10px; margin-bottom: 10px;">
</div>





    <!-- Statistik -->
    <div class="statistik">
        <p><strong>Total Pendaftar:</strong> <?php echo $total_all; ?> siswa</p>
        <p><strong>Diterima:</strong> <?php echo $total_diterima; ?> siswa</p>
        <p><strong>Ditolak:</strong> <?php echo $total_ditolak; ?> siswa</p>
    </div>

    <!-- Tabel utama hanya Diterima -->
    <h3>Daftar Siswa Diterima</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NISN</th>
                <th>JK</th>
                <th>Jurusan</th>
                <th>Asal Sekolah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_utama && $result_utama->num_rows > 0): ?>
                <?php $no = 1; while($row = $result_utama->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                    <td><?php echo substr($row['jenis_kelamin'], 0, 1); ?></td>
                    <td><?php echo htmlspecialchars($row['jurusan_pilihan']); ?></td>
                    <td><?php echo htmlspecialchars($row['asal_sekolah']); ?></td>
                    <td><?php echo htmlspecialchars($row['status_pendaftaran']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Tidak ada siswa diterima.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tabel Cadangan & Ditolak -->
    <h3 style="margin-top: 40px;">Daftar Cadangan</h3>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Lengkap</th>
                <th>NISN</th>
                <th>JK</th>
                <th>Jurusan</th>
                <th>Asal Sekolah</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_cadangan && $result_cadangan->num_rows > 0): ?>
                <?php $no = 1; while($row = $result_cadangan->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nama_lengkap']); ?></td>
                    <td><?php echo htmlspecialchars($row['nisn']); ?></td>
                    <td><?php echo substr($row['jenis_kelamin'], 0, 1); ?></td>
                    <td><?php echo htmlspecialchars($row['jurusan_pilihan']); ?></td>
                    <td><?php echo htmlspecialchars($row['asal_sekolah']); ?></td>
                    <td>Cadangan</td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center">Tidak ada data cadangan atau ditolak.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Tanda Tangan -->
    <div class="ttd-container">
        <div class="tanggal-ttd">
            <p>Medan, <?php echo date('d F Y'); ?></p>
        </div>
        <div class="ttd-left">
            <p>Panitia Pelaksana,</p>
            <br><br><br>
            <p>_______________________</p>
        </div>
        <div class="ttd-right">
            <p>Kepala Sekolah,</p>
            <div class="cap-container">
                <img src="../assets/images/smk2.png" alt="Cap Sekolah">
            </div>
            <br><br><br>
            <p>_______________________</p>
        </div>
    </div>
</div>

<?php
$stmt_utama->close();
$stmt_cadangan->close();
?>

</body>
</html>
