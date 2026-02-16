<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $id_dipendente = $_GET['id_dipendente'] ?? null;
    
    if (!$id_dipendente) {
        throw new Exception('ID Dipendente non fornito');
    }
    
    // Query per ottenere i cantieri assegnati al dipendente
    $sql = "SELECT 
                adc.id,
                adc.id_dipendente,
                adc.id_cantiere,
                adc.ruolo_cantiere,
                adc.ore_previste,
                adc.data_inizio,
                adc.data_fine,
                c.id as cantiere_id,
                c.nome as nome_cantiere,
                c.indirizzo as indirizzo_cantiere
            FROM assegnazioni_dipendenti_cantiere adc
            INNER JOIN cantieri c ON adc.id_cantiere = c.id
            WHERE adc.id_dipendente = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione query: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $id_dipendente);
    
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