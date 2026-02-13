<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT 
            id,
            nome,
            indirizzo,
            referente,
            giorni_lavoro,
            data_inizio,
            data_fine,
            lat,
            lng,
            note,
            stato,
            coordinatore_sicurezza,
            piano_sicurezza,
            numero_operai,
            created_at,
            updated_at
            FROM cantieri
            ORDER BY data_inizio DESC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception("Errore query: " . $conn->error);
    }

    $cantieri = [];
    while ($row = $result->fetch_assoc()) {
        $cantieri[] = $row;
    }

    // Se non è una richiesta AJAX (non ha header JSON), ritorna array semplice
    // altrimenti ritorna success: true
    if (isset($_GET['format']) && $_GET['format'] === 'api') {
        echo json_encode([
            "success" => true,
            "data" => $cantieri,
            "total" => count($cantieri)
        ]);
    } else {
        // Per compatibilità con il vecchio codice che si aspetta array semplice
        echo json_encode($cantieri);
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>