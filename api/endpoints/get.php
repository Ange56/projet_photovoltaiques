<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../config/database.php';

$database = new Database();
$pdo = $database->getConnection();

$action = $_GET['action'] ?? 'all';

switch ($action) {

    //---------------------------PAGE CARTE-----------------------------------
    //  Liste de 20 années au hasard
    case 'annees':
        $stmt = $pdo->query("
            SELECT DISTINCT an_installation AS annee
            FROM Installation
            WHERE an_installation IS NOT NULL
            ORDER BY RAND()
            LIMIT 20
        ");
        echo json_encode(['annees' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;

    //  Liste de 20 départements pour une année
        // Départements pour une année donnée
    case 'departements_par_annee':
        $annee = $_GET['annee'] ?? null;
        if (!$annee) {
            http_response_code(400);
            echo json_encode(["error" => "Paramètre 'annee' requis."]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT DISTINCT d.code, d.nom
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            WHERE i.an_installation = :annee
              AND i.lat IS NOT NULL AND i.`long` IS NOT NULL
            ORDER BY RAND()
            LIMIT 20
        ");
        $stmt->execute(['annee' => $annee]);
        echo json_encode(['departements' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
        break;


    //  Liste combinée année + départements (optionnel si utilisé ailleurs)
    case 'filters':
        $filters = [];

        $stmt1 = $pdo->query("
            SELECT DISTINCT an_installation AS annee
            FROM Installation
            ORDER BY RAND()
            LIMIT 20
        ");
        $filters['annees'] = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        $stmt2 = $pdo->query("
            SELECT code, nom
            FROM Departement
            ORDER BY RAND()
            LIMIT 20
        ");
        $filters['departements'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($filters);
        break;

    //  Installations géolocalisées pour la carte
    case 'installations_map':
        $annee = $_GET['annee'] ?? null;
        $departement = $_GET['departement'] ?? null;

        if (!$annee || !$departement) {
            http_response_code(400);
            echo json_encode(["error" => "Paramètres 'annee' et 'departement' requis."]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT i.id, i.lat, i.`long`, i.puissance_crete, c.nom_standard AS localite
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            WHERE i.an_installation = :annee
              AND d.code = :departement
              AND i.lat IS NOT NULL AND i.`long` IS NOT NULL
        ");
        $stmt->execute(['annee' => $annee, 'departement' => $departement]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    //  Détail d'une installation
    case 'installation_detail':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Paramètre 'id' requis."]);
            exit;
        }

        $stmt = $pdo->prepare("
            SELECT 
                i.*, 
                c.nom_standard AS localite,
                d.nom AS departement,
                r.nom AS region,
                inst.nom AS installateur,
                pan.nom AS marque_panneau,
                pan.nom_modele AS modele_panneau,
                ond.nom AS modele_onduleur,
                ond.nom_Marque_onduleur AS marque_onduleur
            FROM Installation i
            LEFT JOIN Communes c ON i.code_insee = c.code_insee
            LEFT JOIN Departement d ON c.code = d.code
            LEFT JOIN Region r ON d.code_Region = r.code
            LEFT JOIN Installateur inst ON i.id_Installateur = inst.id
            LEFT JOIN Panneau pan ON i.id_panneau = pan.id_panneau
            LEFT JOIN Onduleur ond ON i.id_onduleur = ond.id_onduleur
            WHERE i.id = :id
        ");
        $stmt->execute(['id' => $id]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
        break;




    //---------------------------PAGE RECHERCHE-----------------------------------
    // 🔹 FILTRES DYNAMIQUES SIMPLIFIÉS
    
    // Marques d'onduleurs disponibles selon les autres filtres
    case 'marques_onduleurs':
        $marque_panneau = $_GET['marque_panneau'] ?? null;
        $departement = $_GET['departement'] ?? null;
        
        // Requête de base
        $sql = "
            SELECT DISTINCT mo.nom 
            FROM Installation i
            INNER JOIN Onduleur o ON i.id_onduleur = o.id_onduleur
            INNER JOIN Marque_onduleur mo ON o.nom_Marque_onduleur = mo.nom
            INNER JOIN Communes c ON i.code_insee = c.code_insee
            INNER JOIN Departement d ON c.code = d.code
            INNER JOIN Panneau p ON i.id_panneau = p.id_panneau
            INNER JOIN Marque_panneau mp ON p.nom = mp.nom
            WHERE 1=1
        ";
        
        $params = [];
        
        // Ajout des conditions
        if ($marque_panneau) {
            $sql .= " AND mp.nom = :marque_panneau";
            $params['marque_panneau'] = $marque_panneau;
        }
        
        if ($departement) {
            $sql .= " AND d.code = :departement";
            $params['departement'] = $departement;
        }
        
        $sql .= " ORDER BY RAND() LIMIT 20";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'marques_onduleurs' => $result,
                'debug' => [
                    'sql' => $sql,
                    'params' => $params,
                    'count' => count($result)
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage(), 'sql' => $sql]);
        }
        break;

    // Marques de panneaux disponibles selon les autres filtres
    case 'marques_panneaux':
        $marque_onduleur = $_GET['marque_onduleur'] ?? null;
        $departement = $_GET['departement'] ?? null;
        
        $sql = "
            SELECT DISTINCT mp.nom 
            FROM Installation i
            INNER JOIN Panneau p ON i.id_panneau = p.id_panneau
            INNER JOIN Marque_panneau mp ON p.nom = mp.nom
            INNER JOIN Communes c ON i.code_insee = c.code_insee
            INNER JOIN Departement d ON c.code = d.code
            INNER JOIN Onduleur o ON i.id_onduleur = o.id_onduleur
            INNER JOIN Marque_onduleur mo ON o.nom_Marque_onduleur = mo.nom
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($marque_onduleur) {
            $sql .= " AND mo.nom = :marque_onduleur";
            $params['marque_onduleur'] = $marque_onduleur;
        }
        
        if ($departement) {
            $sql .= " AND d.code = :departement";
            $params['departement'] = $departement;
        }
        
        $sql .= " ORDER BY RAND() LIMIT 20";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'marques_panneaux' => $result,
                'debug' => [
                    'sql' => $sql,
                    'params' => $params,
                    'count' => count($result)
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage(), 'sql' => $sql]);
        }
        break;

    // Départements disponibles selon les autres filtres
    case 'departements_random':
        $marque_onduleur = $_GET['marque_onduleur'] ?? null;
        $marque_panneau = $_GET['marque_panneau'] ?? null;
        
        $sql = "
            SELECT DISTINCT d.code, d.nom
            FROM Installation i
            INNER JOIN Communes c ON i.code_insee = c.code_insee
            INNER JOIN Departement d ON c.code = d.code
            INNER JOIN Onduleur o ON i.id_onduleur = o.id_onduleur
            INNER JOIN Marque_onduleur mo ON o.nom_Marque_onduleur = mo.nom
            INNER JOIN Panneau p ON i.id_panneau = p.id_panneau
            INNER JOIN Marque_panneau mp ON p.nom = mp.nom
            WHERE 1=1
        ";
        
        $params = [];
        
        if ($marque_onduleur) {
            $sql .= " AND mo.nom = :marque_onduleur";
            $params['marque_onduleur'] = $marque_onduleur;
        }
        
        if ($marque_panneau) {
            $sql .= " AND mp.nom = :marque_panneau";
            $params['marque_panneau'] = $marque_panneau;
        }
        
        $sql .= " ORDER BY RAND() LIMIT 20";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'departements' => $result,
                'debug' => [
                    'sql' => $sql,
                    'params' => $params,
                    'count' => count($result)
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage(), 'sql' => $sql]);
        }
        break;

    // Recherche des installations (inchangé)
    case 'recherche_installations':
        $marque_onduleur = $_GET['marque_onduleur'] ?? null;
        $marque_panneau = $_GET['marque_panneau'] ?? null;
        $departement = $_GET['departement'] ?? null;

        // Construction de la requête avec les filtres
        $sql = "
            SELECT 
                i.id,
                i.puissance_crete,
                c.nom_standard AS localite,
                d.nom AS departement,
                mo.nom AS marque_onduleur,
                mp.nom AS marque_panneau
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            JOIN Onduleur o ON i.id_onduleur = o.id_onduleur
            JOIN Marque_onduleur mo ON o.nom_Marque_onduleur = mo.nom
            JOIN Panneau p ON i.id_panneau = p.id_panneau
            JOIN Marque_panneau mp ON p.nom = mp.nom
            WHERE 1=1
        ";

        $params = [];

        // Ajout des filtres selon les paramètres reçus
        if ($marque_onduleur) {
            $sql .= " AND mo.nom = :marque_onduleur";
            $params['marque_onduleur'] = $marque_onduleur;
        }

        if ($marque_panneau) {
            $sql .= " AND mp.nom = :marque_panneau";
            $params['marque_panneau'] = $marque_panneau;
        }

        if ($departement) {
            $sql .= " AND d.code = :departement";
            $params['departement'] = $departement;
        }

        $sql .= " ORDER BY i.an_installation DESC LIMIT 50";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;

    //---------------------------PAGE ACCUEIL-----------------------------------
    // Toutes les installations (page accueil)
    case 'all':
    default:
        $stmt = $pdo->query("
            SELECT i.id, i.an_installation, i.puissance_crete, c.nom_standard AS localite
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            ORDER BY i.an_installation DESC
            LIMIT 100
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
}