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
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    $nome = $data['nome'] ?? null;
    $indirizzo = $data['indirizzo'] ?? null;
    $referente = $data['referente'] ?? null;
    $giorni_lavoro = $data['giorni_lavoro'] ?? null;
    $data_inizio = $data['data_inizio'] ?? null;
    $data_fine = $data['data_fine'] ?? null;
    $note = $data['note'] ?? null;
    $lat = $data['lat'] ?? null;
    $lng = $data['lng'] ?? null;
    $stato = $data['stato'] ?? 'attivo';
    $coordinatore_sicurezza = $data['coordinatore_sicurezza'] ?? null;
    $piano_sicurezza = $data['piano_sicurezza'] ?? null;
    
    if (!$nome) {
        throw new Exception('Nome cantiere obbligatorio');
    }
    
    // Inserimento del cantiere
    $sql = "INSERT INTO cantieri 
            (nome, indirizzo, referente, giorni_lavoro, data_inizio, data_fine, note, lat, lng, stato, coordinatore_sicurezza, piano_sicurezza, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione query: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'ssssssssssss',
        $nome,
        $indirizzo,
        $referente,
        $giorni_lavoro,
        $data_inizio,
        $data_fine,
        $note,
        $lat,
        $lng,
        $stato,
        $coordinatore_sicurezza,
        $piano_sicurezza
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'inserimento: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cantiere creato correttamente',
        'id' => $stmt->insert_id
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