<?php
// api/delete_mezzo_cantiere.php
// Elimina un'assegnazione di un mezzo a un cantiere

require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $data = json_decode(file_get_contents("php://input"), true);

    $id = $data['id'] ?? null;
    if (!$id) {
        throw new Exception("ID assegnazione non fornito");
    }

    $id = (int)$id;

    $query = "DELETE FROM assegnazioni_mezzo WHERE id = ?";

    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Errore prepare: " . $conn->error);
    }

    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode([
            "success" => true,
            "message" => "Mezzo rimosso dal cantiere"
        ]);
    } else {
        throw new Exception("Errore durante l'eliminazione: " . $stmt->error);
    }

    $stmt->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}

$conn->close();
?>