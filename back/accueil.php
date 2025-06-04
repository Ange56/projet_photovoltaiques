<?php
// accueil_admin.php
session_start();

// Vérification de l'authentification admin (à adapter selon votre système)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Pour la démo, on simule un admin connecté
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_name'] = 'Admin';
}

$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pannoneau - Administration</title>
    <link rel="stylesheet" href="../front/css/accueil_admin.css">
</head>
<body>
    <!-- Remplacer la section header actuelle par : -->
    <header>
        <nav class="navbar-custom d-flex justify-content-between align-items-center px-4">
            <!-- Gauche : logo + titre -->
            <div class="d-flex align-items-center">
                <a href="accueil.html">
                    <img src="../images/logo.png" alt="Logo" class="logo-img me-2">
                </a>
                <span class="navbar-title">Panoneau</span>
            </div>

            <!-- Centre : boutons -->
            <div class="d-flex gap-2">
                <a href="accueil.html" class="nav-button active">Accueil</a>
                <a href="recherche.html" class="nav-button">Recherche</a>
                <a href="carte.html" class="nav-button">Carte</a>
                <a href="../../back/connexion.php" class="nav-button">Connexion</a>
            </div>

            <!-- Droite : info admin (ajouté pour garder l'info admin) -->
            <div class="admin-info">
                <span>Admin - <?php echo htmlspecialchars($admin_name); ?></span>
                <div class="user-avatar"><?php echo strtoupper(substr($admin_name, 0, 1)); ?></div>
            </div>
        </nav>
    </header>



    

    <div class="main-container">
        <!-- <div class="dashboard-stats">
            <div class="stat-card">
                <div class="stat-number" id="totalInstallations">0</div>
                <div class="stat-label">Installations totales</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalPanels">0</div>
                <div class="stat-label">Panneaux installés</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalPower">0</div>
                <div class="stat-label">kW de puissance</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalSurface">0</div>
                <div class="stat-label">m² de surface</div>
            </div>
        </div> -->

        <div class="content-section">
            <div class="section-header">
                <h2 class="section-title">
                    Visualisation des données
                </h2>
                <button class="add-btn" id="btnAddInstallation">
                    <img src="../images/plus.png">
                    Nouvelle installation
                </button>
            </div>

            <div id="loadingState" class="loading">
                <div class="spinner"></div>
            </div>

            <div id="dataContainer" style="display: none;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date d'installation</th>
                            <th>Nombre de panneaux</th>
                            <th>Surface (m²)</th>
                            <th>Puissance crête (kW)</th>
                            <th>Localisation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="dataTableBody">
                        <!-- Les données seront chargées via AJAX -->
                    </tbody>
                </table>
            </div>

            <div id="emptyState" class="empty-state" style="display: none;">
                <div class="empty-icon">📋</div>
                <h3>Aucune installation enregistrée</h3>
                <p>Commencez par ajouter votre première installation de panneaux solaires</p>
            </div>
        </div>
    </div>

    <!-- Modal pour ajouter/modifier une installation -->
    <div id="installationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Nouvelle installation</h2>
                <span class="close" id="btnCloseModal">&times;</span>
            </div>
            <form id="installationForm">
                <input type="hidden" id="installationId" value="">
                <div class="form-group">
                    <label for="installDate">Date d'installation</label>
                    <input type="date" id="installDate" required>
                </div>
                <div class="form-group">
                    <label for="panelCount">Nombre de panneaux</label>
                    <input type="number" id="panelCount" min="1" required>
                </div>
                <div class="form-group">
                    <label for="surface">Surface (m²)</label>
                    <input type="number" id="surface" min="0" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="power">Puissance crête (kW)</label>
                    <input type="number" id="power" min="0" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="location">Localisation</label>
                    <input type="text" id="location" required>
                </div>
                <div class="form-buttons">
                    <button type="button" class="btn-secondary" id="btnCancel">Annuler</button>
                    <button type="submit" class="add-btn" id="btnSubmit">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Messages de notification -->
    <div id="notification" class="notification">
        <span id="notificationMessage"></span>
        <button id="closeNotification">&times;</button>
    </div>

    <script src="accueil.js"></script>
</body>
</html>