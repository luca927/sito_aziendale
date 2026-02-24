<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Controlla se loggato
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Non autorizzato"]);
    exit;
}

// 2. Controlla se la sessione è scaduta (1 ora)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 3600) {
    session_destroy();
    http_response_code(401);
    echo json_encode(["error" => "Sessione scaduta"]);
    exit;
}
$_SESSION['last_activity'] = time();
require_once __DIR__ . '/../backend/db.php';
require_once __DIR__ . '/../backend/auth.php';
header('Content-Type: application/json');

// BLOCCO DI SICUREZZA: Solo gli admin possono eliminare
if (!isAdmin()) {
    http_response_code(403); // Forbidden
    echo json_encode([
        'success' => false, 
        'error' => 'Azione non autorizzata. Solo l\'amministratore può eliminare dati.'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'] ?? null;

if (!$id) {
    echo json_encode(['success' => false, 'error' => 'ID mancante']);
    exit;
}

// Eliminazione sicura dalla tabella dashboard
$stmtDash = $conn->prepare("DELETE FROM dashboard WHERE dipendente_id = ?");
$stmtDash->bind_param("i", $id);
$stmtDash->execute();
$stmtDash->close();

// Eliminazione dalla tabella dipendenti
$stmt = $conn->prepare("DELETE FROM dipendenti WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {

        // REGISTRAZIONE LOG
        controlloAzioni(
            "ELIMINAZIONE_DIPENDENTE",
            "Eliminato dipendente ID: $id. Operazione eseguita da: " . $_SESSION['username']
        );

        echo json_encode(['success' => true]);

    } else {
        echo json_encode(['success' => false, 'error' => 'Dipendente non trovato']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Errore durante l\'eliminazione']);
}

$stmt->close();
$conn->close();
?>