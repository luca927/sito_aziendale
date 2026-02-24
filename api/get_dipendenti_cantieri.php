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

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $id_dipendente = $_GET['id_dipendente'] ?? null;

    if (!$id_dipendente) {
        throw new Exception('ID Dipendente non fornito');
    }

    // Query per ottenere i cantieri del dipendente e tutti gli operai assegnati
    $sql = "SELECT 
                c.id,
                c.nome,
                c.indirizzo,
                c.referente,
                c.giorni_lavoro,
                c.data_inizio,
                c.data_fine,
                c.note,
                c.coordinatore_sicurezza,
                c.piano_sicurezza,
                c.stato,
                c.lat,
                c.lng,
                c.created_at,
                c.updated_at,
                COALESCE(
                    GROUP_CONCAT(DISTINCT CONCAT(d.nome, ' ', d.cognome) SEPARATOR ', '),
                    '-'
                ) AS operai
            FROM cantieri c
            LEFT JOIN assegnazioni_dipendenti_cantiere adc_all 
                ON c.id = adc_all.id_cantiere
            LEFT JOIN dipendenti d 
                ON d.id = adc_all.id_dipendente
            WHERE c.id IN (
                SELECT id_cantiere 
                FROM assegnazioni_dipendenti_cantiere 
                WHERE id_dipendente = ?
            )
            GROUP BY c.id
            ORDER BY c.data_inizio DESC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione della query: ' . $conn->error);
    }

    $stmt->bind_param('i', $id_dipendente);

    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'esecuzione della query: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
