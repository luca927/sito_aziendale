<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sitoarsnet_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// 1️⃣ Dipendenti
$dipendenti = $conn->query("SELECT id, nome FROM dipendenti");

// 2️⃣ Mezzi
$mezzi = $conn->query("SELECT id, nome_mezzo FROM mezzi");

// 3️⃣ Cantieri
$cantieri = $conn->query("SELECT id, nome, latitudine, longitudine FROM cantieri");

if ($dipendenti->num_rows > 0 && $mezzi->num_rows > 0 && $cantieri->num_rows > 0) {

    while ($d = $dipendenti->fetch_assoc()) {
        while ($m = $mezzi->fetch_assoc()) {

            $cantieri->data_seek(0);

            while ($c = $cantieri->fetch_assoc()) {

                $lat = $c['latitudine'];
                $lon = $c['longitudine'];
                $coordinate = "$lat,$lon";
                $data = date("Y-m-d H:i:s");

                $sql = "INSERT INTO dashboard 
                        (tipo_attivita, dipendente_id, cantiere_id, mezzo_id, coordinate, data_attivita)
                        VALUES 
                        ('combinazione', ".$d['id'].", ".$c['id'].", ".$m['id'].", '$coordinate', '$data')";

                $conn->query($sql);
            }
        }

        $mezzi->data_seek(0);
    }

    echo "Dati inseriti correttamente nella dashboard!";
} else {
    echo "Errore: una delle tabelle è vuota.";
}

$conn->close();
?>