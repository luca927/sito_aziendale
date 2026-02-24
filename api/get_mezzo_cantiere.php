<?php
// api/assegnazioni/mezzi_cantieri_get.php
// Recupera le assegnazioni di un mezzo
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
require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $id_mezzo = isset($_GET['id_mezzo']) ? (int)$_GET['id_mezzo'] : null;
    
    if (!$id_mezzo) {
        throw new Exception("ID mezzo non fornito");
    }

    $query = "
        SELECT 
            am.id,
            am.id_mezzo,
            am.id_dipendente,
            am.id_cantiere,
            am.ora_inizio,
            am.ora_fine,
            am.km_inizio,
            am.km_fine,
            am.consumo_carburante,
            am.costo_carburante,
            am.note,
            d.nome AS nome_dipendente,
            d.cognome AS cognome_dipendente,
            c.nome AS nome_cantiere
        FROM assegnazioni_mezzo am
        LEFT JOIN dipendenti d ON am.id_dipendente = d.id
        LEFT JOIN cantieri c ON am.id_cantiere = c.id
        WHERE am.id_mezzo = ?
        ORDER BY am.ora_inizio DESC
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id_mezzo);
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