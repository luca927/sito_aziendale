<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $sql = "SELECT 
            c.id,
            c.nome,
            c.indirizzo,
            c.referente,
            c.giorni_lavoro,
            c.data_inizio,
            c.data_fine,
            c.note,
            c.coordinatore_sicurezza,
            c.piano_sicurezza,
            c.stato,
            c.lat,
            c.lng,
            c.created_at,
            c.updated_at,
            COALESCE(
                GROUP_CONCAT(
                    DISTINCT CONCAT(d.nome, ' ', d.cognome)
                    SEPARATOR ', '
                ),
                ''
            ) AS operai
        FROM cantieri c
        LEFT JOIN assegnazioni_dipendenti_cantiere adc 
            ON c.id = adc.id_cantiere
        LEFT JOIN dipendenti d 
            ON d.id = adc.id_dipendente
        GROUP BY c.id
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

