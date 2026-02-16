<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data || !isset($data['id'])) {
        throw new Exception('ID non fornito');
    }
    
    $id = $data['id'];
    
    // Elimina l'attività dalla tabella dashboard
    $sql = "DELETE FROM dashboard WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Errore nella preparazione della query: ' . $conn->error);
    }
    
    $stmt->bind_param('i', $id);
    
    if (!$stmt->execute()) {
        throw new Exception('Errore nell\'eliminazione: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Attività eliminata con successo'
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