<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

// Permettre les requêtes CORS si nécessaire
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que c'est bien une requête PUT
if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

require_once __DIR__ . '/../config/database.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    // Lire les données JSON
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Données JSON invalides');
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'update_installation':
            // Validation des données obligatoires
            $requiredFields = ['id', 'date_installation', 'nb_panneaux', 'surface', 'puissance_crete', 'localisation'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || (empty($input[$field]) && $input[$field] !== 0)) {
                    throw new Exception("Le champ '$field' est requis");
                }
            }
            
            // Extraire l'année et le mois de la date
            $dateInstallation = $input['date_installation'];
            $anneeInstallation = date('Y', strtotime($dateInstallation));
            $moisInstallation = date('n', strtotime($dateInstallation));
            
            // Fonction pour trouver ou créer une commune
            function findOrCreateCommune($pdo, $localisation) {
                $localisation = trim($localisation);
                error_log("Recherche de la commune: " . $localisation);
                
                // 1. Recherche exacte (insensible à la casse)
                $stmt = $pdo->prepare("SELECT code_insee, nom_standard FROM Communes WHERE LOWER(nom_standard) = LOWER(?) LIMIT 1");
                $stmt->execute([$localisation]);
                $commune = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($commune) {
                    error_log("Commune trouvée (exacte): " . $commune['nom_standard'] . " - Code: " . $commune['code_insee']);
                    return $commune['code_insee'];
                }
                
                // 2. Recherche avec normalisation
                $localisationNormalized = str_replace(["'", "-", " "], ["", "", ""], strtolower($localisation));
                $stmt = $pdo->prepare("
                    SELECT code_insee, nom_standard 
                    FROM Communes 
                    WHERE REPLACE(REPLACE(REPLACE(LOWER(nom_standard), \"'\", \"\"), \"-\", \"\"), \" \", \"\") = ? 
                    LIMIT 1
                ");
                $stmt->execute([$localisationNormalized]);
                $commune = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($commune) {
                    error_log("Commune trouvée (normalisée): " . $commune['nom_standard'] . " - Code: " . $commune['code_insee']);
                    return $commune['code_insee'];
                }
                
                // 3. Recherche approximative avec LIKE
                $stmt = $pdo->prepare("SELECT code_insee, nom_standard FROM Communes WHERE nom_standard LIKE ? LIMIT 1");
                $stmt->execute(['%' . $localisation . '%']);
                $commune = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($commune) {
                    error_log("Commune trouvée (approximative): " . $commune['nom_standard'] . " - Code: " . $commune['code_insee']);
                    return $commune['code_insee'];
                }
                
                // 4. Si aucune commune trouvée, créer une nouvelle entrée
                error_log("Aucune commune trouvée pour: " . $localisation . " - Création d'une nouvelle entrée");
                
                try {
                    // Générer un nouveau code_insee unique
                    $stmt = $pdo->query("SELECT MAX(code_insee) as max_code FROM Communes");
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $newCodeInsee = ($result['max_code'] ?? 0) + 1;
                    
                    // Récupérer un département par défaut
                    $stmt = $pdo->query("SELECT code FROM Departement LIMIT 1");
                    $defaultDept = $stmt->fetch(PDO::FETCH_ASSOC);
                    $codeDepartement = $defaultDept['code'] ?? '01';
                    
                    // Insérer la nouvelle commune
                    $insertStmt = $pdo->prepare("
                        INSERT INTO Communes (
                            code_insee, 
                            nom_standard, 
                            code_postal, 
                            code_postal_suffix, 
                            population, 
                            code
                        ) VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    
                    $result = $insertStmt->execute([
                        $newCodeInsee,
                        $localisation,
                        0, // Code postal par défaut
                        null,
                        0,
                        $codeDepartement
                    ]);
                    
                    if ($result) {
                        error_log("Nouvelle commune créée: " . $localisation . " - Code INSEE: " . $newCodeInsee);
                        return $newCodeInsee;
                    } else {
                        throw new Exception("Erreur lors de la création de la nouvelle commune");
                    }
                    
                } catch (Exception $e) {
                    error_log("Erreur lors de la création de la commune: " . $e->getMessage());
                    
                    // En cas d'erreur, utiliser un code par défaut existant
                    $stmt = $pdo->query("SELECT code_insee FROM Communes LIMIT 1");
                    $commune = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $commune['code_insee'] ?? 1;
                }
            }
            
            // Fonction pour trouver ou créer un panneau
            function findOrCreatePanneau($pdo, $marque_panneau, $modele_panneau) {
                if (empty($marque_panneau) || empty($modele_panneau)) {
                    // Retourner un panneau par défaut
                    $stmt = $pdo->query("SELECT id_panneau FROM Panneau LIMIT 1");
                    $panneau = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $panneau['id_panneau'] ?? null;
                }
                
                // Créer ou trouver la marque
                $stmt = $pdo->prepare("INSERT IGNORE INTO Marque_panneau (nom) VALUES (?)");
                $stmt->execute([$marque_panneau]);
                
                // Créer ou trouver le modèle
                $stmt = $pdo->prepare("INSERT IGNORE INTO Modele_panneau (nom_modele) VALUES (?)");
                $stmt->execute([$modele_panneau]);
                
                // Chercher le panneau existant
                $stmt = $pdo->prepare("
                    SELECT id_panneau 
                    FROM Panneau 
                    WHERE nom = ? AND nom_modele = ? 
                    LIMIT 1
                ");
                $stmt->execute([$marque_panneau, $modele_panneau]);
                $panneau = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($panneau) {
                    return $panneau['id_panneau'];
                }
                
                // Créer un nouveau panneau
                $stmt = $pdo->prepare("
                    INSERT INTO Panneau (nom_modele, nom) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$modele_panneau, $marque_panneau]);
                
                return $pdo->lastInsertId();
            }
            
            // Fonction pour trouver ou créer un onduleur
            function findOrCreateOnduleur($pdo, $marque_onduleur, $modele_onduleur) {
                if (empty($marque_onduleur) || empty($modele_onduleur)) {
                    // Retourner un onduleur par défaut
                    $stmt = $pdo->query("SELECT id_onduleur FROM Onduleur LIMIT 1");
                    $onduleur = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $onduleur['id_onduleur'] ?? null;
                }
                
                // Créer ou trouver la marque
                $stmt = $pdo->prepare("INSERT IGNORE INTO Marque_onduleur (nom) VALUES (?)");
                $stmt->execute([$marque_onduleur]);
                
                // Créer ou trouver le modèle
                $stmt = $pdo->prepare("INSERT IGNORE INTO Modele_onduleur (nom) VALUES (?)");
                $stmt->execute([$modele_onduleur]);
                
                // Chercher l'onduleur existant
                $stmt = $pdo->prepare("
                    SELECT id_onduleur 
                    FROM Onduleur 
                    WHERE nom_Marque_onduleur = ? AND nom = ? 
                    LIMIT 1
                ");
                $stmt->execute([$marque_onduleur, $modele_onduleur]);
                $onduleur = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($onduleur) {
                    return $onduleur['id_onduleur'];
                }
                
                // Créer un nouvel onduleur
                $stmt = $pdo->prepare("
                    INSERT INTO Onduleur (nom, nom_Marque_onduleur) 
                    VALUES (?, ?)
                ");
                $stmt->execute([$modele_onduleur, $marque_onduleur]);
                
                return $pdo->lastInsertId();
            }
            
            // Vérifier que l'installation existe
            $stmt = $pdo->prepare("SELECT id, id_panneau, id_onduleur FROM Installation WHERE id = ?");
            $stmt->execute([$input['id']]);
            $installation = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$installation) {
                throw new Exception('Installation non trouvée');
            }
            
            // Récupérer les IDs nécessaires
            $codeInsee = findOrCreateCommune($pdo, $input['localisation']);
            
            $idPanneau = $installation['id_panneau']; // Valeur actuelle par défaut
            if (!empty($input['marque_panneau']) && !empty($input['modele_panneau'])) {
                $idPanneau = findOrCreatePanneau($pdo, $input['marque_panneau'], $input['modele_panneau']);
            }
            
            $idOnduleur = $installation['id_onduleur']; // Valeur actuelle par défaut
            if (!empty($input['marque_onduleur']) && !empty($input['modele_onduleur'])) {
                $idOnduleur = findOrCreateOnduleur($pdo, $input['marque_onduleur'], $input['modele_onduleur']);
            }
            
            // Construire la requête de mise à jour dynamiquement
            $updateFields = [];
            $updateValues = [];
            
            // Champs obligatoires
            $updateFields[] = "an_installation = ?";
            $updateValues[] = $anneeInstallation;
            
            $updateFields[] = "mois_installation = ?";
            $updateValues[] = $moisInstallation;
            
            $updateFields[] = "nb_panneaux = ?";
            $updateValues[] = $input['nb_panneaux'];
            
            $updateFields[] = "puissance_crete = ?";
            $updateValues[] = $input['puissance_crete'];
            
            $updateFields[] = "surface = ?";
            $updateValues[] = $input['surface'];
            
            $updateFields[] = "code_insee = ?";
            $updateValues[] = $codeInsee;
            
            $updateFields[] = "id_panneau = ?";
            $updateValues[] = $idPanneau;
            
            $updateFields[] = "id_onduleur = ?";
            $updateValues[] = $idOnduleur;
            
            // Champs optionnels
            if (isset($input['orientation']) && $input['orientation'] !== '') {
                $updateFields[] = "orientation = ?";
                $updateValues[] = $input['orientation'];
            }
            
            if (isset($input['pente']) && $input['pente'] !== '') {
                $updateFields[] = "pente = ?";
                $updateValues[] = $input['pente'];
            }
            
            if (isset($input['latitude']) && $input['latitude'] !== '') {
                $updateFields[] = "lat = ?";
                $updateValues[] = $input['latitude'];
            }
            
            if (isset($input['longitude']) && $input['longitude'] !== '') {
                $updateFields[] = "`long` = ?";
                $updateValues[] = $input['longitude'];
            }
            
            // Ajouter l'ID pour la clause WHERE
            $updateValues[] = $input['id'];
            
            // Construire et exécuter la requête
            $sql = "UPDATE Installation SET " . implode(', ', $updateFields) . " WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute($updateValues);
            
            if ($result) {
                // Récupérer le nom de la commune utilisée pour confirmation
                $stmt = $pdo->prepare("SELECT nom_standard FROM Communes WHERE code_insee = ?");
                $stmt->execute([$codeInsee]);
                $communeUtilisee = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Installation modifiée avec succès' . 
                               ($communeUtilisee ? ' (Commune: ' . $communeUtilisee['nom_standard'] . ')' : ''),
                    'commune_utilisee' => $communeUtilisee['nom_standard'] ?? 'Inconnue',
                    'updated_fields' => count($updateFields)
                ]);
            } else {
                throw new Exception('Erreur lors de la mise à jour en base de données');
            }
            break;
            
        default:
            throw new Exception('Action non reconnue: ' . $action);
    }
    
} catch (Exception $e) {
    error_log("Erreur dans put.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    error_log("Erreur PDO dans put.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>