<div id="sidebar" class="text-white shadow">

    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a href="dashboard.php" class="nav-link">
                <i class="fa-solid fa-gauge me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#menuDipendenti">
                <span><i class="fa-solid fa-users me-2"></i> Dipendenti</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </a>
            <div class="collapse" id="menuDipendenti"> 
                <ul class="nav flex-column ps-3">
                    <li><a href="dipendenti.php" class="nav-link small active-link">Lista Anagrafica</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center" 
               data-bs-toggle="collapse" href="#menuMezzi">
                <span><i class="fa-solid fa-truck-pickup me-2"></i> Mezzi</span>
                <i class="fa-solid fa-chevron-down small"></i>
            </a>
            <div class="collapse" id="menuMezzi">
                <ul class="nav flex-column ps-3">
                    <li><a href="lista_mezzi.php" class="nav-link small">Parco Veicoli</a></li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a href="cantieri.php" class="nav-link">
                <i class="fa-solid fa-map-location-dot me-2"></i> Cantieri
            </a>
        </li>

        <?php if (isset($_SESSION['ruolo']) && $_SESSION['ruolo'] === 'admin'): ?>
        <li class="nav-item">
            <a href="gestione_utenti.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'gestione_utenti.php' ? 'active' : '' ?>">
                <i class="fa-solid fa-users-cog me-2"></i> Gestione Utenti
            </a>
        </li>
            <?php endif; ?>

        <li class="nav-item">
            <a href="timbratura.php" class="nav-link">
                <i class="fa-solid fa-tasks me-2"></i> Timbrature
            </a>
        </li>

        <li class="nav-item">
            <a href="tracciamento.php" class="nav-link">
                <i class="fa-solid fa-clipboard-list me-2"></i> Tracciamento
            </a>
        </li>
    </ul>
</div>