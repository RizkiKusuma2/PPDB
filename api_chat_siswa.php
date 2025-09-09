
<?php
// File: api_chat_siswa.php
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'siswa') {
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

$user_id = $_SESSION['user_id'];
$action = $_REQUEST['action'] ?? null;

switch ($action) {
    case 'get_messages':
        $with_id = (int)($_GET['with_id'] ?? 0);
        if ($with_id === 0) {
            echo json_encode(['success' => false, 'message' => 'ID penerima tidak valid']);
            exit();
        }
        
        $stmt = $conn->prepare("SELECT * FROM chat WHERE (pengirim_id = ? AND penerima_id = ?) OR (pengirim_id = ? AND penerima_id = ?) ORDER BY waktu_kirim ASC");
        $stmt->bind_param("iiii", $user_id, $with_id, $with_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        echo json_encode(['success' => true, 'messages' => $messages]);
        break;

    case 'send_message':
        $penerima_id = (int)($_POST['penerima_id'] ?? 0);
        $pesan = trim($_POST['pesan'] ?? '');

        if ($penerima_id === 0 || empty($pesan)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            exit();
        }

        $stmt = $conn->prepare("INSERT INTO chat (pengirim_id, penerima_id, pesan) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $user_id, $penerima_id, $pesan);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengirim pesan']);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Aksi tidak dikenal']);
        break;
}
?>