<?php
// Recupera le assegnazioni operai di un cantiere

require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $id_cantiere = isset($_GET['id_cantiere']) ? (int)$_GET['id_cantiere'] : null;
    
    if (!$id_cantiere) {
        throw new Exception("ID cantiere non fornito");
    }

    $query = "
        SELECT 
            ac.id,
            ac.id_dipendente,
            ac.id_cantiere,
            ac.ruolo_cantiere,
            ac.ore_previste,
            ac.ore_effettive,
            ac.data_inizio,
            ac.data_fine,
            d.nome AS nome_dipendente,
            d.cognome AS cognome_dipendente,
            d.telefono
        FROM assegnazioni_cantiere ac
        JOIN dipendenti d ON ac.id_dipendente = d.id
        WHERE ac.id_cantiere = ?
        ORDER BY ac.data_inizio DESC
    ";
    
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Errore prepare: " . $conn->error);
    }
    
    $stmt->bind_param("i", $id_cantiere);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $assegnazioni = [];
    
    while ($row = $result->fetch_assoc()) {
        $assegnazioni[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $assegnazioni]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>