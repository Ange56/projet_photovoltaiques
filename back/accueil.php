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
    <title>Pannoneau - Admin</title>
    <link rel="stylesheet" href="../front/css/accueil_admin.css">
</head>
<body>
    <!-- Remplacer la section header actuelle par : -->
    <header>
        <nav class="navbar-custom d-flex justify-content-between align-items-center px-4">
            <!-- Gauche : logo + titre -->
            <div class="d-flex align-items-center">
                <a href="accueil.php">
                    <img src="../images/logo.png" alt="Logo" class="logo-img me-2">
                </a>
                <span class="navbar-title">Panoneau</span>
            </div>

            <!-- Centre : boutons -->
            <div class="d-flex gap-2">
                <a href="accueil.php" class="nav-button active">Accueil</a>
                <a href="#" class="nav-button">Recherche</a>
                <a href="#" class="nav-button">Carte</a>
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

    <!-- Nouvelle structure HTML pour la modale -->
	<div id="installationModal" class="modal">
		<div class="modal-content">
			<!-- Header fixe -->
			<div class="modal-header">
				<h2 id="modalTitle">Nouvelle installation</h2>
				<span class="close" id="btnCloseModal">&times;</span>
			</div>
			
			<!-- Corps avec scroll -->
			<div class="modal-body">
				<form id="installationForm">
					<h3>Installations</h3>
					<input type="hidden" id="installationId" value="">
					<div class="form-group">
						<label for="installDate">Date d'installation</label>
						<input type="date" id="installDate" required>
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
						<label for="localisation">Localisation</label>
						<input type="text" id="localisation" required>
					</div>

					<h3>Placement</h3>
					<div class="form-group">
						<label for="orientation">Orientation</label>
						<input type="number" id="orientation" min="1" required>
					</div>
					<div class="form-group">
						<label for="orientationOPT">Orientation Optimum</label>
						<input type="number" id="orientationOPT" min="1" required>
					</div>
					<div class="form-group">
						<label for="pente">Pente</label>
						<input type="number" id="pente" min="1" required>
					</div>
					<div class="form-group">
						<label for="penteOPT">Pente Optimum</label>
						<input type="number" id="penteOPT" min="1" required>
					</div>
					<div class="form-group">
						<label for="installateur">Installateur</label>
						<input type="text" id="installateur" required>
					</div>
					<div class="form-group">
						<label for="production">Production (PVGIS)</label>
						<input type="number" id="production" min="1" required>
					</div>
					
					<h3>Panneau</h3>
					<div class="form-group">
						<label for="modele">Modèle</label>
						<input type="text" id="modele" required>
					</div>
					<div class="form-group">
						<label for="marque">Marque</label>
						<input type="text" id="marque" required>
					</div>
					<div class="form-group">
						<label for="nombre">Nombre de panneaux</label>
						<input type="number" id="nombre" min="1" required>
					</div>
					
					<h3>Adresse</h3>
					<div class="form-group">
						<label for="lat">Latitude</label>
						<input type="number" id="lat" step="any" required>
					</div>
					<div class="form-group">
						<label for="long">Longitude</label>
						<input type="number" id="long" step="any" required>
					</div>
					<div class="form-group">
						<label for="localite">Localité</label>
						<input type="text" id="localite" required>
					</div>
					<div class="form-group">
						<label for="codePostal">Code Postal</label>
						<input type="text" id="codePostal" required>
					</div>
					<div class="form-group">
						<label for="zoneAdm">Zone Administrative</label>
						<input type="text" id="zoneAdm" required>
					</div>
					<div class="form-group">
						<label for="pays">Pays</label>
						<input type="text" id="pays" required>
					</div>
					
					<h3>Onduleur</h3>
					<div class="form-group">
						<label for="modeleOnd">Modèle</label>
						<input type="text" id="modeleOnd" required>
					</div>
					<div class="form-group">
						<label for="marqueOnd">Marque</label>
						<input type="text" id="marqueOnd" required>
					</div>
					<div class="form-group">
						<label for="nombreOnd">Nombre</label>
						<input type="number" id="nombreOnd" min="1" required>
					</div>
				</form>
			</div>
			
			<!-- Boutons fixes en bas -->
			<div class="form-buttons">
				<button type="button" class="btn-secondary" id="btnCancel">Annuler</button>
				<button type="submit" class="add-btn" id="btnSubmit" form="installationForm">Ajouter</button>
			</div>
		</div>
	</div>

    <!-- Messages de notification -->
    <div id="notification" class="notification">
        <span id="notificationMessage"></span>
        <button id="closeNotification">&times;</button>
    </div>

    <script src="accueil.js"></script>




     <!-- Footer -->
    <footer class="footer-custom">
        <div class="footer-content">
            <!-- Gauche : Noms avec LinkedIn -->
            <div class="footer-left">
                <span class="footer-name">Tallulah DRENO-TABOT 
                    <a class="linkedin-link" target="_blank" href="https://www.linkedin.com/in/tallulah-dreno-tabot-9406842a3/">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </span>
                <span class="footer-name">Angèle STUTZ 
                    <a class="linkedin-link" target="_blank" href="https://www.linkedin.com/in/ang%C3%A8le-stutz-2b76832a3/">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </span>
                <span class="footer-name">Etienne DECAMPS 
                    <a class="linkedin-link" target="_blank" href="#">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </span>
            </div>

            <!-- Centre : Groupe -->
            <div class="footer-center">
                <span class="footer-group">Groupe 11</span>
            </div>

            <!-- Droite : Année -->
            <div class="footer-right">
                <span class="footer-year">2025</span>
            </div>
        </div>
    </footer>
</body>
</html>