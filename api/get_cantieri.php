<?php
header('Content-Type: application/json');
// Se hai un sistema di login, tieni auth.php, altrimenti commentalo per test
// require_once __DIR__ . '/../backend/auth.php'; 
require_once __DIR__ . '/../backend/db.php';

try {
    $conn->set_charset("utf8mb4");

    // Query ottimizzata: prende tutto (*) e concatena i nomi degli operai
    $sql = "SELECT c.*, 
            (SELECT GROUP_CONCAT(DISTINCT CONCAT(d.nome, ' ', d.cognome) SEPARATOR ', ') 
             FROM assegnazioni_dipendenti_cantiere adc 
             JOIN dipendenti d ON adc.id_dipendente = d.id 
             WHERE adc.id_cantiere = c.id) as operai
            FROM cantieri c 
            ORDER BY c.data_inizio DESC";

    $result = $conn->query($sql);

    if (!$result) {
        throw new Exception($conn->error);
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
    // Converti in float e controlla che non siano zero o vuoti
    $lat = floatval($row['lat'] ?? 0);
    $lng = floatval($row['lng'] ?? 0);
    
    $row['lat'] = $lat != 0 ? $lat : null;
    $row['lng'] = $lng != 0 ? $lng : null;
    
    $data[] = $row;
}

    echo json_encode($data);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "error" => $e->getMessage()
    ]);
}

$conn->close();