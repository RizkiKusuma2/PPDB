<?php
// File: index.php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMKS Bina Satria - PPDB Online</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        /* ======== Gallery ======== */
        body .main {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: url('assets/images/backgroundweb.png') no-repeat center center fixed;
            background-size: cover;
        }   
        .main h1 {
            color: #ffffff;
        }
        .gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 kolom tetap */
            gap: 20px;
            padding: 20px;
            background: url('assets/images/backgroundweb.png') no-repeat center center fixed;
            background-size: cover;
        }


        .gallery figure {
            margin: 0;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .gallery figure:hover {
            transform: scale(1.03);
        }

        .gallery img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery figcaption {
            padding: 10px;
            background: #fff;
            text-align: center;
        }

        /* ======== Modal Popup ======== */
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-content {
            background: #fff;
            border-radius: 10px;
            max-width: 1100px; /* Lebih besar */
            width: 95%;
            height: 80vh; /* Lebih tinggi */
            display: flex;
            flex-direction: row;
            overflow: hidden;
            position: relative;
        }

        .modal-content img {
            width: 60%;          /* Lebih besar */
            height: 100%;        /* Isi penuh */
            object-fit: cover;
        }

        .modal-caption {
            width: 40%;
            padding: 30px;       /* Lebih lebar padding */
            font-size: 1.1rem;   /* Perbesar font */
            line-height: 1.6;
            overflow-y: auto;    /* Scroll jika teks panjang */
        }


        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: #f00;
            color: #fff;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .modal-content {
                flex-direction: column;
            }
            .modal-content img,
            .modal-caption {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .modal-content {
                flex-direction: column;
                height: auto;
                max-height: 90vh;
            }
            .modal-content img,
            .modal-caption {
                width: 100%;
                height: auto;
            }
        }

    </style>
</head>
<body class="public-page">

<?php include '_header.php'; ?>

<main class="main">
    <h1>Galeri Sekolah</h1>

    <div class="gallery">
        <figure data-caption="SMK kami dilengkapi dengan fasilitas belajar modern, mulai dari ruang kelas nyaman, laboratorium komputer, bengkel praktik, hingga perpustakaan dan hotspot Wi-Fi gratis. Semua fasilitas dirancang untuk mendukung pembelajaran teori dan praktik agar siswa siap terjun ke dunia kerja.">
            <img src="assets/images/fasilitas1.jpg" alt="Fasilitas Belajar Lengkap">
            <figcaption>
                <h3>Fasilitas Belajar Lengkap</h3>
                <p>Ruang kelas yang nyaman dan multimedia.</p>
            </figcaption>
        </figure>

        <figure data-caption="Laboratorium komputer kami dilengkapi dengan perangkat terbaru dan koneksi internet cepat, mendukung praktik IT, desain grafis, hingga pemrograman. Siswa bisa belajar langsung dengan teknologi terkini untuk siap menghadapi dunia digital.">
            <img src="assets/images/fasilitas3.jpg" alt="Laboratorium Komputer">
            <figcaption>
                <h3>Laboratorium Komputer</h3>
                <p>PC modern dan koneksi internet cepat.</p>
            </figcaption>
        </figure>

        <figure data-caption="Bengkel otomotif kami dilengkapi dengan peralatan modern, ruang praktik luas, dan kendaraan uji yang siap mendukung pembelajaran siswa. Di sini, siswa belajar langsung merawat dan memperbaiki kendaraan sesuai standar industri.">
            <img src="assets/images/fasilitas4.png" alt="Bengkel Otomotif">
            <figcaption>
                <h3>Bengkel Otomotif</h3>
                <p>Praktikum otomotif dengan alat lengkap.</p>
            </figcaption>
        </figure>

        <figure data-caption="Selamat Kepada Siswa-Siswi Terbaik SMKS BINA SATRIA MEDAN Tahun Ajaran 2023-2024
          “Teruslah Melangkah Meski Itu Pelan, karena dengan melangkah akan menjadikan kita semakin dekat dengan tujuan dan prestasi yang diinginkan”">
            <img src="assets/images/fasilitas2.jpg" alt="Berita">
            <figcaption>
                <h3>Berita</h3>
                <p>Penyerahan Penghargaan Kepada Siswa-Siswi Terbaik SMKS BINA SATRIA MEDAN</p>
            </figcaption>
        </figure>
        <figure data-caption="SMK Bina Satria bersama Indibiz Medan & Indibiz Regional Sumatera sukses menggelar Workshop Digital Branding bertajuk 'Pemanfaatan Sosial Media Sebagai Media Branding Sekolah'. Kegiatan ini menjadi langkah nyata dalam meningkatkan kreativitas dan kemampuan memaksimalkan media sosial untuk citra sekolah.">
            <img src="assets/images/fasilitas5.png" alt="Workshop">
            <figcaption>
                <h3>Workshop</h3>
                <p>Workshop Digital Branding di SMK Bina Satria bersama Indibiz Sumatera dan indibizmedan</p>
            </figcaption>
        </figure>
        <figure data-caption="Hari Pendidikan Nasional (Hardiknas) merupakan salah satu hari penting bagi bangsa Indonesia. Lantas, Hari Pendidikan Nasional diperingati tanggal berapa?
                              Mengutip laman resmi Kemdikbud RI, Hari Pendidikan Nasional (Hardiknas) diperingati setiap tanggal 2 Mei. Tanggal tersebut merupakan hari kelahiran Ki Hajar Dewantara sebagai Bapak Pendidikan Nasional Indonesia.">
            <img src="assets/images/fasilitas6.jpg" alt="Artikel">
            <figcaption>
                <h3>Artikel</h3>
                <p>Memperingati Hari Pendidikan Nasional (Hardiknas)</p>
            </figcaption>
        </figure>
        <figure data-caption="Berbekal sapu, pengki, dan semangat gotong royong, seluruh pengurus OSIS bahu-membahu menggelar kegiatan kerja bakti membersihkan lingkungan sekolah.

              Kegiatan ini diinisiasi oleh OSIS yang bertujuan untuk menumbuhkan rasa tanggung jawab serta kesadaran para siswa terhadap kebersihan lingkungan sekitar.

              Dengan arahan PEMBINA OSIS , pengurus dan anggota dibagi menjadi beberapa kelompok kecil, masing-masing dengan tugas berbeda. Ada yang sibuk menyapu dedaunan kering di halaman, membersihkan mushollah, membersihkan debu di ruang kelas, hingga menyiram tanaman agar kembali subur.">
            <img src="assets/images/fasilitas7.jpg" alt="Berita">
            <figcaption>
                <h3>Berita</h3>
                <p>Gotong Royong Membersihkan Lingkungan Sekolah oleh OSIS SMKS BINA SATRIA MEDAN</p>
            </figcaption>
        </figure>
        
        <figure data-caption="Sosialisasi Keselamatan Berkendara bersama Kepolisian (POLDA), Jasa Raharja, MAXIM (Transportasi Online). Kepolisian (POLDA), Jasa Raharja, MAXIM (Transportasi Online) memberikan perlindungan dan peningkatan keselamatan berlalu lintas kepada para pengguna jalan raya terkhusus para pelajar. Sosialisasi safety riding yang digelar Kepolisian (POLDA), Jasa Raharja, MAXIM (Transportasi Online) di SMKS BINA SATRIA MEDAN. program ini akan lebih efektif dan mudah dicerna dengan penyampaian pesan-pesan keselamatan kepada generasi muda dengan gaya terkini.

“Jadilah pengemudi yang patuh, berkendaralah dengan aman.”">
            <img src="assets/images/fasilitas8.jpg" alt="Artikel">
            <figcaption>
                <h3>Artikel</h3>
                <p>Sosialisasi Keselamatan Berkendara bersama Kepolisian (POLDA), Jasa Raharja, MAXIM (Transportasi Online)</p>
            </figcaption>
        </figure>

        
    </div>
</main>



<!-- Modal -->
<div class="modal" id="imageModal">
    <div class="modal-content">
        <button class="close-btn" id="closeModal">&times; Tutup</button>
        <img id="modalImage" src="" alt="">
        <div class="modal-caption" id="modalCaption"></div>
    </div>
</div>

<script>
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    const closeModal = document.getElementById('closeModal');

    document.querySelectorAll('.gallery figure').forEach(figure => {
        figure.addEventListener('click', () => {
            const img = figure.querySelector('img');
            modalImg.src = img.src;
            modalCaption.innerText = figure.getAttribute('data-caption');
            modal.style.display = 'flex';
        });
    });

    closeModal.addEventListener('click', () => {
        modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>

</body>
</html>
