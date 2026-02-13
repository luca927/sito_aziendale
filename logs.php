<?php
require_once __DIR__ . '/backend/auth.php';
require_once __DIR__ . '/backend/db.php';

// Protezione: solo l'admin può vedere i log
if (!isAdmin()) {
    header("Location: dashboard.php?error=accesso_negato");
    exit;
}

include 'includes/header.php';
?>

<div class="layout d-flex">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content flex-grow-1 p-4">
        <header class="mb-4">
            <h2 class="text-primary fw-bold">📋 Registro Attività (Audit Log)</h2>
            <p class="text-muted">Cronologia completa delle operazioni effettuate sul sistema.</p>
        </header>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Data e Ora</th>
                            <th>Utente</th>
                            <th>Azione</th>
                            <th>Dettagli</th>
                            <th>Indirizzo IP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Recuperiamo i log dal più recente al più vecchio
                        $query = "SELECT * FROM audit_logs ORDER BY data_ora DESC LIMIT 100";
                        $result = $conn->query($query);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $data = date("d/m/Y H:i:s", strtotime($row['data_ora']));
                                
                                // Colori diversi per azioni diverse (opzionale)
                                $badgeClass = "bg-secondary";
                                if (strpos($row['azione'], 'ELIMINAZIONE') !== false) $badgeClass = "bg-danger";
                                if (strpos($row['azione'], 'AGGIUNTA') !== false) $badgeClass = "bg-success";
                                if (strpos($row['azione'], 'LOGIN') !== false) $badgeClass = "bg-info";

                                echo "<tr>
                                    <td class='ps-4 text-muted small'>$data</td>
                                    <td><strong>{$row['username']}</strong> <br><small class='text-muted'>ID: {$row['user_id']}</small></td>
                                    <td><span class='badge $badgeClass'>{$row['azione']}</span></td>
                                    <td class='small'>{$row['dettagli']}</td>
                                    <td class='text-muted small'>{$row['indirizzo_ip']}</td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Nessuna attività registrata.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>