<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: connexion.php');
    exit;
}

$nom_utilisateur = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://fonts.googleapis.com/css2?family=Lalezar&family=Marko+One&family=Roboto&family=Stint+Ultra+Expanded&display=swap" rel="stylesheet"><!--importation de Roboto et Lalezar-->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet"><!--importation des icones de fontawesome-->

        <title>Pannoneau - Administration</title>
        <link rel="stylesheet" href="../front/css/accueil_admin.css">
        <link rel="icon" type="image/png" href="../images/logo.png">

        <script src="accueil_admin.js"></script>

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
                    <span>Admin - <?php echo htmlspecialchars($nom_utilisateur); ?></span>
                    <div class="user-avatar"><?php echo strtoupper(substr($nom_utilisateur, 0, 1)); ?></div>
                    <a href="logout.php" class="btn btn-sm btn-outline-light ms-2">Déconnexion</a>

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