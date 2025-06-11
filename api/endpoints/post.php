<?php
// Configuration pour afficher toutes les erreurs
ini_set('display_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// Headers CORS et JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier la méthode
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit();
}

// Fonction de debug
function debug_log($message) {
    error_log("[POST.PHP DEBUG] " . $message);
}

debug_log("Script démarré");

try {
    // Inclure la base de données
    require_once __DIR__ . '/../config/database.php';
    debug_log("Database.php inclus");
    
    // Lire les données JSON
    $input_raw = file_get_contents('php://input');
    debug_log("Input raw: " . $input_raw);
    
    $input = json_decode($input_raw, true);
    debug_log("Input décodé: " . print_r($input, true));
    
    if (!$input) {
        throw new Exception('Données JSON invalides ou vides');
    }
    
    $action = $input['action'] ?? '';
    debug_log("Action: " . $action);
    
    if ($action !== 'add_installation') {
        throw new Exception('Action non reconnue: ' . $action);
    }
    
    // Validation des données obligatoires
    $requiredFields = [
        'mois_installation', 'an_installation', 'surface', 'puissance_crete',
        'orientation', 'pente', 'localite', 'modele_panneau', 'marque_panneau',
        'nb_panneaux', 'modele_onduleur', 'marque_onduleur', 'nb_onduleur'
    ];
    
    foreach ($requiredFields as $field) {
        if (!isset($input[$field]) || (empty($input[$field]) && $input[$field] !== 0)) {
            throw new Exception("Le champ '$field' est requis");
        }
    }
    debug_log("Validation OK");
    
    // Connexion à la base
    $database = new Database();
    $pdo = $database->getConnection();
    debug_log("Connexion DB OK");
    
    // Démarrer une transaction
    $pdo->beginTransaction();
    
    // Fonction pour trouver ou créer une commune
    // function findOrCreateCommune($pdo, $localisation, $codePostal = null, $departement = null, $region = null) {
    //     debug_log("Recherche commune: " . $localisation);
        
    //     // 1. Recherche exacte
    //     $stmt = $pdo->prepare("SELECT code_insee, nom_standard FROM Communes WHERE LOWER(nom_standard) = LOWER(?) LIMIT 1");
    //     $stmt->execute([$localisation]);
    //     $commune = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if ($commune) {
    //         debug_log("Commune trouvée: " . $commune['nom_standard']);
            
    //         // Mettre à jour les informations si elles sont fournies
    //         if ($codePostal || $departement || $region) {
    //             $updateFields = [];
    //             $updateValues = [];
                
    //             if ($codePostal) {
    //                 $updateFields[] = "code_postal = ?";
    //                 $updateValues[] = $codePostal;
    //             }
                
    //             if (!empty($updateFields)) {
    //                 $updateValues[] = $commune['code_insee'];
    //                 $updateStmt = $pdo->prepare("UPDATE Communes SET " . implode(", ", $updateFields) . " WHERE code_insee = ?");
    //                 $updateStmt->execute($updateValues);
    //                 debug_log("Commune mise à jour avec code postal: " . $codePostal);
    //             }
    //         }
            
    //         return $commune['code_insee'];
    //     }
        
    //     // 2. Recherche approximative
    //     $stmt = $pdo->prepare("SELECT code_insee, nom_standard FROM Communes WHERE nom_standard LIKE ? LIMIT 1");
    //     $stmt->execute(['%' . $localisation . '%']);
    //     $commune = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if ($commune) {
    //         debug_log("Commune trouvée (approximative): " . $commune['nom_standard']);
    //         return $commune['code_insee'];
    //     }
        
    //     // 3. Créer une nouvelle commune
    //     debug_log("Création nouvelle commune avec code postal: " . ($codePostal ?? 'NULL'));
        
    //     try {
    //         // Générer nouveau code INSEE
    //         $stmt = $pdo->query("SELECT MAX(code_insee) as max_code FROM Communes");
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $newCodeInsee = ($result['max_code'] ?? 99999) + 1;
            
    //         // Récupérer premier département
    //         $stmt = $pdo->query("SELECT code FROM Departement LIMIT 1");
    //         $dept = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $codeDept = $dept['code'] ?? '01';
            
    //         // Insérer nouvelle commune avec code postal
    //         $insertStmt = $pdo->prepare("
    //             INSERT INTO Communes (code_insee, nom_standard, code_postal, population, code) 
    //             VALUES (?, ?, ?, ?, ?)
    //         ");
            
    //         $success = $insertStmt->execute([
    //             $newCodeInsee,
    //             $localisation,
    //             $codePostal ?? '', // Utiliser le code postal fourni ou chaîne vide
    //             0, // population par défaut
    //             $codeDept
    //         ]);
            
    //         if ($success) {
    //             debug_log("Nouvelle commune créée avec code: " . $newCodeInsee . " et code postal: " . ($codePostal ?? 'vide'));
    //             return $newCodeInsee;
    //         } else {
    //             throw new Exception("Échec création commune");
    //         }
            
    //     } catch (Exception $e) {
    //         debug_log("Erreur création commune: " . $e->getMessage());
            
    //         // Utiliser code par défaut
    //         $stmt = $pdo->query("SELECT code_insee FROM Communes LIMIT 1");
    //         $commune = $stmt->fetch(PDO::FETCH_ASSOC);
    //         return $commune['code_insee'] ?? 1;
    //     }
    // }


    function findOrCreateCommune($pdo, $localisation, $codePostal = null, $departement = null, $region = null) {
        debug_log("Recherche commune: " . $localisation);
        
        // 1. Recherche exacte
        $stmt = $pdo->prepare("SELECT code_insee, nom_standard, code FROM Communes WHERE LOWER(nom_standard) = LOWER(?) LIMIT 1");
        $stmt->execute([$localisation]);
        $commune = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($commune) {
            debug_log("Commune trouvée: " . $commune['nom_standard']);
            
            // Mettre à jour le code postal si fourni et différent
            if ($codePostal) {
                $stmt = $pdo->prepare("UPDATE Communes SET code_postal = ? WHERE code_insee = ?");
                $stmt->execute([$codePostal, $commune['code_insee']]);
                debug_log("Code postal mis à jour: " . $codePostal);
            }
            
            // Gérer le département
            if ($departement) {
                $codeDept = findOrCreateDepartement($pdo, $departement, $region);
                if ($codeDept && $codeDept !== $commune['code']) {
                    $stmt = $pdo->prepare("UPDATE Communes SET code = ? WHERE code_insee = ?");
                    $stmt->execute([$codeDept, $commune['code_insee']]);
                    debug_log("Département mis à jour: " . $codeDept);
                }
            }
            
            return $commune['code_insee'];
        }
        
        // 2. Recherche approximative
        $stmt = $pdo->prepare("SELECT code_insee, nom_standard FROM Communes WHERE nom_standard LIKE ? LIMIT 1");
        $stmt->execute(['%' . $localisation . '%']);
        $commune = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($commune) {
            debug_log("Commune trouvée (approximative): " . $commune['nom_standard']);
            return $commune['code_insee'];
        }
        
        // 3. Créer une nouvelle commune
        debug_log("Création nouvelle commune");
        
        try {
            // Générer nouveau code INSEE
            $stmt = $pdo->query("SELECT MAX(code_insee) as max_code FROM Communes");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCodeInsee = ($result['max_code'] ?? 99999) + 1;
            
            // Gérer le département
            $codeDept = '01'; // Code par défaut
            if ($departement) {
                $codeDeptCreated = findOrCreateDepartement($pdo, $departement, $region);
                if ($codeDeptCreated) {
                    $codeDept = $codeDeptCreated;
                }
            } else {
                // Récupérer premier département disponible
                $stmt = $pdo->query("SELECT code FROM Departement LIMIT 1");
                $dept = $stmt->fetch(PDO::FETCH_ASSOC);
                $codeDept = $dept['code'] ?? '01';
            }
            
            // Insérer nouvelle commune
            $insertStmt = $pdo->prepare("
                INSERT INTO Communes (code_insee, nom_standard, code_postal, population, code) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            $success = $insertStmt->execute([
                $newCodeInsee,
                $localisation,
                $codePostal ?? '',
                0, // population par défaut
                $codeDept
            ]);
            
            if ($success) {
                debug_log("Nouvelle commune créée avec code: " . $newCodeInsee);
                return $newCodeInsee;
            } else {
                throw new Exception("Échec création commune");
            }
            
        } catch (Exception $e) {
            debug_log("Erreur création commune: " . $e->getMessage());
            
            // Utiliser code par défaut
            $stmt = $pdo->query("SELECT code_insee FROM Communes LIMIT 1");
            $commune = $stmt->fetch(PDO::FETCH_ASSOC);
            return $commune['code_insee'] ?? 1;
        }
    }
    
    // Fonction pour gérer les départements
    // function findOrCreateDepartement($pdo, $nomDepartement) {
    //     if (empty($nomDepartement)) return null;
        
    //     debug_log("Recherche département: " . $nomDepartement);
        
    //     // Recherche exacte
    //     $stmt = $pdo->prepare("SELECT code FROM Departement WHERE LOWER(nom) = LOWER(?) LIMIT 1");
    //     $stmt->execute([$nomDepartement]);
    //     $dept = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if ($dept) {
    //         debug_log("Département trouvé: " . $dept['code']);
    //         return $dept['code'];
    //     }
        
    //     // Créer nouveau département si non trouvé
    //     try {
    //         // Générer nouveau code département
    //         $stmt = $pdo->query("SELECT MAX(CAST(code AS UNSIGNED)) as max_code FROM Departement WHERE code REGEXP '^[0-9]+$'");
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $newCode = str_pad(($result['max_code'] ?? 95) + 1, 2, '0', STR_PAD_LEFT);
            
    //         $insertStmt = $pdo->prepare("INSERT INTO Departement (code, nom) VALUES (?, ?)");
    //         $success = $insertStmt->execute([$newCode, $nomDepartement]);
            
    //         if ($success) {
    //             debug_log("Nouveau département créé: " . $newCode);
    //             return $newCode;
    //         }
    //     } catch (Exception $e) {
    //         debug_log("Erreur création département: " . $e->getMessage());
    //     }
        
    //     return null;
    // }

    function findOrCreateDepartement($pdo, $nomDepartement, $nomRegion = null) {
        if (empty($nomDepartement)) return null;
        
        debug_log("Recherche département: " . $nomDepartement);
        
        // Recherche exacte
        $stmt = $pdo->prepare("SELECT code, code_Region FROM Departement WHERE LOWER(nom) = LOWER(?) LIMIT 1");
        $stmt->execute([$nomDepartement]);
        $dept = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($dept) {
            debug_log("Département trouvé: " . $dept['code']);
            
            // Mettre à jour la région si fournie
            if ($nomRegion) {
                $codeRegion = findOrCreateRegion($pdo, $nomRegion);
                if ($codeRegion && $codeRegion != $dept['code_Region']) {
                    $stmt = $pdo->prepare("UPDATE Departement SET code_Region = ? WHERE code = ?");
                    $stmt->execute([$codeRegion, $dept['code']]);
                    debug_log("Région du département mise à jour: " . $codeRegion);
                }
            }
            
            return $dept['code'];
        }
        
        // Créer nouveau département
        try {
            // Générer nouveau code département
            $stmt = $pdo->query("SELECT MAX(CAST(code AS UNSIGNED)) as max_code FROM Departement WHERE code REGEXP '^[0-9]+$'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCode = str_pad(($result['max_code'] ?? 95) + 1, 2, '0', STR_PAD_LEFT);
            
            // Gérer la région
            $codeRegion = 1; // Code région par défaut
            if ($nomRegion) {
                $regionCreated = findOrCreateRegion($pdo, $nomRegion);
                if ($regionCreated) {
                    $codeRegion = $regionCreated;
                }
            } else {
                // Récupérer première région disponible
                $stmt = $pdo->query("SELECT code FROM Region LIMIT 1");
                $region = $stmt->fetch(PDO::FETCH_ASSOC);
                $codeRegion = $region['code'] ?? 1;
            }
            
            $insertStmt = $pdo->prepare("INSERT INTO Departement (code, nom, code_Region) VALUES (?, ?, ?)");
            $success = $insertStmt->execute([$newCode, $nomDepartement, $codeRegion]);
            
            if ($success) {
                debug_log("Nouveau département créé: " . $newCode . " avec région: " . $codeRegion);
                return $newCode;
            }
        } catch (Exception $e) {
            debug_log("Erreur création département: " . $e->getMessage());
        }
        
        return null;
    }
    
    // Fonction pour gérer les régions
    // function findOrCreateRegion($pdo, $nomRegion) {
    //     if (empty($nomRegion)) return null;
        
    //     debug_log("Recherche région: " . $nomRegion);
        
    //     // Recherche exacte
    //     $stmt = $pdo->prepare("SELECT code FROM Region WHERE LOWER(nom) = LOWER(?) LIMIT 1");
    //     $stmt->execute([$nomRegion]);
    //     $region = $stmt->fetch(PDO::FETCH_ASSOC);
        
    //     if ($region) {
    //         debug_log("Région trouvée: " . $region['code']);
    //         return $region['code'];
    //     }
        
    //     // Créer nouvelle région si non trouvée
    //     try {
    //         // Générer nouveau code région
    //         $stmt = $pdo->query("SELECT MAX(CAST(code AS UNSIGNED)) as max_code FROM Region WHERE code REGEXP '^[0-9]+$'");
    //         $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //         $newCode = str_pad(($result['max_code'] ?? 90) + 1, 2, '0', STR_PAD_LEFT);
            
    //         $insertStmt = $pdo->prepare("INSERT INTO Region (code, nom) VALUES (?, ?)");
    //         $success = $insertStmt->execute([$newCode, $nomRegion]);
            
    //         if ($success) {
    //             debug_log("Nouvelle région créée: " . $newCode);
    //             return $newCode;
    //         }
    //     } catch (Exception $e) {
    //         debug_log("Erreur création région: " . $e->getMessage());
    //     }
        
    //     return null;
    // }
    function findOrCreateRegion($pdo, $nomRegion) {
        if (empty($nomRegion)) return null;
        
        debug_log("Recherche région: " . $nomRegion);
        
        // Recherche exacte
        $stmt = $pdo->prepare("SELECT code FROM Region WHERE LOWER(nom) = LOWER(?) LIMIT 1");
        $stmt->execute([$nomRegion]);
        $region = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($region) {
            debug_log("Région trouvée: " . $region['code']);
            return $region['code'];
        }
        
        // Créer nouvelle région
        try {
            // Générer nouveau code région
            $stmt = $pdo->query("SELECT MAX(code) as max_code FROM Region");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $newCode = ($result['max_code'] ?? 90) + 1;
            
            $insertStmt = $pdo->prepare("INSERT INTO Region (code, nom, pays) VALUES (?, ?, ?)");
            $success = $insertStmt->execute([$newCode, $nomRegion, 'France']);
            
            if ($success) {
                debug_log("Nouvelle région créée: " . $newCode);
                return $newCode;
            }
        } catch (Exception $e) {
            debug_log("Erreur création région: " . $e->getMessage());
        }
        
        return null;
    }
    
    // Fonction pour trouver ou créer un modèle de panneau
    function findOrCreateModelePanneau($pdo, $modele) {
        debug_log("Recherche modèle panneau: " . $modele);
        
        $stmt = $pdo->prepare("SELECT nom_modele FROM Modele_panneau WHERE nom_modele = ?");
        $stmt->execute([$modele]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            debug_log("Création nouveau modèle panneau: " . $modele);
            $stmt = $pdo->prepare("INSERT INTO Modele_panneau (nom_modele) VALUES (?)");
            $stmt->execute([$modele]);
        }
        
        return $modele;
    }
    
    // Fonction pour trouver ou créer une marque de panneau
    function findOrCreateMarquePanneau($pdo, $marque) {
        debug_log("Recherche marque panneau: " . $marque);
        
        $stmt = $pdo->prepare("SELECT nom FROM Marque_panneau WHERE nom = ?");
        $stmt->execute([$marque]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            debug_log("Création nouvelle marque panneau: " . $marque);
            $stmt = $pdo->prepare("INSERT INTO Marque_panneau (nom) VALUES (?)");
            $stmt->execute([$marque]);
        }
        
        return $marque;
    }
    
    // Fonction pour trouver ou créer un modèle d'onduleur
    function findOrCreateModeleOnduleur($pdo, $modele) {
        debug_log("Recherche modèle onduleur: " . $modele);
        
        $stmt = $pdo->prepare("SELECT nom FROM Modele_onduleur WHERE nom = ?");
        $stmt->execute([$modele]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            debug_log("Création nouveau modèle onduleur: " . $modele);
            $stmt = $pdo->prepare("INSERT INTO Modele_onduleur (nom) VALUES (?)");
            $stmt->execute([$modele]);
        }
        
        return $modele;
    }
    
    // Fonction pour trouver ou créer une marque d'onduleur
    function findOrCreateMarqueOnduleur($pdo, $marque) {
        debug_log("Recherche marque onduleur: " . $marque);
        
        $stmt = $pdo->prepare("SELECT nom FROM Marque_onduleur WHERE nom = ?");
        $stmt->execute([$marque]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            debug_log("Création nouvelle marque onduleur: " . $marque);
            $stmt = $pdo->prepare("INSERT INTO Marque_onduleur (nom) VALUES (?)");
            $stmt->execute([$marque]);
        }
        
        return $marque;
    }
    
    // Fonction pour trouver ou créer un installateur
    function findOrCreateInstallateur($pdo, $nom) {
        if (empty($nom)) return null;
        
        debug_log("Recherche installateur: " . $nom);
        
        $stmt = $pdo->prepare("SELECT id FROM Installateur WHERE nom = ?");
        $stmt->execute([$nom]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            debug_log("Création nouvel installateur: " . $nom);
            $stmt = $pdo->prepare("INSERT INTO Installateur (nom) VALUES (?)");
            $stmt->execute([$nom]);
            return $pdo->lastInsertId();
        }
        
        return $result['id'];
    }
    
    // Traitement des données
    debug_log("Traitement des données");
    debug_log("Code postal reçu: " . ($input['code_postal'] ?? 'NULL'));
    debug_log("Département reçu: " . ($input['departement'] ?? 'NULL'));
    debug_log("Région reçue: " . ($input['region'] ?? 'NULL'));
    
    // Trouver ou créer les éléments nécessaires
    // $codeInsee = findOrCreateCommune($pdo, $input['localite'], $input['code_postal'] ?? null, $input['departement'] ?? null, $input['region'] ?? null);
    $codeInsee = findOrCreateCommune($pdo, $input['localite'], $input['code_postal'] ?? null, $input['departement'] ?? null, $input['region'] ?? null);

    $codeDepartement = findOrCreateDepartement($pdo, $input['departement'] ?? null);
    $codeRegion = findOrCreateRegion($pdo, $input['region'] ?? null);
    
    $modeleNomPanneau = findOrCreateModelePanneau($pdo, $input['modele_panneau']);
    $marqueNomPanneau = findOrCreateMarquePanneau($pdo, $input['marque_panneau']);
    $modeleNomOnduleur = findOrCreateModeleOnduleur($pdo, $input['modele_onduleur']);
    $marqueNomOnduleur = findOrCreateMarqueOnduleur($pdo, $input['marque_onduleur']);
    $installateurId = findOrCreateInstallateur($pdo, $input['installateur'] ?? null);
    $codeDepartement = findOrCreateDepartement($pdo, $input['departement'] ?? null);
    $codeRegion = findOrCreateRegion($pdo, $input['region'] ?? null);

    
    // Créer ou récupérer le panneau
    $stmt = $pdo->prepare("SELECT id_panneau FROM Panneau WHERE nom_modele = ? AND nom = ?");
    $stmt->execute([$modeleNomPanneau, $marqueNomPanneau]);
    $panneau = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$panneau) {
        debug_log("Création nouveau panneau");
        $stmt = $pdo->prepare("INSERT INTO Panneau (nom_modele, nom) VALUES (?, ?)");
        $stmt->execute([$modeleNomPanneau, $marqueNomPanneau]);
        $idPanneau = $pdo->lastInsertId();
    } else {
        $idPanneau = $panneau['id_panneau'];
    }
    
    // Créer ou récupérer l'onduleur
    $stmt = $pdo->prepare("SELECT id_onduleur FROM Onduleur WHERE nom = ? AND nom_Marque_onduleur = ?");
    $stmt->execute([$modeleNomOnduleur, $marqueNomOnduleur]);
    $onduleur = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$onduleur) {
        debug_log("Création nouvel onduleur");
        $stmt = $pdo->prepare("INSERT INTO Onduleur (nom, nom_Marque_onduleur) VALUES (?, ?)");
        $stmt->execute([$modeleNomOnduleur, $marqueNomOnduleur]);
        $idOnduleur = $pdo->lastInsertId();
    } else {
        $idOnduleur = $onduleur['id_onduleur'];
    }


    if ($codeDepartement || $codeRegion) {
        $updateFields = [];
        $updateValues = [];
        
        if ($codeDepartement) {
            $updateFields[] = "code = ?";
            $updateValues[] = $codeDepartement;
        }
        
        if (!empty($updateFields)) {
            $updateValues[] = $codeInsee;
            $updateStmt = $pdo->prepare("UPDATE Communes SET " . implode(", ", $updateFields) . " WHERE code_insee = ?");
            $updateStmt->execute($updateValues);
            debug_log("Commune mise à jour avec département: " . $codeDepartement);
        }
    }
        
    // Générer un iddoc unique
    $stmt = $pdo->query("SELECT MAX(iddoc) as max_iddoc FROM Installation");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $newIddoc = ($result['max_iddoc'] ?? 0) + 1;
    
    // Vérifier la structure de la table Installation pour s'assurer que tous les champs existent
    debug_log("Insertion de l'installation avec:");
    debug_log("- Code postal: " . ($input['code_postal'] ?? 'NULL'));
    debug_log("- Département: " . ($input['departement'] ?? 'NULL'));
    debug_log("- Région: " . ($input['region'] ?? 'NULL'));
    
    // Insérer l'installation
    $insertInstallationStmt = $pdo->prepare("
        INSERT INTO Installation (
            iddoc, an_installation, mois_installation, nb_panneaux, lat, `long`,
            production_pvgis, puissance_crete, pente, pente_optimum, surface,
            orientation, orientation_optimum, administrative_area_level3,
            administrative_area_level4, political, id_Installateur, id_onduleur,
            code_insee, id_panneau
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $success = $insertInstallationStmt->execute([
        $newIddoc,
        (int)$input['an_installation'],
        (int)$input['mois_installation'],
        (int)$input['nb_panneaux'],
        isset($input['latitude']) && $input['latitude'] !== '' ? (float)$input['latitude'] : null,
        isset($input['longitude']) && $input['longitude'] !== '' ? (float)$input['longitude'] : null,
        isset($input['production_pvgis']) && $input['production_pvgis'] !== '' ? (int)$input['production_pvgis'] : null,
        (float)$input['puissance_crete'],
        (int)$input['pente'],
        isset($input['pente_optimum']) && $input['pente_optimum'] !== '' ? (int)$input['pente_optimum'] : null,
        (float)$input['surface'],
        (int)$input['orientation'],
        isset($input['orientation_optimum']) && $input['orientation_optimum'] !== '' ? (int)$input['orientation_optimum'] : null,
        $codeDepartement, 
        $codeRegion,
        $input['pays'] ?? 'France',     // political
        $installateurId,
        $idOnduleur,
        $codeInsee,
        $idPanneau
    ]);
    
    if (!$success) {
        throw new Exception("Échec de l'insertion de l'installation");
    }
    
    $installationId = $pdo->lastInsertId();
    debug_log("Installation créée avec ID: " . $installationId);
    
    // Valider la transaction
    $pdo->commit();
    
    // Retourner le succès
    echo json_encode([
        'success' => true,
        'message' => 'Installation ajoutée avec succès',
        'data' => [
            'id' => $installationId,
            'iddoc' => $newIddoc,
            'code_postal' => $input['code_postal'] ?? null,
            'departement' => $input['departement'] ?? null,
            'region' => $input['region'] ?? null
        ]
    ]);
        
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    if (isset($pdo)) {
        $pdo->rollback();
    }
    
    debug_log("Erreur: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de l\'ajout de l\'installation: ' . $e->getMessage()
    ]);
}
?>