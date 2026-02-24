<?php
// api/manutenzioni/get.php
// Recupera lo storico di manutenzioni di un mezzo
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

header('Content-Type: application/json; charset=utf-8');

try {
    $id_mezzo = isset($_GET['id_mezzo']) ? (int)$_GET['id_mezzo'] : null;
    
    if (!$id_mezzo) {
        throw new Exception("ID mezzo non fornito");
    }

    $query = "
        SELECT 
            id,
            id_mezzo,
            tipo_manutenzione,
            descrizione,
            data_manutenzione,
            prossima_scadenza,
            costo,
            ore_lavoro,
            fornitore,
            completata,
            note_tecniche
        FROM manutenzioni_mezzo
        WHERE id_mezzo = ?
        ORDER BY data_manutenzione DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_mezzo);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $manutenzioni = [];
    
    while ($row = $result->fetch_assoc()) {
        $manutenzioni[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $manutenzioni]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>