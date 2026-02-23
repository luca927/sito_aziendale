<?php
require_once __DIR__ . '/backend/auth.php';
require_once __DIR__ . '/backend/db.php';
require_once __DIR__ . '/includes/auth_admin.php';

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("ID mezzo mancante o non valido");
}

$id = intval($_GET["id"]);

// Prepariamo la query per recuperare i dati del mezzo
$stmt = $conn->prepare("SELECT * FROM mezzi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$mezzo = $stmt->get_result()->fetch_assoc();

if (!$mezzo) {
    die("Mezzo non trovato");
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica Mezzo</title>
    <style>
        /* ... il tuo CSS rimane invariato ... */
        body { font-family: Arial, sans-serif; background: #f4f6f9; margin: 0; }
        header { background: #0056D2; color: white; padding: 15px 25px; font-size: 1.4rem; font-weight: bold; }
        .container { max-width: 800px; margin: 25px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 12px rgba(0,0,0,0.1); }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input, textarea, select { width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        button { margin-top: 20px; background: #0056D2; color: white; border: none; padding: 10px 18px; border-radius: 8px; cursor: pointer; font-weight: bold; }
        button:hover { background: #003f9e; }
    </style>
</head>
<body>

<header>✏️ Modifica Mezzo: <?= htmlspecialchars($mezzo['nome_mezzo']) ?></header>

<div class="container">
    <form id="updateForm">
        <input type="hidden" id="mezzo_id" value="<?= $mezzo['id'] ?>">

        <label>Nome Mezzo</label>
        <input type="text" id="nome_mezzo" value="<?= htmlspecialchars($mezzo['nome_mezzo']) ?>" required>

        <label>Targa</label>
        <input type="text" id="targa" value="<?= htmlspecialchars($mezzo['targa']) ?>">

        <label>Tipo Mezzo</label>
        <input type="text" id="tipo" value="<?= htmlspecialchars($mezzo['tipo']) ?>">

        <label>Anno</label>
        <input type="number" id="anno" value="<?= $mezzo['anno'] ?>">

        <label>Ultima Manutenzione</label>
        <input type="date" id="ultima_manutenzione" value="<?= $mezzo['ultima_manutenzione'] ?>">

        <label>Prossima Manutenzione</label>
        <input type="date" id="prossima_manutenzione" value="<?= $mezzo['prossima_manutenzione'] ?>">

        <label>Stato</label>
        <select id="stato">
            <option value="attivo" <?= $mezzo['stato']=="attivo"?"selected":"" ?>>Attivo</option>
            <option value="in manutenzione" <?= $mezzo['stato']=="in manutenzione"?"selected":"" ?>>In manutenzione</option>
            <option value="fuori uso" <?= $mezzo['stato']=="fuori uso"?"selected":"" ?>>Fuori uso</option>
        </select>

        <label>Note</label>
        <textarea id="note"><?= htmlspecialchars($mezzo['note']) ?></textarea>

        <button type="submit">💾 Salva Modifiche</button>
        <button type="button" onclick="window.location.href='mezzi.php'" style="background:#6c757d;">Annulla</button>
    </form>
</div>

<script>
document.getElementById("updateForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    // Recupero i valori tramite document.getElementById per sicurezza
    const payload = {
        id: document.getElementById("mezzo_id").value,
        nome_mezzo: document.getElementById("nome_mezzo").value,
        targa: document.getElementById("targa").value,
        tipo: document.getElementById("tipo").value,
        anno: document.getElementById("anno").value,
        ultima_manutenzione: document.getElementById("ultima_manutenzione").value,
        prossima_manutenzione: document.getElementById("prossima_manutenzione").value,
        stato: document.getElementById("stato").value,
        note: document.getElementById("note").value
    };

    try {
        const res = await fetch("./api/update_mezzo.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (data.success) {
            alert("✅ Modifiche salvate con successo!");
            window.location.href = "mezzi.php";
        } else {
            alert("❌ Errore: " + (data.error || "Impossibile salvare"));
        }
    } catch (error) {
        console.error("Errore fetch:", error);
        alert("❌ Errore di connessione al server");
    }
});
</script>

</body>
</html>