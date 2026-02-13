<?php
require_once __DIR__ . '/backend/auth.php';
?>
<!DOCTYPE html>
<html lang="it">
<head>
<meta charset="UTF-8">
<title>Aggiungi Mezzo</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: #f4f6f9;
        margin: 0;
    }

    header {
        background: #0056D2;
        color: white;
        padding: 15px 25px;
        font-size: 1.4rem;
        font-weight: bold;
    }

    .container {
        max-width: 800px;
        margin: 25px auto;
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 5px 12px rgba(0,0,0,0.1);
    }

    label {
        font-weight: bold;
        margin-top: 10px;
        display: block;
    }

    input, textarea, select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    button {
        margin-top: 20px;
        background: #0056D2;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: bold;
        font-size: 1rem;
    }

    button:hover {
        background: #003f9e;
    }

</style>
</head>

<body>

<header>➕ Aggiungi Mezzo</header>

<div class="container">

<form id="addForm">

    <label>Nome Mezzo</label>
    <input type="text" id="nome_mezzo">

    <label>Targa</label>
    <input type="text" id="targa">

    <label>Tipo Mezzo</label>
    <input type="text" id="tipo">

    <label>Ultima Manutenzione</label>
    <input type="date" id="ultima_manutenzione">

    <label>Prossima Manutenzione</label>
    <input type="date" id="prossima_manutenzione">

    <label>Anno</label>
    <input type="number" id="anno">

    <label>Note</label>
    <textarea id="note"></textarea>

    <button type="submit">Aggiungi Mezzo</button>

</form>

</div>

<script>
document.getElementById("addForm").addEventListener("submit", async (e) => {
    e.preventDefault();

    const payload = {
        nome_mezzo: document.getElementById("nome_mezzo").value.trim(),
        targa: document.getElementById("targa").value.trim(),
        tipo: document.getElementById("tipo").value.trim(),
        ultima_manutenzione: document.getElementById("ultima_manutenzione").value || null,
        prossima_manutenzione: document.getElementById("prossima_manutenzione").value || null,
        anno: document.getElementById("anno").value ? parseInt(document.getElementById("anno").value, 10) : null,
        note: document.getElementById("note").value.trim()
    };

    console.log("Payload add_mezzo:", payload);

    try {
        const res = await fetch("./api/add_mezzo.php", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify(payload)
        });

        const text = await res.text();
        console.log("Server response raw:", text);

        // se il server non ritorna JSON valido, fallo vedere
        let data;
        try { data = JSON.parse(text); } catch (err) {
            alert("Risposta server non valida. Vedi console.");
            return;
        }

        if (data.success) {
            alert("Mezzo aggiunto correttamente!");
            window.location.href = "mezzi.php";
        } else {
            alert("Errore: " + (data.error || "salvataggio fallito"));
        }
    } catch (err) {
        console.error("Errore fetch:", err);
        alert("Errore imprevisto. Controlla console e Network tab.");
    }
});

</script>

</body>
</html>
