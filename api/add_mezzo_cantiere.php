<?php
// api/assegnazioni/mezzi_cantieri_add.php
// Assegna un mezzo a un cantiere con un dipendente

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id_mezzo']) || !isset($input['id_cantiere'])) {
        throw new Exception("Dati obbligatori mancanti");
    }
    
    $id_mezzo = (int)$input['id_mezzo'];
    $id_cantiere = (int)$input['id_cantiere'];
    $id_dipendente = isset($input['id_dipendente']) ? (int)$input['id_dipendente'] : null;
    $ora_inizio = $input['ora_inizio'] ?? date('Y-m-d H:i:s');
    $km_inizio = isset($input['km_inizio']) ? (int)$input['km_inizio'] : null;
    $note = $input['note'] ?? null;
    
    // Verifica mezzo
    $checkMezzo = $conn->prepare("SELECT id FROM mezzi WHERE id = ?");
    $checkMezzo->bind_param("i", $id_mezzo);
    $checkMezzo->execute();
    if ($checkMezzo->get_result()->num_rows === 0) {
        throw new Exception("Mezzo non trovato");
    }
    
    // Verifica cantiere
    $checkCant = $conn->prepare("SELECT id FROM cantieri WHERE id = ?");
    $checkCant->bind_param("i", $id_cantiere);
    $checkCant->execute();
    if ($checkCant->get_result()->num_rows === 0) {
        throw new Exception("Cantiere non trovato");
    }
    
    $query = "
        INSERT INTO assegnazioni_mezzo 
        (id_mezzo, id_dipendente, id_cantiere, ora_inizio, km_inizio, note)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiisis", $id_mezzo, $id_dipendente, $id_cantiere, 
                      $ora_inizio, $km_inizio, $note);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Mezzo assegnato',
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