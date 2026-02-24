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

$dipendente_id = $_SESSION['user_id'];

$sql = "SELECT t.*, c.nome as cantiere_nome 
        FROM timbrature t 
        JOIN cantieri c ON t.cantiere_id = c.id 
        WHERE t.dipendente_id = ? 
        AND DATE(t.data_ora_server) = CURDATE() 
        ORDER BY t.data_ora_server DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dipendente_id);
$stmt->execute();
$result = $stmt->get_result();

$timbrature = [];
while ($row = $result->fetch_assoc()) {
    $timbrature[] = $row;
}

echo json_encode($timbrature);