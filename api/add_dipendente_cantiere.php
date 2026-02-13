<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    $id_dipendente = $data['id_dipendente'] ?? null;
    $id_cantiere = $data['id_cantiere'] ?? null;
    $ruolo_cantiere = $data['ruolo_cantiere'] ?? null;
    $ore_previste = $data['ore_previste'] ?? null;
    $data_inizio = $data['data_inizio'] ?? null;
    $data_fine = $data['data_fine'] ?? null;
    
    if (!$id_dipendente || !$id_cantiere) {
        throw new Exception('Dipendente e Cantiere sono obbligatori');
    }
    
    // Inserisci l'assegnazione dipendente al cantiere
    $sql = "INSERT INTO assegnazioni_dipendenti_cantiere 
            (id_dipendente, id_cantiere, ruolo_cantiere, ore_previste, data_inizio, data_fine) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione query: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'iissss',
        $id_dipendente,
        $id_cantiere,
        $ruolo_cantiere,
        $ore_previste,
        $data_inizio,
        $data_fine
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'inserimento: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Dipendente assegnato correttamente',
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