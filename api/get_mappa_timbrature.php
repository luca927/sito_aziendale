<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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