<?php
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/../backend/auth.php';
require_once __DIR__ . '/../backend/db.php';

header('Content-Type: application/json');

try {
    // --- VERSIONE PDO ---
    if (isset($pdo)) {
        $stmt = $pdo->query("
            SELECT 
                t.id,
                t.dipendente_id,
                t.cantiere_id,
                t.mezzo_id,
                t.lat,
                t.lng,
                t.data_attivita,
                CONCAT(d.nome, ' ', d.cognome) as dipendente,
                c.nome as cantiere,
                m.nome_mezzo as mezzo
            FROM tracciamenti t
            LEFT JOIN dipendenti d ON t.dipendente_id = d.id
            LEFT JOIN cantieri c ON t.cantiere_id = c.id
            LEFT JOIN mezzi m ON t.mezzo_id = m.id
            ORDER BY t.id ASC
        ");
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Formatta i risultati
        $data = array_map(function($row) {
            return [
                'id' => $row['id'],
                'dipendente_id' => $row['dipendente_id'],
                'cantiere_id' => $row['cantiere_id'],
                'mezzo_id' => $row['mezzo_id'],
                'dipendente' => $row['dipendente'] ?: 'Sconosciuto',
                'cantiere' => $row['cantiere'] ?: 'Sconosciuto',
                'mezzo' => $row['mezzo'] ?: 'Nessuno',
                'latitudine' => $row['lat'],
                'longitudine' => $row['lng'],
                'lat' => $row['lat'],
                'lng' => $row['lng'],
                'data' => $row['data_attivita'],
                'data_attivita' => $row['data_attivita']
            ];
        }, $results);
        
        echo json_encode([
            'success' => true,
            'count' => count($data),
            'data' => $data
        ]);
        exit;
    }

    // --- VERSIONE MYSQLI ---
    if (!isset($conn)) {
        echo json_encode(['success' => false, 'error' => 'Connessione database non disponibile']);
        exit;
    }

    $query = "
        SELECT 
            t.id,
            t.dipendente_id,
            t.cantiere_id,
            t.mezzo_id,
            t.lat,
            t.lng,
            t.data_attivita,
            CONCAT(d.nome, ' ', d.cognome) as dipendente,
            c.nome as cantiere,
            m.nome_mezzo as mezzo
        FROM tracciamenti t
        LEFT JOIN dipendenti d ON t.dipendente_id = d.id
        LEFT JOIN cantieri c ON t.cantiere_id = c.id
        LEFT JOIN mezzi m ON t.mezzo_id = m.id
        ORDER BY t.id ASC
    ";

    $result = $conn->query($query);

    if (!$result) {
        echo json_encode([
            'success' => false,
            'error' => 'Errore query',
            'mysql_error' => $conn->error
        ]);
        exit;
    }

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'id' => $row['id'],
            'dipendente_id' => $row['dipendente_id'],
            'cantiere_id' => $row['cantiere_id'],
            'mezzo_id' => $row['mezzo_id'],
            'dipendente' => $row['dipendente'] ?: 'Sconosciuto',
            'cantiere' => $row['cantiere'] ?: 'Sconosciuto',
            'mezzo' => $row['mezzo'] ?: 'Nessuno',
            'latitudine' => $row['lat'],
            'longitudine' => $row['lng'],
            'lat' => $row['lat'],
            'lng' => $row['lng'],
            'data' => $row['data_attivita'],
            'data_attivita' => $row['data_attivita']
        ];
    }

    echo json_encode([
        'success' => true,
        'count' => count($data),
        'data' => $data
    ]);

    $conn->close();

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>