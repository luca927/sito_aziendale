<?php
require_once __DIR__ . '/backend/db.php'; 
require_once __DIR__ . '/includes/auth_admin.php';

// Verifichiamo se la connessione esiste
if (!$conn) {
    die("ERRORE: Connessione al database non riuscita.");
}

// Query per i menu a tendina
$resDip = $conn->query("SELECT id, nome, cognome FROM dipendenti");
$resCant = $conn->query("SELECT id, nome FROM cantieri");
$resMezzi = $conn->query("SELECT id, nome_mezzo, targa FROM mezzi");
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex">
  <?php include 'includes/sidebar.php'; ?>
  <div class="main-content container-fluid">
    <h2 class="mb-4 fw-semibold text-primary">
      <i class="fa-solid fa-chart-line me-2"></i>Dashboard Amministrativa
    </h2>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white p-4 shadow-sm">
                <h5>👷 Dipendenti</h5>
                <h2 id="count-dipendenti" class="text-white">0</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white p-4 shadow-sm">
                <h5>🏗️ Cantieri Attivi</h5>
                <h2 id="count-cantieri">0</h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white p-4 shadow-sm">
                <h5>🚜 Mezzi Disponibili</h5>
                <h2 id="count-mezzi">0</h2>
            </div>
        </div>
    </div>

<div class="row mb-4 g-4">
    <div class="col-12">
        <div class="card shadow-sm h-100 border-left-primary">
            <div class="card-body">
                <h5 class="card-title text-primary mb-3">Registra Nuova Operazione</h5>
                <form id="formAssegna">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Tipo Attività</label>
                            <select id="selTipo" class="form-select border-primary" required>
                                <option value=""> Seleziona </option>
                                <option value="LAVORAZIONE">Lavorazione</option>
                                <option value="SPOSTAMENTO">Spostamento</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Dipendente</label>
                            <select id="selDipendente" class="form-select border-primary" required>
                                <option value=""> Seleziona Dipendente </option>
                                <?php $resDip->data_seek(0); while($d = $resDip->fetch_assoc()): ?>
                                    <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['cognome'] . " " . $d['nome']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Cantiere</label>
                            <select id="selCantiere" class="form-select border-primary" required>
                                <option value=""> Seleziona Cantiere </option>
                                <?php $resCant->data_seek(0); while($c = $resCant->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted">Mezzo</label>
                            <select id="selMezzo" class="form-select border-primary">
                                <option value=""> Seleziona Mezzo </option>
                                <?php $resMezzi->data_seek(0); while($m = $resMezzi->fetch_assoc()): ?>
                                    <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nome_mezzo']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold shadow-sm">
                                <i class="fa-solid fa-plus me-2"></i>AGGIUNGI OPERAZIONE
                            </button>
                        </div>

                        <div class="col-12">
                            <small class="text-muted" id="status-pos">
                                <i class="fa-solid fa-circle-info me-1"></i> 
                                Le coordinate vengono caricate automaticamente in base al cantiere selezionato.
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
              <div class="d-flex align-items-center">
                <span class="me-2">Mostra</span>
                <select id="entriesPerPage" class="form-select form-select-sm" style="width: auto;">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                </select>
                <span class="ms-2">voci</span>
                <button id="downloadReport" class="btn btn-outline-primary text-nowrap ms-4 fw-bold">
                  <i class="fa-solid fa-file-csv me-1"></i> Report
                </button>
              </div>
              <div class="d-flex gap-2">
                <input type="text" id="search" class="form-control" placeholder="Cerca...">
              </div>
            </div>

            <div class="table-responsive">
              <table id="tbl" class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th class="col-tipo">TIPO</th>
                    <th class="col-dipendente">DIPENDENTE</th>
                    <th class="col-cantiere">CANTIERE</th>
                    <th class="col-mezzo">MEZZO</th>
                    <th class="col-coordinate">COORDINATE</th>
                    <th class="col-data">DATA</th>
                    <th class="col-azioni">AZIONI</th>
                  </tr>
                </thead>
                <tbody id="dashboard-data"></tbody>
              </table>
            </div>
            <nav><ul id="pagination" class="pagination justify-content-end mt-3"></ul></nav>
        </div>
    </div>
  </div>
</div>

<div id="mapModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.8);">
  <div style="background:white; margin:5% auto; padding:20px; width:80%; height:80%; border-radius:15px; position:relative;">
    <button onclick="closeMap()" style="position:absolute; top:10px; right:20px; font-size:30px; border:none; background:none; cursor:pointer;">&times;</button>
    <h4 id="mapTitle" class="mb-3 text-primary fw-bold">Posizione Attività</h4>
    <div id="map" style="width:100%; height:90%; border-radius:10px; border: 1px solid #ddd;"></div>
  </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<script>
let allData = [];
let filteredData = [];
let rowsPerPage = 10;
let currentPage = 1;
let map = null;
let markerGroup = null;
let liveUpdateInterval = null;

function escapeHtml(str) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(str || ''));
    return div.innerHTML;
}

function openMap(lat, lng, dipendente) {
    // Forza numeri
    lat = parseFloat(lat);
    lng = parseFloat(lng);

    if (isNaN(lat) || isNaN(lng)) {
        alert("Coordinate non valide per questo cantiere");
        return;
    }

    // Mostra la modale
    const modal = document.getElementById('mapModal');
    modal.style.display = 'block';
    document.getElementById('mapTitle').innerText = "Posizione di: " + (dipendente || "Dipendente");

    setTimeout(() => {
        try {
            if (!map) {
                map = L.map('map').setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap'
                }).addTo(map);
                markerGroup = L.layerGroup().addTo(map);
            } else {
                map.setView([lat, lng], 15);
            }

            // Pulisce e aggiunge il marker
            if (markerGroup) {
                markerGroup.clearLayers();
                L.marker([lat, lng])
                    .addTo(markerGroup)
                    .bindPopup(`<strong>${dipendente}</strong><br>Posizione registrata`)
                    .openPopup();
            }

            map.invalidateSize();
        } catch (error) {
            console.error("Errore Leaflet:", error);
        }
    }, 300);
}

// Aspetta che il documento sia pronto per collegare l'evento alla select dei cantieri
document.addEventListener("DOMContentLoaded", () => {
    // Caricamento iniziale
    loadDashboard();

    const selectCantiere = document.getElementById("selCantiere"); // ID Corretto
    const inputLat = document.getElementById("latitudine");
    const inputLng = document.getElementById("longitudine");
    const status = document.getElementById('status-pos');

    // AUTO-COMPILAZIONE COORDINATE DA DATABASE (Roma)
    selectCantiere.addEventListener("change", async function() {
        const idCantiere = this.value;
        if (!idCantiere) {
            return;
        }

       try {
            status.innerHTML = "<i class='fa-solid fa-spinner fa-spin'></i> Caricamento coordinate...";
            const response = await fetch(`api/get_cantieri.php?id=${idCantiere}`);
            let data = await response.json();

            // FIX: Se il PHP restituisce un array [ {...} ], prendiamo il primo elemento
            if (Array.isArray(data)) {
                data = data[0];
            }

            // Verifichiamo che i dati esistano e non siano "0" o null
            if (data && data.lat && data.lng) {
                status.innerHTML = `<span class='text-success'>📍 Coordinate caricate: ${data.lat}, ${data.lng}</span>`;
            } else {
                status.innerHTML = "<span class='text-warning'>⚠️ Questo cantiere non ha coordinate valide nel database.</span>";
            }
        } catch (error) {
            console.error("Errore fetch:", error);
            status.innerHTML = "<span class='text-danger'>❌ Errore di connessione con get_cantieri.php</span>";
        }
    });
});

// 3. LA FUNZIONE PER CHIUDERE (Assicurati che ci sia)
function closeMap() {
    document.getElementById('mapModal').style.display = 'none';
    if (liveUpdateInterval) clearInterval(liveUpdateInterval);
}

// Chiusura con tasto ESC
document.addEventListener('keydown', (e) => {
    if (e.key === "Escape") closeMap();
 });

// --- CARICAMENTO DATI ---
async function loadDashboard() {
    try {
        const res = await fetch("api/dati_combinati.php");
        const data = await res.json();

         console.log("RAW DATA FROM PHP:", data);
         
        allData = data.map(item => ({
            ...item,
            lat: item.lat ? parseFloat(item.lat) : null,
            lng: item.lng ? parseFloat(item.lng) : null,
            dataFormattata: item.data ? new Date(item.data).toLocaleDateString('it-IT') : "-"
        }));

        filteredData = [...allData];
        displayPage(1);
        setupPagination();
        updateKPIs();

    } catch (err) {
        console.error("Errore fetch:", err);
    }

}

// UNICO PUNTO DI AVVIO: Quando la pagina è pronta, carica dati e mappa
document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
});

function displayPage(page) {
    currentPage = page;
    const start = (page - 1) * rowsPerPage;
    const pageData = filteredData.slice(start, start + parseInt(rowsPerPage));
    const tbody = document.getElementById("dashboard-data");
    tbody.innerHTML = "";

    pageData.forEach(row => {
        const badgeClass = row.tipo_attivita === 'SPOSTAMENTO' ? 'bg-info' : 'bg-success';

        // Coordinate corrette per questa riga
        const lat = row.lat !== null ? parseFloat(row.lat) : null;
        const lng = row.lng !== null ? parseFloat(row.lng) : null;
        const haCoord = lat !== null && lng !== null;

        // Creiamo il click handler inline SOLO se ci sono coordinate
        const action = haCoord
            ? `onclick="openMap(${lat}, ${lng}, '${row.dipendente.replace(/'/g, "\\'")}')"`
            : "";

        tbody.innerHTML += `
            <tr>
                <td class="col-tipo"><span class="badge ${badgeClass}">${row.tipo_attivita}</span></td>
                <td class="col-dipendente fw-bold text-primary">${escapeHtml(row.dipendente)}</td>
                <td class="col-cantiere">${escapeHtml(row.cantiere)}</td>
                <td class="col-mezzo">${escapeHtml(row.mezzo)}</td>
                <td class="col-coordinate">
                    <div class="coord-badge ${haCoord ? 'active' : 'disabled'}" ${action}>
                        <i class="fa-solid fa-location-dot me-1"></i>
                        ${haCoord ? 'Vedi Mappa' : 'No GPS'}
                    </div>
                </td>
                <td class="col-data">${row.dataFormattata}</td>
                <td class="col-azioni">
                    <button class="btn btn-sm btn-warning me-2" onclick="modificaAttivita(${row.id})" title="Modifica">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminaAttivita(${row.id})" title="Elimina">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
    });
}

async function updateKPIs() {
    try {
        const res = await fetch("api/kpi_counts.php");
        const counts = await res.json();

        document.getElementById('count-dipendenti').innerText = counts.dipendenti;
        document.getElementById('count-cantieri').innerText   = counts.cantieri;
        document.getElementById('count-mezzi').innerText      = counts.mezzi;

    } catch (err) {
        console.error("Errore KPI:", err);
    }
}

// (Funzioni di paginazione e ricerca rimangono uguali alle tue...)
function setupPagination() {
    const totalPages = Math.ceil(filteredData.length / rowsPerPage);
    const pagination = document.getElementById("pagination");
    if (!pagination) return;
    pagination.innerHTML = "";
    if (totalPages <= 1) return;
    for (let i = 1; i <= totalPages; i++) {
        pagination.innerHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a></li>`;
    }
}
function changePage(p) { displayPage(p); setupPagination(); }

// --- FUNZIONE ESPORTA CSV ---
function esportaInCSV() {
    if (filteredData.length === 0) {
        alert("Nessun dato da esportare.");
        return;
    }

    // Definizione intestazioni
    const headers = ["Tipo Attività", "Dipendente", "Cantiere", "Mezzo", "Coordinate", "Data"];
    
    // Mappatura dei dati
    const rows = filteredData.map(row => [
        row.tipo_attivita,
        `"${row.dipendente}"`, // Usiamo le virgolette per evitare problemi con eventuali virgole nei nomi
        `"${row.cantiere}"`,
        `"${row.mezzo}"`,
        row.lat && row.lng ? `${row.lat} ${row.lng}` : "N/D",
        row.dataFormattata
    ]);

    // Unione di intestazioni e righe
    let csvContent = "data:text/csv;charset=utf-8," 
        + headers.join(",") + "\n" 
        + rows.map(e => e.join(",")).join("\n");

    // Creazione del link di download
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    
    // Nome file con data odierna
    const dataOggi = new Date().toISOString().slice(0, 10);
    link.setAttribute("download", `report_attivita_arsnets_${dataOggi}.csv`);
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Collega la funzione al pulsante esistente nel DOM
document.getElementById('downloadReport').addEventListener('click', esportaInCSV);

// --- GESTIONE INVIO FORM (AGGIUNGI/MODIFICA) ---
document.getElementById('formAssegna').addEventListener('submit', async function(e) {
    e.preventDefault();

    const idAttivita = document.getElementById('attivita_id')?.value || '';
    const isUpdate = !!idAttivita;

    const payload = {
        id: idAttivita,
        tipo_attivita: document.getElementById('selTipo').value,
        dipendente_id: document.getElementById('selDipendente').value,
        cantiere_id: document.getElementById('selCantiere').value,
        mezzo_id: document.getElementById('selMezzo').value,
    };

    try {
        const endpoint = isUpdate ? 'api/aggiorna_attivita.php' : 'api/assegna_attivita.php';
        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (result.success) {
            alert(isUpdate ? "Operazione aggiornata con successo!" : "Operazione registrata con successo!");
            
            // Reset del form
            document.getElementById('formAssegna').reset();
            if (document.getElementById('attivita_id')) {
                document.getElementById('attivita_id').value = '';
            }
            document.querySelector('#formAssegna button[type="submit"]').innerHTML = '<i class="fa-solid fa-plus me-2"></i>AGGIUNGI OPERAZIONE';
            
            loadDashboard();
        } else {
            alert("Errore durante il salvataggio: " + result.error);
        }
    } catch (error) {
        console.error("Errore tecnico:", error);
        alert("Errore di connessione al server.");
    }
});

document.getElementById("search").addEventListener("input", filtraTabella);

function filtraTabella() {
    const testo = document.getElementById("search").value.toLowerCase();
    const righe = document.querySelectorAll("#dashboard-data tr");

    righe.forEach(riga=> {
        const tipo = riga.querySelector(".col-tipo").innerText.toLowerCase();
        const dip = riga.querySelector(".col-dipendente").innerText.toLowerCase();
        const cant = riga.querySelector(".col-cantiere").innerText.toLowerCase();
        const mez = riga.querySelector(".col-mezzo").innerText.toLowerCase();
        const coord = riga.querySelector(".col-coordinate").innerText.toLowerCase();
        const data = riga.querySelector(".col-data").innerText.toLowerCase();

        const match = 
        dip.includes(testo) ||
        cant.includes(testo) ||
        mez.includes(testo) ||
        coord.includes(testo) ||
        tipo.includes(testo) ||
        data.includes(testo);
        riga.style.display = match ? "" : "none";
    })
}

function ottieniPosizione() {
    if (navigator.geolocation) {
        //opzioni per alta precisione
        const options = {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        };
        navigator.geolocation.getCurrentPosition(success, error, options); 
    } else {
        alert("Geolocalizzazione non supportata dal browser.");
    }

    const btn = document.getElementById('btnLoc');
    const status = document.getElementById('status-pos');
    
    // Feedback visivo: disabilita il tasto e mostra caricamento
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>...';
    status.innerText = "Ricerca segnale GPS in corso...";

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                // Successo
                document.getElementById('latitudine').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitudine').value = position.coords.longitude.toFixed(6);
                
                status.innerHTML = "<span class='text-success'>✅ Posizione acquisita con successo!</span>";
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check"></i> Fatto';
            },
            (error) => {
                // Errore
                btn.disabled = false;
                btn.innerHTML = 'Rileva';
                status.innerHTML = "<span class='text-danger'>❌ Errore: Assicurati di aver dato i permessi GPS al browser.</span>";
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    } else {
        alert("Geolocalizzazione non supportata");
    }
}

function success(pos) {
    const crd = pos.coords;
    document.getElementById('latitudine').value = crd.latitude.toFixed(6);
    document.getElementById('longitudine').value = crd.longitude.toFixed(6);
    console.log("Posizione rilevata con precisione di " + crd.accuracy + " metri.");
}
function error(err) {
    console.warn(`ERRORE (${err.code}): ${err.message}`);
    alert("Impossibile ottenere la posizione. Assicurati di aver concesso i permessi e di avere una connessione GPS attiva."); 
}

// --- MODIFICA ATTIVITA ---
function modificaAttivita(id) {
    const attivita = allData.find(a => String(a.id) === String(id));
    
    if (!attivita) {
        alert('Attività non trovata');
        return;
    }
    
    // 1. Campi semplici
    document.getElementById('selTipo').value = attivita.tipo_attivita;
    document.getElementById('selDipendente').value = String(attivita.dipendente_id);
    document.getElementById('selMezzo').value = attivita.mezzo_id || '';

    // 2. Cantiere - imposta il valore SENZA dispatchEvent
    document.getElementById('selCantiere').value = attivita.cantiere_id;

    // 4. Gestione ID nascosto e UI
    document.getElementById('formAssegna').scrollIntoView({ behavior: 'smooth' });
    
    let inputId = document.getElementById('attivita_id');
    if (!inputId) {
        inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.id = 'attivita_id';
        document.getElementById('formAssegna').appendChild(inputId);
    }
    inputId.value = id;
    
    document.querySelector('#formAssegna button[type="submit"]').innerHTML = 
        '<i class="fa-solid fa-floppy-disk me-2"></i> AGGIORNA OPERAZIONE';
}

// --- ELIMINA ATTIVITA ---
function eliminaAttivita(id) {
    if (!confirm('Sei sicuro di voler eliminare questa attività?')) {
        return;
    }
    
    fetch('api/elimina_attivita.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            alert('Attività eliminata con successo!');
            loadDashboard();
        } else {
            alert('Errore: ' + result.error);
        }
    })
    .catch(err => {
        console.error('Errore:', err);
        alert('Errore durante l\'eliminazione');
    });
}

</script>

<style>
/* CSS PROFESSIONALE B2B */
.main-content { 
    margin-left: 260px !important; /* Deve combaciare con la sidebar */
    width: calc(100% - 260px) !important;
    transition: all 0.3s; 
    padding: 2rem; 
    background: #f8f9fc; 
    min-height: 100vh; 
}

.card { border: none; border-radius: 12px; transition: transform 0.2s; }
.border-left-primary { border-left: 0.25rem solid #4e73df !important; }

.table thead th {
    background-color: #f8f9fc;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #4e73df;
    padding: 15px;
}

.badge { padding: 0.5em 0.8em; border-radius: 50rem; font-weight: 600; }

/* COORDINATE BADGE */
.coord-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 12px;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 500;
    transition: all 0.2s;
    border: 1px solid transparent;
}
.coord-badge.active {
    background: #eef2ff;
    color: #4e73df;
    border-color: #4e73df;
    cursor: pointer;
}
.coord-badge.active:hover {
    background: #4e73df;
    color: white;
}
.coord-badge.disabled {
    background: #f8f9fc;
    color: #b7b9cc;
    border-color: #e3e6f0;
    cursor: not-allowed;
}

#map { box-shadow: inset 0 0 10px rgba(0,0,0,0.1); }

#sidebar {
    background: #1a237e; /* Blu Notte Professionale */
    background: linear-gradient(180deg, #1a237e 0%, #0d47a1 100%);
    min-width: 260px;
    min-height: 100vh;
    transition: all 0.3s;
    box-shadow: 4px 0 10px rgba(0,0,0,0.1);
}

#sidebar .nav-link {
    color: rgba(255, 255, 255, 0.8) !important;
    font-weight: 500;
    padding: 12px 20px;
    border-left: 4px solid transparent;
    transition: all 0.2s;
}

#sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    color: #fff !important;
    border-left-color: #4fc3f7; /* Azzurro brillante */
}

#sidebar .nav-link i {
    width: 25px;
    color: #4fc3f7;
}

#sidebar .collapse {
    background: rgba(0, 0, 0, 0.15);
}

#sidebar .nav-item .small {
    font-size: 0.85rem;
    padding-left: 50px !important;
}

#sidebar .active-link {
    background: rgba(255, 255, 255, 0.1);
    border-left-color: #4fc3f7 !important;
    color: #fff !important;
}
.col-azioni {
    width: 120px;
    text-align: center;
}
</style>

<?php include 'includes/footer.php'; ?>