<?php
// api/corsi_dipendente/get.php
// Recupera i corsi di un dipendente

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
            nome_corso,
            descrizione,
            ente_erogante,
            data_inizio_corso,
            data_completamento,
            ore_corso,
            costo,
            certificazione_numero,
            certificazione_rilasciata,
            voto_finale
        FROM corsi_dipendente
        WHERE id_dipendente = ?
        ORDER BY data_completamento DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_dipendente);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $corsi = [];
    
    while ($row = $result->fetch_assoc()) {
        $corsi[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $corsi]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>