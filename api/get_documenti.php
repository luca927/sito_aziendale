<?php
// api/documenti_dipendente/get.php
// Recupera i documenti di identità di un dipendente

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $id_dipendente = isset($_GET['id_dipendente']) ? (int)$_GET['id_dipendente'] : null;
    
    if (!$id_dipendente) {
        throw new Exception("ID dipendente non fornito");
    }

    $query = "
        SELECT 
            id,
            id_dipendente,
            tipo_documento,
            numero_documento,
            descrizione,
            data_rilascio,
            data_scadenza,
            rilasciato_da
        FROM documenti_dipendente
        WHERE id_dipendente = ?
        ORDER BY data_scadenza ASC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_dipendente);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $documenti = [];
    
    while ($row = $result->fetch_assoc()) {
        $documenti[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $documenti]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>