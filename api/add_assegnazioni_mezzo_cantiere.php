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
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    $id_mezzo = intval($data['id_mezzo'] ?? 0);
    $id_cantiere = intval($data['id_cantiere'] ?? 0);
    $id_dipendente = !empty($data['id_dipendente']) ? intval($data['id_dipendente']) : null;
    $ora_inizio = $data['ora_inizio'] ?? null;
    $km_inizio = !empty($data['km_inizio']) ? floatval($data['km_inizio']) : null;
    $note = $data['note'] ?? null;
    
    if (!$id_mezzo || !$id_cantiere) {
        throw new Exception('Mezzo e Cantiere sono obbligatori');
    }
    
    $sql = "INSERT INTO assegnazioni_mezzo_cantiere 
            (id_mezzo, id_cantiere, id_dipendente, ora_inizio, km_inizio, note) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore query: ' . $conn->error);
    }
    
    $stmt->bind_param('iiisds', $id_mezzo, $id_cantiere, $id_dipendente, $ora_inizio, $km_inizio, $note);
    
    if (!$stmt->execute()) {
        throw new Exception('Errore: ' . $stmt->error);
    }
    
    echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>