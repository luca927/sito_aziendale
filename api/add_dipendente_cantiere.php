<?php
// api/assegnazioni/dipendenti_cantieri_add.php
// Assegna un dipendente a un cantiere

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id_dipendente']) || !isset($input['id_cantiere'])) {
        throw new Exception("Dati obbligatori mancanti");
    }
    
    $id_dipendente = (int)$input['id_dipendente'];
    $id_cantiere = (int)$input['id_cantiere'];
    $ruolo_cantiere = $input['ruolo_cantiere'] ?? 'Operaio';
    $ore_previste = isset($input['ore_previste']) ? (int)$input['ore_previste'] : null;
    $data_inizio = $input['data_inizio'] ?? date('Y-m-d');
    $data_fine = $input['data_fine'] ?? null;
    
    // Verifica dipendente
    $checkDip = $conn->prepare("SELECT id FROM dipendenti WHERE id = ?");
    $checkDip->bind_param("i", $id_dipendente);
    $checkDip->execute();
    if ($checkDip->get_result()->num_rows === 0) {
        throw new Exception("Dipendente non trovato");
    }
    
    // Verifica cantiere
    $checkCant = $conn->prepare("SELECT id FROM cantieri WHERE id = ?");
    $checkCant->bind_param("i", $id_cantiere);
    $checkCant->execute();
    if ($checkCant->get_result()->num_rows === 0) {
        throw new Exception("Cantiere non trovato");
    }
    
    $query = "
        INSERT INTO assegnazioni_cantiere 
        (id_dipendente, id_cantiere, ruolo_cantiere, ore_previste, data_inizio, data_fine)
        VALUES (?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iisiss", $id_dipendente, $id_cantiere, $ruolo_cantiere, 
                      $ore_previste, $data_inizio, $data_fine);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Assegnazione creata',
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