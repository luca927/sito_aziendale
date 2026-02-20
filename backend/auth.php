<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* --- CONFIG SICUREZZA --- */
$session_timeout = 3600; // 1 ora
$session_regen_time = 300; // 5 minuti

/* --- 1. Protezione session fixation --- */
if (!isset($_SESSION['created'])) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > $session_regen_time) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}

/* --- 2. Protezione hijacking (Fingerprint) --- */
/*$current_fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']);
if (!isset($_SESSION['fingerprint'])) {
    $_SESSION['fingerprint'] = $current_fingerprint;
} else {
    if ($_SESSION['fingerprint'] !== $current_fingerprint) {
        session_unset();
        session_destroy();
        
        //FIX: Controlla se è richiesta JSON
        if (isJsonRequest()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                "success" => false, 
                "error" => "Sessione non valida",
                "redirect" => "login.php?error=session_hijack"
            ]);
            exit;
        }
        
        header("Location: login.php?error=session_hijack");
        exit;
    }
}*/

/* --- 3. Timeout sessione --- */
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    
    // FIX: Controlla se è richiesta JSON
    if (isJsonRequest()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false, 
            "error" => "Sessione scaduta",
            "redirect" => "login.php?error=session_timeout"
        ]);
        exit;
    }
    
    header("Location: login.php?error=session_timeout");
    exit;
}
$_SESSION['last_activity'] = time();

/* --- 4. Controllo accesso --- */
if (!isset($_SESSION['user_id']) || !isset($_SESSION['loggedin'])) {
    
    // FIX: Usa la funzione helper
    if (isJsonRequest()) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false, 
            "error" => "Autenticazione richiesta",
            "redirect" => "login.php?error=not_logged_in"
        ]);
        exit;
    }

    // Redirect normale per le pagine PHP
    header("Location: login.php?error=not_logged_in");
    exit;
}

/* --- 4.1 CARICA DATI UTENTE DAL DB (MANCANTE!) --- */
if (!isset($_SESSION['username']) || !isset($_SESSION['ruolo'])) {
    // Ricarica dati utente dal DB
    require_once __DIR__ . '/db.php';  // ← Assicurati che db.php sia incluso
    
    $stmt = $conn->prepare("SELECT username, ruolo, nome, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['ruolo'] = $user['ruolo'];   
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['email'] = $user['email'];
    } else {
        // Utente non trovato nel DB → logout forzato
        session_unset();
        session_destroy();
        if (isJsonRequest()) {
            http_response_code(401);
            echo json_encode(["success" => false, "error" => "Utente non trovato"]);
            exit;
        }
        header("Location: login.php?error=user_not_found");
        exit;
    }
    $stmt->close();
}


/* --- 5. Funzioni di Utilità --- */

/**
 * Verifica se la richiesta corrente è JSON/AJAX
 */
function isJsonRequest() {
    // Controlla header X-Requested-With (jQuery/Axios)
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        return true;
    }
    
    // Controlla se l'Accept header richiede JSON
    if (isset($_SERVER['HTTP_ACCEPT']) && 
        strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        return true;
    }
    
    // Controlla se il Content-Type è JSON
    if (isset($_SERVER['CONTENT_TYPE']) && 
        strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
        return true;
    }
    
    // Controlla se siamo in una cartella /api/
    if (isset($_SERVER['REQUEST_URI']) && 
        strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        return true;
    }
    
    return false;
}

/**
 * Verifica se l'utente è admin
 */
function isAdmin() {
    return isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'admin';
}

/**
 * Registra un'azione nel log di sistema
 */
function controlloAzioni($azione, $dettagli = "") {
    global $conn;

    $user_id = $_SESSION['user_id'] ?? 0;
    $username = $_SESSION['username'] ?? 'Sconosciuto';
    $ip = $_SERVER['REMOTE_ADDR'];

    $stmt = $conn->prepare("INSERT INTO registro_controllo (user_id, username, azione, dettagli, indirizzo_ip) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $username, $azione, $dettagli, $ip);
    $stmt->execute();
    $stmt->close();
}
?>