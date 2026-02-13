<?php
// api/documenti_dipendente/add.php
// Aggiunge un documento di identità a un dipendente

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id_dipendente']) || !isset($input['tipo_documento']) || 
        !isset($input['numero_documento'])) {
        throw new Exception("Dati obbligatori mancanti");
    }
    
    $id_dipendente = (int)$input['id_dipendente'];
    $tipo_documento = $input['tipo_documento'];
    $numero_documento = $input['numero_documento'];
    $descrizione = $input['descrizione'] ?? null;
    $data_rilascio = $input['data_rilascio'] ?? null;
    $data_scadenza = $input['data_scadenza'] ?? null;
    $rilasciato_da = $input['rilasciato_da'] ?? null;
    
    // Verifica che il dipendente esista
    $checkDip = $conn->prepare("SELECT id FROM dipendenti WHERE id = ?");
    $checkDip->bind_param("i", $id_dipendente);
    $checkDip->execute();
    if ($checkDip->get_result()->num_rows === 0) {
        throw new Exception("Dipendente non trovato");
    }
    
    $query = "
        INSERT INTO documenti_dipendente 
        (id_dipendente, tipo_documento, numero_documento, descrizione, 
         data_rilascio, data_scadenza, rilasciato_da)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssss", $id_dipendente, $tipo_documento, $numero_documento, 
                      $descrizione, $data_rilascio, $data_scadenza, $rilasciato_da);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Documento aggiunto',
            'id' => $conn->insert_id
        ]);
    } else {
        throw new Exception("Errore durante l'inserimento: " . $stmt->error);
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>