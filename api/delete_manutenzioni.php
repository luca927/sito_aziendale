<?php
// api/manutenzioni/delete.php
// Elimina una registrazione di manutenzione

require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $input = json_decode(file_get_contents("php://input"), true);
    
    if (!$input || !isset($input['id'])) {
        throw new Exception("ID non fornito");
    }
    
    $id = (int)$input['id'];
    
    $query = "DELETE FROM manutenzioni_mezzo WHERE id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Manutenzione eliminata'
        ]);
    } else {
        throw new Exception("Errore durante l'eliminazione");
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>