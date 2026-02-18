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
    
    // 1. Recuperiamo i dati dal JSON
    $lat_inviata = !empty($data['lat_man']) ? floatval($data['lat_man']) : null;
    $lng_inviata = !empty($data['lng_man']) ? floatval($data['lng_man']) : null;

    // 2. Se mancano le coordinate inviate, le cerchiamo nel cantiere
    if ($lat_inviata === null || $lng_inviata === null || $lat_inviata == 0) {
        $stmt_c = $conn->prepare("SELECT lat, lng FROM cantieri WHERE id = ?");
        $stmt_c->bind_param("i", $cantiere_id);
        $stmt_c->execute();
        $res_c = $stmt_c->get_result()->fetch_assoc();
        
        // Assegniamo le coordinate del cantiere solo se non abbiamo quelle manuali
        $lat_finale = ($res_c && isset($res_c['lat'])) ? floatval($res_c['lat']) : 0.0;
        $lng_finale = ($res_c && isset($res_c['lng'])) ? floatval($res_c['lng']) : 0.0;
    } else {
        // Usiamo quelle inviate dal dispositivo (GPS)
        $lat_finale = $lat_inviata;
        $lng_finale = $lng_inviata;
    }

// 3. Ora usiamo $lat_finale e $lng_finale nella query di UPDATE
// (Modifica il bind_param sotto usando queste due variabili)
    
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