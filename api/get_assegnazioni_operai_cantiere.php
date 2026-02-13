<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $id_cantiere = $_GET['id_cantiere'] ?? null;
    
    if (!$id_cantiere) {
        throw new Exception('ID Cantiere non fornito');
    }
    
    // Query per ottenere le assegnazioni dipendenti al cantiere
    $sql = "SELECT 
                adc.id,
                adc.id_dipendente,
                adc.id_cantiere,
                adc.ruolo_cantiere,
                adc.ore_previste,
                adc.data_inizio,
                adc.data_fine,
                d.id as dipendente_id,
                d.nome as nome_dipendente,
                d.cognome as cognome_dipendente
            FROM assegnazioni_dipendenti_cantiere adc
            LEFT JOIN dipendenti d ON adc.id_dipendente = d.id
            WHERE adc.id_cantiere = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione query: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $id_cantiere);
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'esecuzione query: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $data = [];
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>