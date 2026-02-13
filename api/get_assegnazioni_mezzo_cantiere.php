<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $id_mezzo = $_GET['id_mezzo'] ?? 0;
    $id_cantiere = $_GET['id_cantiere'] ?? null;
    
    if (!$id_cantiere) {
        throw new Exception('ID Cantiere non fornito');
    }
    
    // Query per ottenere le assegnazioni mezzo al cantiere
    $sql = "SELECT 
                amc.id,
                amc.id_mezzo,
                amc.id_cantiere,
                amc.id_dipendente,
                amc.ora_inizio,
                amc.ora_fine,
                amc.km_inizio,
                amc.km_fine,
                amc.note,
                m.id as mezzo_id,
                m.nome_mezzo,
                m.targa,
                d.id as dipendente_id,
                d.nome as nome_dipendente,
                d.cognome as cognome_dipendente
            FROM assegnazioni_mezzo_cantiere amc
            LEFT JOIN mezzi m ON amc.id_mezzo = m.id
            LEFT JOIN dipendenti d ON amc.id_dipendente = d.id
            WHERE amc.id_cantiere = ?";
    
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