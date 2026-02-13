<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/auth.php'; // Protezione sessione

// Restituisce la data in formato ISO 8601 per JavaScript
echo json_encode([
    "datetime" => date('c'), 
    "success" => true
]);
?>