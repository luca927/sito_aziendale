<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/db.php';

// Impostiamo il charset per evitare problemi di caratteri speciali
$conn->set_charset("utf8mb4");

$sql = "SELECT c.*, 
        (SELECT GROUP_CONCAT(DISTINCT d.nome SEPARATOR ', ') 
         FROM assegnazioni_dipendenti_cantiere adc 
         JOIN dipendenti d ON adc.id_dipendente = d.id 
         WHERE CAST(adc.id_cantiere AS UNSIGNED) = CAST(c.id AS UNSIGNED)) as operai
        FROM cantieri c 
        ORDER BY c.id ASC";

$result = $conn->query($sql);

if (!$result) {
    // Se c'è un errore, lo stampiamo per capire cosa succede
    echo json_encode(["error" => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data); 
$conn->close();
?>