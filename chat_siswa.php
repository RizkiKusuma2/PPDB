
<?php
// File: chat_siswa.php
require_once 'config.php';
check_login();
check_role('siswa');

$user_id = $_SESSION['user_id'];
// Admin diasumsikan memiliki user_id = 1
$admin_id = 1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan Admin - PPDB</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="public-page" >

<?php include '_header.php'; ?>

<main class="dashboard-page" style="background-image: url('assets/images/backgroundweb.png'); background-size: cover; background-position: center;">
    <div class="container">
        <div class="content-header">
            <h2>Chat dengan Admin</h2>
        </div>
        <div class="chat-container-wrapper">
            <div class="chat-box" id="chat-box">
                <!-- Pesan akan dimuat di sini oleh JavaScript -->
                <p class="text-center">Memuat percakapan...</p>
            </div>
            <form id="chat-form" class="chat-input-area">
                <input type="hidden" name="penerima_id" value="<?php echo $admin_id; ?>">
                <input type="text" name="pesan" id="pesan-input" placeholder="Ketik pesan Anda..." autocomplete="off" required>
                <button type="submit">Kirim</button>
            </form>
        </div>
    </div>
</main>

<?php include '_footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chat-box');
    const chatForm = document.getElementById('chat-form');
    const pesanInput = document.getElementById('pesan-input');
    const myUserId = <?php echo $user_id; ?>;

    function fetchMessages() {
        fetch('api_chat_siswa.php?action=get_messages&with_id=<?php echo $admin_id; ?>')
            .then(response => response.json())
            .then(data => {
                chatBox.innerHTML = ''; // Kosongkan box
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
                    chatBox.innerHTML = '<p class="text-center">Belum ada percakapan. Mulai obrolan!</p>';
                }
                chatBox.scrollTop = chatBox.scrollHeight;
            })
            .catch(error => console.error('Error fetching messages:', error));
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(chatForm);
        formData.append('action', 'send_message');

        fetch('api_chat_siswa.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pesanInput.value = ''; // Kosongkan input
                fetchMessages(); // Langsung refresh chat
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
</body>
</html>
