<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

// Solo utenti loggati
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
    exit;
}

require_once __DIR__ . '/../backend/db.php';
$conn->set_charset("utf8mb4");

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$userId = $_SESSION['user_id'];

// ─── AGGIORNA NOME ED EMAIL ───────────────────────────────────────────────────
if ($action === 'profilo') {
    $nome  = trim($input['nome']  ?? '');
    $email = trim($input['email'] ?? '');

    // Validazione email
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'error' => 'Email non valida']);
        exit;
    }

    // Controlla se l'email è già usata da un altro utente
    if ($email) {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check->bind_param("si", $email, $userId);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'error' => 'Email già in uso da un altro account']);
            exit;
        }
        $check->close();
    }

    $stmt = $conn->prepare("UPDATE users SET nome = ?, email = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ssi", $nome, $email, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();

// ─── CAMBIA PASSWORD ──────────────────────────────────────────────────────────
} elseif ($action === 'password') {
    $pwdAttuale = $input['pwd_attuale'] ?? '';
    $pwdNuova   = $input['pwd_nuova']   ?? '';

    if (strlen($pwdNuova) < 8) {
        echo json_encode(['success' => false, 'error' => 'La password deve essere di almeno 8 caratteri']);
        exit;
    }

    // Recupera hash attuale
    $stmt = $conn->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row || !password_verify($pwdAttuale, $row['password_hash'])) {
        echo json_encode(['success' => false, 'error' => 'La password attuale non è corretta']);
        exit;
    }

    $nuovoHash = password_hash($pwdNuova, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("si", $nuovoHash, $userId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();

} else {
    echo json_encode(['success' => false, 'error' => 'Azione non riconosciuta']);
}

$conn->close();
?>