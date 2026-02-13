<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/db.php';

// Deve restituire TUTTI i cantieri per il forEach
$sql = "SELECT * FROM cantieri ORDER BY id ASC";
$result = $conn->query($sql);
$data = $result->fetch_all(MYSQLI_ASSOC); 

echo json_encode($data); 
$conn->close();