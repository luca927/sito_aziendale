<?php
require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json');

// Leggo il JSON
$input = json_decode(file_get_contents('php://input'), true);

// Estraggo i campi
$dipendente_id = $input['dipendente_id'] ?? null;
$cantiere_id   = $input['cantiere_id'] ?? null;
$mezzo_id = !empty($input['mezzo_id']) ? (int)$input['mezzo_id'] : null;
$data_attivita = $input['data_attivita'] ?? null;
// Recupero coordinate del cantiere

$stmtCant = $conn->prepare("SELECT lat, lng FROM cantieri WHERE id = ?");
$stmtCant->bind_param("i", $cantiere_id);
$stmtCant->execute();
$resultCant = $stmtCant->get_result();
$cantiere = $resultCant->fetch_assoc();

if (!$cantiere) {
    echo json_encode(['success' => false, 'error' => 'Cantiere non trovato']);
    exit;
}

$lat = $cantiere['lat'];
$lng = $cantiere['lng'];

$stmtCant->close();

// Controllo campi obbligatori
if (!$dipendente_id || !$cantiere_id || !$data_attivita) {
    echo json_encode(['success' => false, 'error' => 'Campi obbligatori mancanti']);
    exit;
}

try {

    // --- VERSIONE MYSQLI ---
    $stmt = $conn->prepare("
        INSERT INTO tracciamenti
        (dipendente_id, cantiere_id, mezzo_id, lat, lng, data_attivita)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiidds",
        $dipendente_id,
        $cantiere_id,
        $mezzo_id,
        $lat,
        $lng,
        $data_attivita
    );

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'id' => $conn->insert_id
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'error' => $stmt->error,
            'sql_error' => $conn->error
        ]);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>