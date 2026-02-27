<?php
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

// Solo admin
if (!isset($_SESSION['user_id']) || $_SESSION['ruolo'] !== 'admin') {
    echo json_encode(['success' => false, 'error' => 'Accesso negato.']);
    exit;
}

require_once __DIR__ . '/../backend/db.php';

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';

switch ($action) {

    // ==================== CREA UTENTE ====================
    case 'crea':
    $username = trim($input['username'] ?? '');
    $password = $input['password'] ?? '';
    $nome     = trim($input['nome'] ?? '');
    $email    = trim($input['email'] ?? '');
    $ruolo    = in_array($input['ruolo'] ?? '', ['dipendente','manager','admin']) ? $input['ruolo'] : 'dipendente';

    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'error' => 'Username e password sono obbligatori.']);
        exit;
    }
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'error' => 'La password deve essere di almeno 8 caratteri.']);
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'error' => 'Username già in uso.']);
        exit;
    }
    $check->close();

    // Separa nome e cognome
    $parti = explode(' ', $nome, 2);
    $nome_dip    = $parti[0] ?? '';
    $cognome_dip = $parti[1] ?? '';

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // 1. Insert in dipendenti
    $stmt1 = $conn->prepare("INSERT INTO dipendenti (nome, cognome, recapitieMail) VALUES (?, ?, ?)");
    $stmt1->bind_param("sss", $nome_dip, $cognome_dip, $email);
    if (!$stmt1->execute()) {
        echo json_encode(['success' => false, 'error' => 'Errore inserimento dipendente: ' . $conn->error]);
        exit;
    }
    $dipendente_id = $conn->insert_id;
    $stmt1->close();

    // 2. Insert in users collegato al dipendente
    $stmt2 = $conn->prepare("INSERT INTO users (username, password_hash, nome, email, ruolo, dipendente_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssssi", $username, $hash, $nome, $email, $ruolo, $dipendente_id);
    if ($stmt2->execute()) {
        echo json_encode(['success' => true]);
    } else {
        // Rollback manuale: elimina il dipendente appena inserito
        $conn->query("DELETE FROM dipendenti WHERE id = $dipendente_id");
        echo json_encode(['success' => false, 'error' => 'Errore inserimento utente: ' . $conn->error]);
    }
    $stmt2->close();
    break;

    // ==================== MODIFICA UTENTE ====================
case 'modifica':
    $id    = intval($input['id'] ?? 0);
    $nome  = trim($input['nome'] ?? '');
    $email = trim($input['email'] ?? '');
    $ruolo = in_array($input['ruolo'] ?? '', ['dipendente','manager','admin']) ? $input['ruolo'] : null;
    $data_assunzione   = !empty($input['data_assunzione']) ? $input['data_assunzione'] : null;
    $livello           = !empty($input['livello_esperienza']) ? intval($input['livello_esperienza']) : null;

    if (!$id || !$ruolo) {
        echo json_encode(['success' => false, 'error' => 'Dati non validi.']);
        exit;
    }

    // Aggiorna users
    $stmt = $conn->prepare("UPDATE users SET nome = ?, email = ?, ruolo = ? WHERE id = ?");
    $stmt->bind_param("sssi", $nome, $email, $ruolo, $id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'error' => 'Errore: ' . $conn->error]);
        exit;
    }
    $stmt->close();

    // Aggiorna anche la tabella dipendenti se collegata
   $parti       = explode(' ', $nome, 2);
    $nome_dip    = $parti[0] ?? '';
    $cognome_dip = $parti[1] ?? '';

    $stmt2 = $conn->prepare("
        UPDATE dipendenti d
        JOIN users u ON u.dipendente_id = d.id
        SET d.nome              = ?,
            d.cognome           = ?,
            d.recapitieMail     = ?,
            d.data_assunzione   = ?,
            d.livello_esperienza = ?
        WHERE u.id = ?
    ");
    $stmt2->bind_param("ssssii", $nome_dip, $cognome_dip, $email, $data_assunzione, $livello, $id);
    $stmt2->execute();
    $stmt2->close();

    echo json_encode(['success' => true]);
    break;

    // ==================== RESET PASSWORD ====================
    case 'reset_pwd':
        $id       = intval($input['id'] ?? 0);
        $password = $input['password'] ?? '';

        if (!$id || strlen($password) < 8) {
            echo json_encode(['success' => false, 'error' => 'Dati non validi.']);
            exit;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param("si", $hash, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Errore database.']);
        }
        $stmt->close();
        break;

    // ==================== ELIMINA UTENTE ====================
    case 'elimina':
        $id = intval($input['id'] ?? 0);

        // Impedisci auto-eliminazione
        if ($id === intval($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'error' => 'Non puoi eliminare il tuo account.']);
            exit;
        }
        if (!$id) {
            echo json_encode(['success' => false, 'error' => 'ID non valido.']);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Errore database.']);
        }
        $stmt->close();
        break;

    default:
        echo json_encode(['success' => false, 'error' => 'Azione non riconosciuta.']);
}