<?php
// api/corsi_dipendente/add.php
// Aggiunge un corso a un dipendente

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id_dipendente']) || !isset($input['nome_corso'])) {
        throw new Exception("Dati obbligatori mancanti");
    }
    
    $id_dipendente = (int)$input['id_dipendente'];
    $nome_corso = $input['nome_corso'];
    $descrizione = $input['descrizione'] ?? null;
    $ente_erogante = $input['ente_erogante'] ?? null;
    $data_inizio_corso = $input['data_inizio_corso'] ?? null;
    $data_completamento = $input['data_completamento'] ?? null;
    $ore_corso = isset($input['ore_corso']) ? (int)$input['ore_corso'] : null;
    $costo = isset($input['costo']) ? (float)$input['costo'] : null;
    $certificazione_numero = $input['certificazione_numero'] ?? null;
    $certificazione_rilasciata = isset($input['certificazione_rilasciata']) ? (int)$input['certificazione_rilasciata'] : 0;
    $voto_finale = isset($input['voto_finale']) ? (int)$input['voto_finale'] : null;
    
    // Verifica che il dipendente esista
    $checkDip = $conn->prepare("SELECT id FROM dipendenti WHERE id = ?");
    $checkDip->bind_param("i", $id_dipendente);
    $checkDip->execute();
    if ($checkDip->get_result()->num_rows === 0) {
        throw new Exception("Dipendente non trovato");
    }
    
    $query = "
        INSERT INTO corsi_dipendente 
        (id_dipendente, nome_corso, descrizione, ente_erogante, 
         data_inizio_corso, data_completamento, ore_corso, costo,
         certificazione_numero, certificazione_rilasciata, voto_finale)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issssiidisi", $id_dipendente, $nome_corso, $descrizione, 
                      $ente_erogante, $data_inizio_corso, $data_completamento,
                      $ore_corso, $costo, $certificazione_numero, 
                      $certificazione_rilasciata, $voto_finale);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Corso aggiunto',
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