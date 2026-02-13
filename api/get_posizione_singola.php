<?php
require_once '/../backend/db.php';

$id = intval($_GET['id']); // sicurezza

$sql = "SELECT lat, lng FROM dipendenti WHERE id = $id LIMIT 1";
$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {
    echo json_encode($res->fetch_assoc());
} else {
    echo json_encode(["lat" => null, "lng" => null]);
}

$conn->close();
?>
