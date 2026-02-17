<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../backend/db.php';

$id_cantiere = isset($_GET['id_cantiere']) ? intval($_GET['id_cantiere']) : 0;

if ($id_cantiere <= 0) {
    echo json_encode([]);
    exit;
}

try {
    // Recuperiamo solo gli ID dei dipendenti assegnati
    $sql = "SELECT id_dipendente FROM assegnazioni_dipendenti_cantiere WHERE id_cantiere = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cantiere);
    $stmt->execute();
    $result = $stmt->get_result();

    $assegnati = [];
    while ($row = $result->fetch_assoc()) {
        // Estraiamo solo l'ID e lo forziamo a numero
        $assegnati[] = intval($row['id_dipendente']);
    }

    echo json_encode($assegnati);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}