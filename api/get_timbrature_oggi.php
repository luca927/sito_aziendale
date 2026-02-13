<?php
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