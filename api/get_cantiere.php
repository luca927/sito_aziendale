<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    // Usiamo la subquery che si è rivelata più affidabile nel tuo ambiente
    $sql = "SELECT c.*, 
            (SELECT GROUP_CONCAT(DISTINCT CONCAT(d.nome, ' ', d.cognome) SEPARATOR ', ') 
             FROM assegnazioni_dipendenti_cantiere adc 
             JOIN dipendenti d ON adc.id_dipendente = d.id 
             WHERE adc.id_cantiere = c.id) as operai
            FROM cantieri c
            ORDER BY c.data_inizio DESC";

    $result = $conn->query($sql);
    
    if (!$result) {
        throw new Exception('Errore nell\'esecuzione query: ' . $conn->error);
    }
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode($data);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>