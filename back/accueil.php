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

        <script src="../js/accueil_admin.js"></script>

    </head>
    <body>
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
                                <th>Puissance crête (W)</th>
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
                    <h3>Aucune installation enregistrée</h3>
                    <p>Commencez par ajouter votre première installation de panneaux solaires</p>
                </div>
            </div>
        </div>


        <div id="installationModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="modalTitle">Nouvelle installation</h2>
                    <button class="close" id="btnCloseModal">&times;</button>
                </div>
                
                <form id="installationForm">
                    <div class="modal-body">
                        <input type="hidden" id="installationId" value="">
                        
                        <!-- Section Installation -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-icon">🔧</span>
                                Informations générales de l'installation
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="installMonth">Mois d'installation <span class="required">*</span></label>
                                    <select id="installMonth" required>
                                        <option value="">Sélectionner un mois</option>
                                        <option value="1">Janvier</option>
                                        <option value="2">Février</option>
                                        <option value="3">Mars</option>
                                        <option value="4">Avril</option>
                                        <option value="5">Mai</option>
                                        <option value="6">Juin</option>
                                        <option value="7">Juillet</option>
                                        <option value="8">Août</option>
                                        <option value="9">Septembre</option>
                                        <option value="10">Octobre</option>
                                        <option value="11">Novembre</option>
                                        <option value="12">Décembre</option>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="installYear">Année d'installation <span class="required">*</span></label>
                                    <input type="number" id="installYear" min="2000" max="2030" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="surface">Surface (m²) <span class="required">*</span></label>
                                    <input type="number" id="surface" min="0" step="0.1" required>
                                    <div class="help-text">Surface totale occupée par les panneaux</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="puissanceCrete">Puissance crête (W) <span class="required">*</span></label>
                                    <input type="number" id="puissanceCrete" min="0" step="0.1" required>
                                    <div class="help-text">Puissance maximale de l'installation</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Placement -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-icon">📐</span>
                                Placement et orientation
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="orientation">Orientation (°) <span class="required">*</span></label>
                                    <input type="number" id="orientation" min="0" max="360" required>
                                    <div class="help-text">Nord = 0°, Est = 90°, Sud = 180° , Ouest = 270°</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="orientationOptimum">Orientation optimum (°)</label>
                                    <input type="number" id="orientationOptimum" min="0" max="360">
                                    <div class="help-text">Orientation idéale calculée</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="pente">Pente (°) <span class="required">*</span></label>
                                    <input type="number" id="pente" min="0" max="90" required>
                                    <div class="help-text">Inclinaison des panneaux</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="penteOptimum">Pente optimum (°)</label>
                                    <input type="number" id="penteOptimum" min="0" max="90">
                                    <div class="help-text">Inclinaison idéale calculée</div>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="installateur">Installateur</label>
                                    <input type="text" id="installateur" placeholder="Nom de l'entreprise installatrice">
                                </div>
                                
                                <div class="form-group">
                                    <label for="productionPvgis">Production PVGIS (kWh/an)</label>
                                    <input type="number" id="productionPvgis" min="0">
                                    <div class="help-text">Production estimée par PVGIS</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Adresse -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-icon">📍</span>
                                Adresse et localisation
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="latitude">Latitude</label>
                                    <input type="number" id="latitude" step="0.000001" placeholder="46.123456">
                                    <div class="help-text">Coordonnée GPS (décimal)</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="longitude">Longitude</label>
                                    <input type="number" id="longitude" step="0.000001" placeholder="2.123456">
                                    <div class="help-text">Coordonnée GPS (décimal)</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="localite">Localité <span class="required">*</span></label>
                                    <input type="text" id="localite" required list="communes-list">
                                    <div class="help-text">Nom de la commune</div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="departement">Département</label>
                                    <input type="text" id="departement" required>
                                    <!-- <div class="help-text">Rempli automatiquement</div> -->
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="codePostal">Code postal</label>
                                    <input type="text" id="codePostal" pattern="[0-9]{5}" maxlength="5">
                                </div>
                                
                                <div class="form-group">
                                    <label for="region">Région</label>
                                    <input type="text" id="region" required>
                                    <!-- <div class="help-text">Rempli automatiquement</div> -->
                                </div>
                                
                                <div class="form-group">
                                    <label for="pays">Pays</label>
                                    <input type="text" id="pays" value="France" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Section Panneau -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-icon">☀️</span>
                                Informations panneaux
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="modeleReponse">Modèle de panneau <span class="required">*</span></label>
                                    <input type="text" id="modeleReponse" required placeholder="Ex: Monocristallin 300W">
                                </div>
                                
                                <div class="form-group">
                                    <label for="marquePanneau">Marque de panneau <span class="required">*</span></label>
                                    <input type="text" id="marquePanneau" required placeholder="Ex: SunPower, LG, Panasonic">
                                </div>
                                
                                <div class="form-group">
                                    <label for="nbPanneaux">Nombre de panneaux <span class="required">*</span></label>
                                    <input type="number" id="nbPanneaux" min="1" required>
                                    <div class="help-text">Nombre total de panneaux installés</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section Onduleur -->
                        <div class="form-section">
                            <h3 class="section-title">
                                <span class="section-icon">⚡</span>
                                Informations onduleur
                            </h3>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="modeleOnduleur">Modèle d'onduleur <span class="required">*</span></label>
                                    <input type="text" id="modeleOnduleur" required placeholder="Ex: String 5000W">
                                </div>
                                
                                <div class="form-group">
                                    <label for="marqueOnduleur">Marque d'onduleur <span class="required">*</span></label>
                                    <input type="text" id="marqueOnduleur" required placeholder="Ex: SMA, Fronius, Huawei">
                                </div>
                                
                                <div class="form-group">
                                    <label for="nbOnduleur">Nombre d'onduleurs <span class="required">*</span></label>
                                    <input type="number" id="nbOnduleur" min="1" required>
                                    <div class="help-text">Nombre d'onduleurs installés</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-buttons">
                        <button type="button" class="btn-secondary" id="btnCancel">Annuler</button>
                        <button type="submit" class="add-btn" id="btnSubmit">Ajouter l'installation</button>
                    </div>
                </form>
            </div>
        </div>

        <datalist id="communes-list">
        <!-- Options ajoutées dynamiquement -->
        </datalist>
        
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