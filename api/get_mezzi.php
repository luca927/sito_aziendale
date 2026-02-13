<?php
require_once __DIR__ . '/../backend/db.php';

$q = $conn->query("SELECT * FROM mezzi ORDER BY id ASC");
$data = $q->fetch_all(MYSQLI_ASSOC);

echo json_encode($data);

$conn->close();
?>