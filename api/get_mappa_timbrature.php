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

header('Content-Type: application/json');
require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

$sql = "SELECT t.*, d.nome as dip_nome, d.cognome as dip_cognome, c.nome as cantiere_nome
        FROM timbrature t
        JOIN dipendenti d ON t.dipendente_id = d.id
        JOIN cantieri c ON t.cantiere_id = c.id
        WHERE DATE(t.data_ora_server) = CURDATE()";

$res = $conn->query($sql);

// DEBUG: mostra errore SQL
if (!$res) {
    echo json_encode([
        "success" => false,
        "error" => $conn->error,
        "sql" => $sql
    ]);
    exit;
}

$punti = [];

while ($row = $res->fetch_assoc()) {
    $punti[] = $row;
}

echo json_encode($punti);