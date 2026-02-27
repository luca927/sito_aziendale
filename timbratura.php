<?php
require_once __DIR__ . '/backend/auth.php';
include 'includes/header.php';
?>

<style>
/* ===== RESET E BASE ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html, body {
    width: 100%;
    overflow-x: hidden;
}

.navbar {
    z-index: 1055;
}

body {
    padding-top: 70px;
    background: #f5f7fa;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.layout {
    display: flex;
    width: 100%;
    min-height: 100vh;
}

/* ===== MAIN CONTENT ===== */
.main-content {
    position: relative;
    z-index: 0;
}

/* ===== CONTAINER FLUID RESPONSIVE ===== */
/*.container-fluid {
    width: 100%;
    padding-left: 0;
    padding-right: 0;
    margin-left: auto;
    margin-right: auto;
}*/

/* ===== CLOCK CONTAINER ===== */
.clock-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    text-align: center;
}

.clock-display {
    font-size: 2.5rem;
    font-weight: bold;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
    margin-bottom: 10px;
}

.date-display {
    font-size: 1rem;
    opacity: 0.9;
    text-transform: capitalize;
}

/* ===== TIMBRATURA CARD ===== */
.timbratura-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    margin-bottom: 15px;
}

.timbratura-card h3,
.timbratura-card h5 {
    font-size: 1.1rem;
    margin-bottom: 15px;
    color: #333;
}

/* ===== FORM SELECT ===== */
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
    color: #333;
}

.form-select,
.form-control {
    font-size: 16px; /* Evita zoom su iOS */
    padding: 10px 12px;
    border-radius: 8px;
    border: 1px solid #ddd;
    width: 100%;
    transition: border-color 0.3s;
}

.form-select:focus,
.form-control:focus {
    border-color: #667eea;
    outline: none;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

/* ===== PULSANTI TIMBRATURA ===== */
.buttons-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    width: 100%;
}

.btn-timbratura {
    padding: 25px 15px;
    font-size: 1rem;
    font-weight: bold;
    border-radius: 12px;
    border: none;
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    cursor: pointer;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.btn-timbratura:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,0.2);
}

.btn-timbratura:active {
    transform: translateY(0);
}

.btn-timbratura i {
    font-size: 1.8rem;
    margin-bottom: 8px;
    display: block;
}

.btn-entrata { background: linear-gradient(135deg, #28a745, #20c997); color: white; }
.btn-uscita  { background: linear-gradient(135deg, #dc3545, #fd7e14); color: white; }

button.btn-entrata,
button.btn-entrata:focus,
button.btn-entrata:focus-visible,
button.btn-entrata:hover {
    background: linear-gradient(135deg, #28a745, #20c997) !important;
    color: white !important;
    box-shadow: none !important;
    outline: none !important;
}

/* ===== STATUS LOCATION ===== */
.status-location {
    padding: 12px;
    background: #f0f0f0;
    border-radius: 8px;
    font-size: 0.9rem;
    color: #666;
    text-align: center;
    margin-top: 12px;
}

.status-location.success {
    background: #d4edda;
    color: #155724;
}

.status-location.error {
    background: #f8d7da;
    color: #721c24;
}

/* ===== CRONOLOGIA ITEMS ===== */
.cronologia-item {
    background: #f8f9fa;
    border-left: 4px solid #0056D2;
    padding: 12px;
    margin-bottom: 10px;
    border-radius: 8px;
    transition: all 0.2s;
}

.cronologia-item:hover {
    background: #e9ecef;
    transform: translateX(3px);
}

.cronologia-item .d-flex {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 10px;
    flex-wrap: wrap;
}

.badge {
    padding: 5px 10px;
    font-size: 0.8rem;
    border-radius: 20px;
    color: white;
    white-space: nowrap;
}

.badge-entrata {
    background: #28a745;
}

.badge-uscita {
    background: #dc3545;
}

.cronologia-item strong {
    display: block;
    margin: 5px 0;
    font-size: 0.95rem;
    color: #333;
}

.cronologia-item .small {
    font-size: 0.85rem;
    color: #6c757d;
}

.cronologia-item .text-end {
    margin-left: auto;
    text-align: right;
}

/* ===== RESPONSIVE TABLET (768px) ===== */
@media (max-width: 768px) {
    body {
        padding-top: 60px;
    }

    .main-content {
        padding: 15px 12px;
    }

    .clock-container {
        padding: 25px 15px;
        margin-bottom: 15px;
    }

    .clock-display {
        font-size: 2rem;
        margin-bottom: 8px;
    }

    .date-display {
        font-size: 0.95rem;
    }

    .timbratura-card {
        padding: 15px;
        margin-bottom: 12px;
    }

    .timbratura-card h3,
    .timbratura-card h5 {
        font-size: 1rem;
        margin-bottom: 12px;
    }

    .form-select,
    .form-control {
        font-size: 16px;
        padding: 10px;
    }

    .btn-timbratura {
        padding: 20px 12px;
        font-size: 0.95rem;
    }

    .btn-timbratura i {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .row.g-3 {
        gap: 10px;
    }

    .cronologia-item {
        padding: 10px;
        margin-bottom: 8px;
    }

    .cronologia-item .d-flex {
        flex-direction: column;
        gap: 8px;
    }

    .cronologia-item .text-end {
        margin-left: 0;
        margin-top: 8px;
    }

    .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
    }
}

/* ===== RESPONSIVE MOBILE (576px) ===== */
@media (max-width: 576px) {
    body {
        padding-top: 56px;
        background: #f5f7fa;
    }

    .main-content {
        padding: 12px 10px;
    }

    .container-fluid {
        width: 100%;
        padding: 0;
    }

    .clock-container {
        padding: 20px 12px;
        margin-bottom: 12px;
        border-radius: 12px;
    }

    .clock-display {
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .date-display {
        font-size: 0.9rem;
    }

    .timbratura-card {
        padding: 12px;
        margin-bottom: 10px;
        border-radius: 10px;
    }

    .timbratura-card h3 {
        font-size: 0.95rem;
        margin-bottom: 10px;
    }

    .timbratura-card h5 {
        font-size: 0.9rem;
        margin-bottom: 10px;
        border-bottom-width: 1px;
        padding-bottom: 8px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .form-select,
    .form-control {
        font-size: 16px !important;
        padding: 10px 10px;
        border-radius: 8px;
    }

    .row.g-3 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .btn-timbratura {
        padding: 18px 10px;
        font-size: 0.9rem;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.12);
    }

    .btn-timbratura i {
        font-size: 1.4rem;
        margin-bottom: 4px;
    }

    .status-location {
        padding: 10px;
        font-size: 0.85rem;
        margin-top: 10px;
        border-radius: 8px;
    }

    .cronologia-item {
        padding: 10px;
        margin-bottom: 8px;
        border-left-width: 3px;
        border-radius: 8px;
    }

    .cronologia-item .d-flex {
        flex-direction: column;
        gap: 6px;
    }

    .cronologia-item strong {
        font-size: 0.9rem;
        margin: 4px 0;
    }

    .cronologia-item .small {
        font-size: 0.8rem;
    }

    .cronologia-item .text-end {
        margin-left: 0;
        margin-top: 6px;
        font-size: 0.8rem;
    }

    .badge {
        font-size: 0.7rem;
        padding: 3px 6px;
    }

    /* Nascondi scrollbar orizzontale */
    ::-webkit-scrollbar {
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
}

/* ===== EXTRA SMALL (320px) ===== */
@media (max-width: 360px) {
    .clock-display {
        font-size: 1.5rem;
    }

    .btn-timbratura {
        padding: 15px 8px;
        font-size: 0.85rem;
    }

    .btn-timbratura i {
        font-size: 1.2rem;
    }
}

/* ===== LANDSCAPE MODE ===== */
@media (max-height: 500px) and (max-width: 768px) {
    .main-content {
        padding: 10px;
    }

    .clock-container {
        padding: 15px 12px;
        margin-bottom: 10px;
    }

    .clock-display {
        font-size: 1.5rem;
        margin-bottom: 5px;
    }

    .timbratura-card {
        padding: 10px;
    }

    .btn-timbratura {
        padding: 12px 10px;
        font-size: 0.85rem;
    }
}

.layout,
.main-content,
.container-fluid {
    z-index: auto !important;
}

#sidebar {
    z-index: 1020;
}

/* ===== UTILITY ===== */
.text-center {
    text-align: center;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-3 {
    margin-bottom: 1rem;
}

.mb-4 {
    margin-bottom: 1.5rem;
}

.mt-1 {
    margin-top: 0.25rem;
}

.mt-3 {
    margin-top: 1rem;
}

.me-2 {
    margin-right: 0.5rem;
}

.py-3 {
    padding: 1rem 0;
}

.text-muted {
    color: #6c757d;
}

.text-danger {
    color: #dc3545;
}

.border-bottom {
    border-bottom: 1px solid #dee2e6;
}

.pb-3 {
    padding-bottom: 1rem;
}

.fw-bold {
    font-weight: 600;
}

/* ========================================
   MOBILE + TABLET (fino a 992px)
   Layout centrato tipo smartphone
   ======================================== */

@media (max-width: 992px) {

    /* ---- RESET SICUREZZA ---- */
    html, body {
        overflow-x: hidden !important;
    }

    * {
        box-sizing: border-box;
    }

    body {
        padding-top: 56px;
        margin: 0;
    }

    /* ---- NASCONDI SIDEBAR + LOGOUT ---- */
    #sidebar,
    .sidebar,
    aside,
    [class*="logout"],
    [class*="sign-out"],
    button[class*="logout"],
    a[class*="logout"],
    .logout-btn,
    .col-md-3,
    .col-lg-2,
    .col-xl-2 {
        display: none !important;
    }

    /* ---- DROPDOWN NAVBAR ---- */
    .navbar .dropdown-menu {
        position: absolute !important;
        right: 10px;
        left: auto !important;
        top: 100%;
        z-index: 9999;
    }

    /* ---- LAYOUT CENTRATO ---- */
    .layout {
        display: flex !important;
        justify-content: center !important;
    }

    .main-content {
        width: 92% !important;
        max-width: 500px; /* leggermente più largo per tablet */
        margin: 0 auto !important;
    }

    .content,
    .container,
    .container-fluid {
        margin-left: 0 !important;
        padding-left: 0 !important;
    }

    /* ---- OROLOGIO ---- */
    .clock-container {
        padding: 20px 12px;
        margin-bottom: 15px;
        border-radius: 12px;
    }

    .clock-display {
        font-size: 1.8rem;
        margin-bottom: 5px;
    }

    .date-display {
        font-size: 0.9rem;
    }

    /* ---- CARD TIMBRATURA ---- */
    .timbratura-card {
        padding: 15px;
        margin-bottom: 12px;
        border-radius: 10px;
    }

    .timbratura-card h3 {
        font-size: 0.95rem;
        margin-bottom: 15px;
    }

    .timbratura-card h5 {
        font-size: 0.9rem;
        margin-bottom: 10px;
        padding-bottom: 8px;
    }

    /* ---- FORM ---- */
    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        font-size: 0.9rem;
        margin-bottom: 6px;
    }

    .form-select,
    .form-control {
        font-size: 16px !important; /* evita zoom su iOS */
        padding: 10px 10px;
    }

    /* ---- PULSANTI COLONNA ---- */
    .buttons-container {
        display: flex !important;
        flex-direction: column !important;
        gap: 12px;
        width: 100%;
    }

    .btn-timbratura {
        width: 100% !important;
        padding: 16px 10px !important;
        font-size: 0.95rem;
        margin: 0 !important;
    }

    .btn-timbratura i {
        font-size: 1.3rem;
        margin-bottom: 4px;
    }

    /* ---- GPS STATUS ---- */
    .status-location {
        padding: 10px;
        font-size: 0.85rem;
        margin-top: 10px;
    }

    /* ---- CRONOLOGIA ---- */
    .cronologia-item {
        padding: 10px;
        margin-bottom: 8px;
    }

    .cronologia-item .d-flex {
        flex-direction: column;
        gap: 6px;
    }

    .cronologia-item .text-end {
        margin-left: 0;
        margin-top: 6px;
        font-size: 0.8rem;
    }

    .badge {
        font-size: 0.7rem;
        padding: 3px 6px;
    }
}

</style>

<div class="layout">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <div class="container-fluid">
            <!-- Orologio -->
            <div class="clock-container text-center">
                <div id="clock" class="clock-display">--:--:--</div>
                <div id="current-date" class="date-display"></div>
            </div>

            <!-- Card Timbratura -->
            <div class="timbratura-card">
                <h3 class="mb-4"><i class="fas fa-clock"></i> Timbra Presenza</h3>
                
                <div class="form-group mb-4">
                    <label class="fw-bold mb-2">Causale (Opzionale):</label>
                    <select id="causale" class="form-select">
                        <option value="">Lavoro Ordinario</option>
                        <option value="1">Straordinario</option>
                        <option value="2">Permesso</option>
                    </select>
                </div>

                <div class="buttons-container">
                    <button onclick="effettuaTimbratura('Entrata')" class="btn-timbratura btn-entrata">
                        <i class="fas fa-sign-in-alt"></i>
                        ENTRATA
                    </button>
                    <button onclick="effettuaTimbratura('Uscita')" class="btn btn-danger btn-timbratura">
                        <i class="fas fa-sign-out-alt"></i>
                        USCITA
                    </button>
                </div>

                <div id="status-location" class="status-location">
                    <i class="fas fa-location-dot"></i> Ricerca posizione GPS...
                </div>
            </div>

            <!-- Cronologia Timbrature -->
            <div class="timbratura-card">
                <h5 class="fw-bold border-bottom pb-3 mb-3">
                    <i class="fas fa-history me-2"></i>Timbrature di Oggi
                </h5>
                <div id="cronologia-list">
                    <p class="text-muted text-center py-3">Nessuna timbratura registrata oggi.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let serverTime;

    async function syncClock() {
        try {
            const res = await fetch('api/get_server_time.php');
            const data = await res.json();
            serverTime = new Date(data.datetime);
            
            document.getElementById('current-date').innerText = serverTime.toLocaleDateString('it-IT', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch (error) {
            console.error('Errore sincronizzazione orologio:', error);
        }
    }

    setInterval(() => {
        if (serverTime) {
            serverTime.setSeconds(serverTime.getSeconds() + 1);
            document.getElementById('clock').innerText = serverTime.toLocaleTimeString('it-IT');
        }
    }, 1000);

    async function caricaCronologia() {
        try {
            const res = await fetch('api/get_timbrature_oggi.php');
            const data = await res.json();
            const container = document.getElementById('cronologia-list');
            
            if (!Array.isArray(data) || data.length === 0) {
                container.innerHTML = '<p class="text-muted text-center py-3">Nessuna timbratura registrata oggi.</p>';
                return;
            }

            container.innerHTML = data.map(t => {
                const dataOra = new Date(t.data_ora_server);
                const ora = dataOra.toLocaleTimeString('it-IT', {hour: '2-digit', minute:'2-digit'});
                const badgeClass = t.tipo === 'Entrata' ? 'badge-entrata' : 'badge-uscita';
                
                return `
                    <div class="cronologia-item">
                        <div class="d-flex">
                            <div>
                                <span class="badge ${badgeClass} me-2">
                                    ${t.tipo.toUpperCase()}
                                </span>
                                <strong>${t.cantiere_nome || 'Cantiere non specificato'}</strong>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock"></i> ${ora}
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="small text-muted">
                                    <i class="fas fa-location-dot"></i> ${t.distanza_rilevatore}m
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        } catch (err) {
            console.error("Errore caricamento cronologia:", err);
            document.getElementById('cronologia-list').innerHTML = 
                '<p class="text-danger text-center py-3">Errore nel caricamento delle timbrature</p>';
        }
    }

    async function effettuaTimbratura(tipo) {
        if (!navigator.geolocation) {
            alert("❌ Il tuo browser non supporta la geolocalizzazione.");
            return;
        }

        const statusEl = document.getElementById('status-location');
        statusEl.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Acquisizione coordinate in corso...';

        navigator.geolocation.getCurrentPosition(async (position) => {
            const payload = {
                tipo: tipo,
                lat: position.coords.latitude,
                lng: position.coords.longitude,
                causale_id: document.getElementById('causale').value || null
            };

            try {
                const res = await fetch('api/add_timbratura.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const responseText = await res.text();

                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (e) {
                    throw new Error('Risposta non valida dal server: ' + responseText.substring(0, 100));
                }
                
                if (!result.success && result.redirect) {
                    alert(result.error);
                    window.location.href = result.redirect;
                    return;
                }
                
                if (result.success) {
                    const cantiereInfo = result.cantiere ? `\n📍 Cantiere: ${result.cantiere}` : '';
                    const distanzaInfo = result.distanza ? `\n📏 Distanza: ${result.distanza}m` : '';
                    
                    alert(`✅ Timbratura ${tipo} registrata alle ${result.ora}!${cantiereInfo}${distanzaInfo}`);
                    
                    statusEl.innerHTML = '<i class="fas fa-check-circle text-success"></i> Posizione acquisita';
                    statusEl.classList.add('success');
                    await caricaCronologia();
                    document.getElementById('causale').value = '';
                } else {
                    alert("❌ " + (result.error || 'Errore sconosciuto'));
                    statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Errore';
                    statusEl.classList.add('error');
                }
                
            } catch (err) {
                console.error("=== ERRORE ===", err);
                alert("❌ Errore di comunicazione con il server.");
                statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Errore di comunicazione';
                statusEl.classList.add('error');
            }
        }, (err) => {
            console.error("Errore GPS:", err);
            alert("❌ Errore GPS: Attiva la geolocalizzazione sul tuo dispositivo.");
            statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i> Geolocalizzazione negata';
            statusEl.classList.add('error');
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    }

    syncClock();
    caricaCronologia();
</script>

<?php include 'includes/footer.php'; ?>