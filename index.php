
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
</head>
<body class="public-page">

    <?php include '_header.php'; ?>
 <div style="background-color: #f03c0aff; padding: 10px 0; color: #fff; font-weight: bold; font-size: 1rem;">
    <marquee behavior="scroll" direction="left" scrollamount="6">
        ⚠️ Peringatan: Pastikan Anda mengisi data dengan benar dan sesuai dokumen resmi. Kesalahan data dapat menghambat proses pendaftaran.
    </marquee>
</div>

    <main>
        <!-- Hero Section - Added as it was defined in CSS but missing in HTML -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Selamat Datang di penerimaan siswa baru Online SMKS Bina Satria</h1>
                    <p>Daftarkan diri Anda sekarang dan bergabunglah dengan sekolah kejuruan unggulan yang siap mencetak generasi profesional.</p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <a href="register.php" class="btn btn-primary btn-lg">Daftar Sekarang</a>
                    <?php endif; ?>

                </div>
            </div>
        </section>


        <<section id="profil" class="content-section" style="background: linear-gradient(135deg, #a8e6cf, #dcedc1); padding: 50px 0;">">

    <div class="container">
        <h2>Profil & Sejarah SMKS Bina Satria</h2>
        <div class="profil-content">
            <!-- Slideshow -->
            <div class="slideshow-container">
                <div class="slide active">
                    <img src="assets/images/gedungsmk.jpg" alt="Gedung SMKS Bina Satria 1">
                </div>
                <div class="slide">
                    <img src="assets/images/fasilitas1.jpg" alt="Gedung SMKS Bina Satria 2">
                </div>
                <div class="slide">
                    <img src="assets/images/fasilitas3.jpg" alt="Gedung SMKS Bina Satria 3">
                </div>
                <div class="slide">
                    <img src="assets/images/fasilitas4.png" alt="Gedung SMKS Bina Satria 3">
                </div>

                <!-- Tombol navigasi -->
                <a class="prev">&#10094;</a>
                <a class="next">&#10095;</a>

                <!-- Pagination dots -->
                <div class="pagination">
                    <span class="dot" data-slide="0"></span>
                    <span class="dot" data-slide="1"></span>
                    <span class="dot" data-slide="2"></span>
                    <span class="dot" data-slide="3"></span>
                </div>
            </div>


            <!-- Text Profil -->
            <div class="profil-text">
                <p>Pada era 1960-an hingga 1970-an, Sekolah Teknik Menengah (STM) menjadi pilihan favorit karena lulusannya mudah mendapatkan pekerjaan sekaligus dapat melanjutkan ke Perguruan Tinggi. Menjawab tantangan tersebut dan keterbatasan jumlah STM saat itu, Bapak Ir. Ali Sugiono dan Ibu Hj. Tariani, diprakarsai oleh putra-putri mereka, mendirikan Yayasan Perguruan Bina Satria.</p>
                <p>Setelah menemukan lokasi yang strategis di Marelan, Yayasan Perguruan Bina Satria resmi dibangun pada tahun 1987 dan mulai menerima siswa baru pada Tahun Pelajaran 1988/1989 untuk unit STM, SMEA, dan SMP. Kami berkomitmen untuk membantu program pemerintah dalam mencerdaskan kehidupan bangsa dan membentuk manusia pembangunan yang ber-Pancasila.</p>
                <p><strong>Visi Kami:</strong> Mengembangkan potensi anak secara menyeluruh dan seimbang sesuai dengan minat, kebutuhan, serta tingkat perkembangan dan kemampuan mereka untuk melanjutkan program sekolah yang telah berjalan dengan baik.</p>
            </div>
        </div>
    </div>
</section>

        <section id="jurusan" class="content-section bg-light"  style="background: linear-gradient(135deg, #a8e6cf, #dcedc1); padding: 50px 0;">
            <div class="container">
                <h2>Program Keahlian Unggulan</h2>
                <p class="section-subtitle">Kami menyediakan program keahlian yang relevan dengan kebutuhan industri saat ini.</p>
                <div class="jurusan-grid">
                    <div class="jurusan-card">
                        <h3>Teknik Kendaraan Ringan (TKR)</h3>
                        <p>Mempelajari perawatan dan perbaikan komponen-komponen mobil secara konvensional maupun dengan teknologi terbaru.</p>
                    </div>
                    <div class="jurusan-card">
                        <h3>Teknik & Bisnis Sepeda Motor (TBSM)</h3>
                        <p>Menjadi ahli dalam perawatan, perbaikan, dan manajemen bisnis di industri sepeda motor yang terus berkembang.</p>
                    </div>
                    <div class="jurusan-card">
                        <h3>Teknik Komputer & Jaringan (TKJ)</h3>
                        <p>Menguasai instalasi, perbaikan, dan pengelolaan jaringan komputer skala kecil hingga besar untuk mendukung dunia digital.</p>
                    </div>
                    <div class="jurusan-card">
                        <h3>Manajemen Perkantoran & Layanan Bisnis (MPLB)</h3>
                        <p>Mempersiapkan tenaga ahli di bidang administrasi perkantoran modern dan layanan bisnis yang profesional.</p>
                    </div>
                    
                </div>
            </div>
        </section>
    </main>

    <?php include '_footer.php'; ?>
    <script>
let slideIndex = 0;
const slides = document.querySelectorAll(".slide");
const dots = document.querySelectorAll(".dot");
const prev = document.querySelector(".prev");
const next = document.querySelector(".next");

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.toggle("active", i === index);
        dots[i].classList.toggle("active", i === index);
    });
    slideIndex = index;
}

function nextSlide() {
    showSlide((slideIndex + 1) % slides.length);
}

function prevSlide() {
    showSlide((slideIndex - 1 + slides.length) % slides.length);
}

next.addEventListener("click", nextSlide);
prev.addEventListener("click", prevSlide);

dots.forEach((dot, i) => {
    dot.addEventListener("click", () => showSlide(i));
});

// Auto-slide setiap 3 detik
setInterval(nextSlide, 3000);

// Tampilkan slide pertama
showSlide(slideIndex);
</script>

</body>
</html>