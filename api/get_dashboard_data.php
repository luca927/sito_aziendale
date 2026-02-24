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

$data = [];

// QUERY CORRETTA basata sulle colonne REALI della tabella dashboard
$sql = "
    SELECT 
        r.id,
        r.tipo_attivita,
        d.id AS dipendente_id,
        d.nome AS dipendente_nome,
        c.nome AS cantiere_nome,
        m.nome_mezzo AS mezzo_nome,
        r.lat,
        r.lng,
        r.data_attivita AS data_registrazione
    FROM dashboard r
    LEFT JOIN dipendenti d ON r.dipendente_id = d.id
    LEFT JOIN cantieri c ON r.cantiere_id = c.id
    LEFT JOIN mezzi m ON r.mezzo_id = m.id
    ORDER BY r.id ASC
";

$res = $conn->query($sql);

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode(["success" => true, "data" => $data]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Errore SQL: " . $conn->error
    ]);
}

$conn->close();
?>