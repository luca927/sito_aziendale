<?php
// api/manutenzioni/add.php
// Registra una manutenzione per un mezzo
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
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id_mezzo']) || !isset($input['data_manutenzione'])) {
        throw new Exception("Dati obbligatori mancanti");
    }
    
    $id_mezzo = (int)$input['id_mezzo'];
    $data_manutenzione = $input['data_manutenzione'];
    $tipo_manutenzione = $input['tipo_manutenzione'] ?? null;
    $descrizione = $input['descrizione'] ?? null;
    $prossima_scadenza = $input['prossima_scadenza'] ?? null;
    $costo = isset($input['costo']) ? (float)$input['costo'] : null;
    $ore_lavoro = isset($input['ore_lavoro']) ? (float)$input['ore_lavoro'] : null;
    $fornitore = $input['fornitore'] ?? null;
    $completata = isset($input['completata']) ? (int)$input['completata'] : 1;
    $note_tecniche = $input['note_tecniche'] ?? null;
    
    // Verifica mezzo
    $checkMezzo = $conn->prepare("SELECT id FROM mezzi WHERE id = ?");
    $checkMezzo->bind_param("i", $id_mezzo);
    $checkMezzo->execute();
    if ($checkMezzo->get_result()->num_rows === 0) {
        throw new Exception("Mezzo non trovato");
    }
    
    $query = "
        INSERT INTO manutenzioni_mezzo 
        (id_mezzo, tipo_manutenzione, descrizione, data_manutenzione, 
         prossima_scadenza, costo, ore_lavoro, fornitore, completata, note_tecniche)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssddsii", $id_mezzo, $tipo_manutenzione, $descrizione, 
                      $data_manutenzione, $prossima_scadenza, $costo, $ore_lavoro, 
                      $fornitore, $completata, $note_tecniche);
    
    if ($stmt->execute()) {
        // Aggiorna campo prossima_manutenzione della tabella mezzi se fornito
        if ($prossima_scadenza) {
            $updateMezzo = $conn->prepare(
                "UPDATE mezzi SET prossima_manutenzione = ? WHERE id = ?"
            );
            $updateMezzo->bind_param("si", $prossima_scadenza, $id_mezzo);
            $updateMezzo->execute();
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Manutenzione registrata',
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception("Errore durante l'inserimento");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>