<?php
// File: admin/chat.php
require_once '../config.php';
check_login();
check_role('admin');

$admin_id = $_SESSION['user_id'];
$siswa_id = (int)($_GET['user_id'] ?? 0);

// Ambil daftar siswa yang pernah chat atau yang dipilih
$sql_siswa_list = "SELECT u.id, p.nama_lengkap 
                   FROM users u 
                   JOIN pendaftaran p ON u.id = p.user_id
                   WHERE u.role = 'siswa'
                   ORDER BY p.nama_lengkap ASC";
$siswa_list_result = $conn->query($sql_siswa_list);

$nama_siswa_chat = "Pilih Siswa";
if ($siswa_id > 0) {
    $stmt_nama = $conn->prepare("SELECT nama_lengkap FROM pendaftaran WHERE user_id = ?");
    $stmt_nama->bind_param("i", $siswa_id);
    $stmt_nama->execute();
    $nama_result = $stmt_nama->get_result();
    if ($nama_result->num_rows > 0) {
        $nama_siswa_chat = $nama_result->fetch_assoc()['nama_lengkap'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Admin - PPDB</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="dashboard-container">
    <?php include '_admin_nav.php'; ?>
    <main class="dashboard-content">
        <div class="admin-chat-layout">
            <div class="chat-user-list">
                <h3>Daftar Siswa</h3>
                <ul>
                    <?php while($siswa = $siswa_list_result->fetch_assoc()): ?>
                        <li class="<?php echo ($siswa['id'] == $siswa_id) ? 'active' : ''; ?>">
                            <a href="chat.php?user_id=<?php echo $siswa['id']; ?>">
                                <?php echo htmlspecialchars($siswa['nama_lengkap']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
            <div class="chat-main">
                <div class="chat-header">
                    <h3>Percakapan dengan: <?php echo htmlspecialchars($nama_siswa_chat); ?></h3>
                </div>
                <div class="chat-container">
                    <div class="chat-box" id="chat-box">
                        <?php if ($siswa_id === 0): ?>
                            <p class="text-center">Silakan pilih siswa dari daftar di sebelah kiri untuk memulai percakapan.</p>
                        <?php else: ?>
                            <p class="text-center">Memuat percakapan...</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($siswa_id > 0): ?>
                    <form id="chat-form" class="chat-input-area">
                        <input type="hidden" name="penerima_id" value="<?php echo $siswa_id; ?>">
                        <input type="text" name="pesan" id="pesan-input" placeholder="Ketik balasan Anda..." autocomplete="off" required>
                        <button type="submit">Kirim</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<?php if ($siswa_id > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script chat sama persis dengan chat_siswa.php, hanya URL API yang berbeda
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const pesanInput = document.getElementById('pesan-input');
    const myUserId = <?php echo $admin_id; ?>;
    const otherUserId = <?php echo $siswa_id; ?>;

    function fetchMessages() {
        fetch(`api_chat.php?action=get_messages&with_id=${otherUserId}`)
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML = '';
                if (data.success && data.messages.length > 0) {
                    data.messages.forEach(msg => {
                        const messageElement = document.createElement('div');
                        messageElement.classList.add('chat-message');
                        if (parseInt(msg.pengirim_id) === myUserId) {
                            messageElement.classList.add('sent');
                        } else {
                            messageElement.classList.add('received');
                        }
                        messageElement.innerHTML = `<p>${msg.pesan}</p><span class="chat-timestamp">${new Date(msg.waktu_kirim).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>`;
                        chatBox.appendChild(messageElement);
                    });
                } else {
                    chatBox.innerHTML = '<p class="text-center">Belum ada percakapan.</p>';
                }
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(chatForm);
        formData.append('action', 'send_message');

        fetch('api_chat.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pesanInput.value = '';
                fetchMessages();
            } else {
                alert('Gagal mengirim pesan: ' + data.message);
            }
        })
        .catch(error => console.error('Error sending message:', error));
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
});
</script>
<?php endif; ?>
</body>
</html>