<?php
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