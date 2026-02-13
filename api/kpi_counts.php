<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');

$out = [
    "dipendenti" => 0,
    "cantieri" => 0,
    "mezzi" => 0
];

$out["dipendenti"] = $conn->query("SELECT COUNT(*) AS n FROM dipendenti")->fetch_assoc()["n"];
$out["cantieri"]   = $conn->query("SELECT COUNT(*) AS n FROM cantieri")->fetch_assoc()["n"];
$out["mezzi"]      = $conn->query("SELECT COUNT(*) AS n FROM mezzi")->fetch_assoc()["n"];

echo json_encode($out);