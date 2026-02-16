<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('ID non fornito');
    }
    
    $id = intval($data['id']);
    $tipo_attivita = $data['tipo_attivita'] ?? 'LAVORAZIONE';
    $dipendente_id = intval($data['dipendente_id'] ?? 0);
    $cantiere_id = intval($data['cantiere_id'] ?? 0);
    $mezzo_id = !empty($data['mezzo_id']) ? intval($data['mezzo_id']) : null;
    $lat_man = !empty($data['lat_man']) ? floatval($data['lat_man']) : null;
    $lng_man = !empty($data['lng_man']) ? floatval($data['lng_man']) : null;
    
    if (!$dipendente_id || !$cantiere_id) {
        throw new Exception('Campi obbligatori mancanti');
    }
    
    // Se non hai coordinate manuali, prendi quelle del cantiere
    if ($lat_man === null || $lng_man === null) {
        $stmt_c = $conn->prepare("SELECT lat, lng FROM cantieri WHERE id = ?");
        $stmt_c->bind_param("i", $cantiere_id);
        $stmt_c->execute();
        $res_c = $stmt_c->get_result()->fetch_assoc();
        
        $lat_man = $lat_man ?? (isset($res_c['lat']) ? floatval($res_c['lat']) : 0.0);
        $lng_man = $lng_man ?? (isset($res_c['lng']) ? floatval($res_c['lng']) : 0.0);
    }
    
    // Aggiorna l'attività nella tabella dashboard
    $sql = "UPDATE dashboard 
            SET tipo_attivita = ?, 
                dipendente_id = ?, 
                cantiere_id = ?, 
                mezzo_id = ?, 
                lat = ?, 
                lng = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Errore nella preparazione della query: ' . $conn->error);
    }
    
    $stmt->bind_param('siiiddi', 
        $tipo_attivita,
        $dipendente_id,
        $cantiere_id,
        $mezzo_id,
        $lat_man,
        $lng_man,
        $id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'aggiornamento: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Attività aggiornata con successo'
    ]);
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?>