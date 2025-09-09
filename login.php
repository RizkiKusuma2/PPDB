<?php
// File: login.php
require_once 'config.php';

// Jika sudah login, redirect ke halaman utama
if (isset($_SESSION['user_id'])) {
    redirect('index.php');
}

$error_message = '';
$role_selected = $_GET['role'] ?? ''; // Ambil role dari query string

// Proses login jika form dikirim POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = escape($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ? AND role = ?");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Password langsung dibandingkan, bisa diganti ke password_verify jika pakai hash
        if ($password === $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            redirect('index.php');
        } else {
            $error_message = "Password yang Anda masukkan salah.";
            $role_selected = $role; // supaya form yg tampil sesuai role
        }
    } else {
        $error_message = "Username atau role tidak ditemukan.";
        $role_selected = $role;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login - PPDB SMKS Bina Satria</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css?family=Montserrat:400,800&display=swap" rel="stylesheet">

<!-- FontAwesome untuk icon sosial -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

<style>
* {
	box-sizing: border-box;
}

body {
	background: #93DA97;
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	font-family: 'Montserrat', sans-serif;
	height: 100vh;
	margin: 0;
}

h1 {
	font-weight: bold;
	margin: 0;
}

p {
	font-size: 14px;
	font-weight: 100;
	line-height: 20px;
	letter-spacing: 0.5px;
	margin: 20px 0 30px;
	text-align: center;
	color: #333;
}

a {
	color: #007bff;
	font-size: 14px;
	text-decoration: none;
}

a:hover {
	text-decoration: underline;
}

button {
	border-radius: 20px;
	border: 1px solid #3E5F44;
	background-color: #3E5F44;
	color: #FFFFFF;
	font-size: 12px;
	font-weight: bold;
	padding: 12px 45px;
	letter-spacing: 1px;
	text-transform: uppercase;
	cursor: pointer;
	transition: transform 80ms ease-in;
}

button:active {
	transform: scale(0.95);
}

button:focus {
	outline: none;
}

button.ghost {
	background-color: transparent;
	border-color: #FFFFFF;
	color: white;
}

form {
	background-color: #FFFFFF;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 0 50px;
	height: 100%;
	text-align: center;
}

input {
	background-color: #eee;
	border: none;
	padding: 12px 15px;
	margin: 8px 0;
	width: 100%;
}

.container {
	background-color: #fff;
	border-radius: 10px;
  	box-shadow: 0 14px 28px rgba(0,0,0,0.25), 
			0 10px 10px rgba(0,0,0,0.22);
	position: relative;
	overflow: hidden;
	width: 768px;
	max-width: 100%;
	min-height: 480px;
}

.form-container {
	position: absolute;
	top: 0;
	height: 100%;
	transition: all 0.6s ease-in-out;
}

.sign-in-container {
	left: 0;
	width: 50%;
	z-index: 2;
}

.container.right-panel-active .sign-in-container {
	transform: translateX(100%);
}

.sign-up-container {
	left: 0;
	width: 50%;
	opacity: 0;
	z-index: 1;
	pointer-events: none;
}

.container.right-panel-active .sign-up-container {
	transform: translateX(100%);
	opacity: 1;
	z-index: 5;
	pointer-events: auto;
	animation: show 0.6s;
}

@keyframes show {
	0%, 49.99% {
		opacity: 0;
		z-index: 1;
		pointer-events: none;
	}
	
	50%, 100% {
		opacity: 1;
		z-index: 5;
		pointer-events: auto;
	}
}

.overlay-container {
	position: absolute;
	top: 0;
	left: 50%;
	width: 50%;
	height: 100%;
	overflow: hidden;
	transition: transform 0.6s ease-in-out;
	z-index: 100;
}

.container.right-panel-active .overlay-container{
	transform: translateX(-100%);
}

.overlay {
	background: linear-gradient(to right, #5E936C, #aeec82ff);
	color: #FFFFFF;
	position: relative;
	left: -100%;
	height: 100%;
	width: 200%;
  	transform: translateX(0);
	transition: transform 0.6s ease-in-out;
	display: flex;
}

.container.right-panel-active .overlay {
  	transform: translateX(50%);
}

.overlay-panel {
	position: absolute;
	display: flex;
	align-items: center;
	justify-content: center;
	flex-direction: column;
	padding: 0 40px;
	text-align: center;
	top: 0;
	height: 100%;
	width: 50%;
	transform: translateX(0);
	transition: transform 0.6s ease-in-out;
}

.overlay-left {
	transform: translateX(-20%);
}

.container.right-panel-active .overlay-left {
	transform: translateX(0);
}

.overlay-right {
	right: 0;
	transform: translateX(0);
}

.container.right-panel-active .overlay-right {
	transform: translateX(20%);
}

.social-container {
	margin: 20px 0;
}

.social-container a {
	border: 1px solid #DDDDDD;
	border-radius: 50%;
	display: inline-flex;
	justify-content: center;
	align-items: center;
	margin: 0 5px;
	height: 40px;
	width: 40px;
}

.alert {
	background-color: #ffdddd;
	border-left: 6px solid #f44336;
	padding: 10px 20px;
	margin: 20px 0;
	color: #a94442;
	border-radius: 5px;
	text-align: center;
	font-weight: 600;
}

/* Footer */
footer {
    background-color: #222;
    color: #fff;
    font-size: 14px;
    bottom: 0;
    position: fixed;
    left: 0;
    right: 0;
    text-align: center;
    z-index: 999;
}

footer p {
    margin: 10px 0;
}

footer i {
    color: red;
}

footer a {
    color: #3c97bf;
    text-decoration: none;
}

</style>

</head>
<body>

<div class="container <?php if($role_selected === 'admin') echo 'right-panel-active'; ?>" id="container">

	<!-- Form Sign Up (untuk contoh, kosongkan atau hapus jika tidak dipakai) -->
	<div class="form-container sign-up-container">
		<form action="#" method="POST" autocomplete="off">
			<h1>Create Account</h1>
			
			<span>or use your email for registration</span>
			<input type="text" placeholder="Name" disabled />
			<input type="email" placeholder="Email" disabled />
			<input type="password" placeholder="Password" disabled />
			<button disabled>Sign Up</button>
		</form>
	</div>

	<!-- Form Login Siswa -->
<!-- Form Login Siswa -->
<div class="form-container sign-in-container">
    <form action="login.php?role=siswa" method="POST" autocomplete="off">
        <!-- Tambah logo di atas -->
        <a href="index.php" style="display: inline-block; margin-bottom: 20px;">
            <img src="assets/images/smk2.png" alt="Logo Sekolah" style="width: 100px; cursor: pointer;">
        </a>

        <h1>Login Siswa</h1>
        
        <?php if ($role_selected === 'siswa' && $error_message): ?>
            <div class="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <input type="hidden" name="role" value="siswa" />
        <input type="text" name="username" placeholder="NISN Siswa" required autofocus />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        
        <!-- Tambahan teks bantuan -->
        <p style="font-size: 13px; margin-top: 15px; color: #555;">
            Apabila terjadi kendala, hubungi admin.<br>
             <a href="mailto:info@smksbinasatria.sch.id" style="color:#007bff;">info@smksbinasatria.sch.id</a>
        </p>

        <p style="font-size: 13px; margin-top: 10px;">
            Belum punya akun? <a href="register.php">Daftar sebagai calon siswa</a>
        </p>
    </form>
</div>

	<!-- Overlay panel untuk toggle login admin -->
	<div class="overlay-container">
		<div class="overlay">
			<div class="overlay-panel overlay-left">
				<h1>Login Siswa</h1>
				<p>Untuk login siswa, gunakan data pribadi Anda.</p>
				<button class="ghost" id="signIn">Login Siswa</button>
			</div>
			<div class="overlay-panel overlay-right">
				<h1>Login Admin</h1>
				<p>Masukkan data admin untuk akses panel.</p>
				<button class="ghost" id="signUp">Login Admin</button>
			</div>
		</div>
	</div>

	<!-- Form Login Admin -->
	<div class="form-container sign-up-container admin-login-form" style="top:0; left:0; width: 50%; height: 100%; position: absolute; z-index: 5; background: #fff; padding: 0 50px; display:none;">
    <form action="login.php?role=admin" method="POST" autocomplete="off">
        <!-- Tambah logo di atas -->
        <a href="index.php" style="display: inline-block; margin-bottom: 20px;">
            <img src="assets/images/smk2.png" alt="Logo Sekolah" style="width: 100px; cursor: pointer;">
        </a>

        <h1>Login Admin</h1>
        <?php if ($role_selected === 'admin' && $error_message): ?>
            <div class="alert"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <input type="hidden" name="role" value="admin" />
        <input type="text" name="username" placeholder="Username Admin" required />
        <input type="password" name="password" placeholder="Password" required />
        <button type="submit">Login</button>
        
        <!-- Tambahan teks bantuan untuk admin juga -->
        
    </form>
</div>


<script>
// Script toggle panel kanan-kiri
const container = document.getElementById('container');
const signUpButton = document.getElementById('signUp');
const signInButton = document.getElementById('signIn');

const adminLoginForm = document.querySelector('.admin-login-form');
const signUpContainer = document.querySelector('.sign-up-container'); // form create account (disabled)

signUpButton.addEventListener('click', () => {
	container.classList.add("right-panel-active");
	adminLoginForm.style.display = 'block';
	signUpContainer.style.display = 'none';
});

signInButton.addEventListener('click', () => {
	container.classList.remove("right-panel-active");
	adminLoginForm.style.display = 'none';
	signUpContainer.style.display = 'block';
});

// Saat halaman dimuat, cek role untuk tampilkan form sesuai
window.addEventListener('DOMContentLoaded', () => {
	if(container.classList.contains('right-panel-active')) {
		adminLoginForm.style.display = 'block';
		signUpContainer.style.display = 'none';
	} else {
		adminLoginForm.style.display = 'none';
		signUpContainer.style.display = 'block';
	}
});
</script>

</body>
</html>
