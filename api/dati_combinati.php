<?php
require_once __DIR__ . '/../backend/db.php';
header('Content-Type: application/json');
$conn->set_charset("utf8mb4");

$sql = "SELECT 
            d.id AS id,
            d.tipo_attivita,
            -- Dati Dipendente
            CONCAT(dip.cognome, ' ', dip.nome) AS dipendente,
            d.dipendente_id, -- Prendiamo l'ID direttamente dalla dashboard
            dip.recapitieMail AS email,
            dip.telefono AS telefono,
            dip.documentiIdentita AS documenti,
            dip.patenti,
            dip.indirizzoResidenza AS residenza,
            dip.DatiBancari AS iban,
            dip.sesso,
            dip.stato_civile AS statoCivile,
            dip.dataDiNascita,
            dip.Corsi_e_Formazione AS formazione,
            dip.Esperienze AS esperienze,
            dip.Competenze AS competenze,
            -- Dati Cantiere
            c.nome AS cantiere,
            d.cantiere_id,    -- FONDAMENTALE
            -- Dati Mezzo
            m.nome_mezzo AS mezzo,
            d.mezzo_id,       -- FONDAMENTALE
            -- Dati Geolocalizzazione e Tempo
            c.lat AS lat,
            c.lng AS lng,
            d.data_attivita AS data
        FROM dashboard d
        LEFT JOIN dipendenti dip ON d.dipendente_id = dip.id
        LEFT JOIN cantieri c ON d.cantiere_id = c.id
        LEFT JOIN mezzi m ON d.mezzo_id = m.id
        WHERE d.tipo_attivita IS NOT NULL AND d.tipo_attivita <> ''
        ORDER BY d.data_attivita DESC";

$result = $conn->query($sql);

if (!$result) {
    echo json_encode(["error" => $conn->error]);
    exit;
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>