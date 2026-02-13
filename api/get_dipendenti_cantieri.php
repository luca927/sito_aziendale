<?php
// api/assegnazioni/dipendenti_cantieri_get.php
// Recupera le assegnazioni di un dipendente ai cantieri

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $id_dipendente = isset($_GET['id_dipendente']) ? (int)$_GET['id_dipendente'] : null;
    
    if (!$id_dipendente) {
        throw new Exception("ID dipendente non fornito");
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
            c.nome AS nome_cantiere,
            c.indirizzo AS indirizzo_cantiere,
            c.stato AS stato_cantiere
        FROM assegnazioni_cantiere ac
        JOIN cantieri c ON ac.id_cantiere = c.id
        WHERE ac.id_dipendente = ?
        ORDER BY ac.data_inizio DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_dipendente);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $assegnazioni = [];
    
    while ($row = $result->fetch_assoc()) {
        $assegnazioni[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $assegnazioni]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>