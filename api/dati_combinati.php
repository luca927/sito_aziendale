<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// 1. Controlla se loggato
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "Non autorizzato"]);
    exit;
}

// 2. Controlla se la sessione è scaduta (1 ora)
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 3600) {
    session_destroy();
    http_response_code(401);
    echo json_encode(["error" => "Sessione scaduta"]);
    exit;
}
$_SESSION['last_activity'] = time();

require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');
$conn->set_charset("utf8mb4");

$sql = "SELECT 
            d.id AS id,
            d.tipo_attivita,
            CONCAT(dip.cognome, ' ', dip.nome) AS dipendente,
            d.dipendente_id,
            c.nome AS cantiere,
            d.cantiere_id,
            m.nome_mezzo AS mezzo,
            d.mezzo_id,
            IFNULL(CAST(c.lat AS DECIMAL(10,7)), NULL) AS lat,
            IFNULL(CAST(c.lng AS DECIMAL(10,7)), NULL) AS lng,
            d.data_attivita AS data
        FROM dashboard d
        LEFT JOIN dipendenti dip ON d.dipendente_id = dip.id
        LEFT JOIN cantieri c ON d.cantiere_id = c.id
        LEFT JOIN mezzi m ON d.mezzo_id = m.id
        WHERE d.tipo_attivita IS NOT NULL AND d.tipo_attivita <> ''
        ORDER BY d.data_attivita DESC";

$result = $conn->query($sql);

if (!$result) {
    error_log("Errore dati_combinati.php: " . $conn->error);
    http_response_code(500);
    echo json_encode(["error" => "Errore interno del server"]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>