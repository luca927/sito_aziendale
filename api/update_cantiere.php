<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        throw new Exception('Dati non validi');
    }
    
    $id = $data['id'] ?? null;
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
    
    if (!$id) {
        throw new Exception('ID cantiere non fornito');
    }
    
    if (!$nome) {
        throw new Exception('Nome cantiere obbligatorio');
    }
    
    // Update del cantiere
    $sql = "UPDATE cantieri SET 
            nome = ?,
            indirizzo = ?,
            referente = ?,
            giorni_lavoro = ?,
            data_inizio = ?,
            data_fine = ?,
            note = ?,
            lat = ?,
            lng = ?,
            stato = ?,
            coordinatore_sicurezza = ?,
            piano_sicurezza = ?,
            updated_at = NOW()
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Errore nella preparazione query: ' . $conn->error);
    }
    
    $stmt->bind_param(
        'ssssssssssssi',
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
        $piano_sicurezza,
        $id
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'aggiornamento: ' . $stmt->error);
    }
    
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Cantiere aggiornato correttamente'
        ]);
    } else {
        throw new Exception('Nessun cantiere aggiornato. Controlla che l\'ID esista');
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>