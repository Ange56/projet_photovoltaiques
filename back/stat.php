<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . '/../api/config/database.php';

$database = new Database();
$pdo = $database->getConnection();

$action = $_GET['action'] ?? 'all';

switch ($action) {
    case 'general':
        // Statistiques générales
        $stats = [];
        
        // Nombre total d'installations
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Installation");
        $stats['total_installations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Nombre d'installateurs
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Installateur");
        $stats['total_installateurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Nombre de marques d'onduleurs
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Marque_onduleur");
        $stats['total_marques_onduleurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Nombre de marques de panneaux
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Marque_panneau");
        $stats['total_marques_panneaux'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo json_encode($stats);
        break;
        
    case 'installations_par_annee':
        // Nombre d'installations par année
        $stmt = $pdo->query("
            SELECT YEAR(an_installation) as annee, COUNT(*) as count 
            FROM Installation 
            GROUP BY YEAR(an_installation) 
            ORDER BY annee
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'installations_par_region':
        // Nombre d'installations par région
        $stmt = $pdo->query("
            SELECT r.nom as region, COUNT(*) as count
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            JOIN Region r ON d.code_Region = r.code
            GROUP BY r.nom
            ORDER BY count DESC
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'installations_annee_region':
        // Nombre d'installations par année et région
        $stmt = $pdo->query("
            SELECT YEAR(i.an_installation) as annee, r.nom as region, COUNT(*) as count
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            JOIN Region r ON d.code_Region = r.code
            GROUP BY YEAR(i.an_installation), r.nom
            ORDER BY annee, r.nom
        ");
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        break;
        
    case 'all':
    default:
        // Toutes les statistiques en une fois
        $allStats = [];
        
        // Statistiques générales
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Installation");
        $allStats['general']['total_installations'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Installateur");
        $allStats['general']['total_installateurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Marque_onduleur");
        $allStats['general']['total_marques_onduleurs'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM Marque_panneau");
        $allStats['general']['total_marques_panneaux'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Installations par année
        $stmt = $pdo->query("
            SELECT YEAR(an_installation) as annee, COUNT(*) as count 
            FROM Installation 
            GROUP BY YEAR(an_installation) 
            ORDER BY annee
        ");
        $allStats['installations_par_annee'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Installations par région
        $stmt = $pdo->query("
            SELECT r.nom as region, COUNT(*) as count
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            JOIN Region r ON d.code_Region = r.code
            GROUP BY r.nom
            ORDER BY count DESC
        ");
        $allStats['installations_par_region'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Installations par année et région
        $stmt = $pdo->query("
            SELECT YEAR(i.an_installation) as annee, r.nom as region, COUNT(*) as count
            FROM Installation i
            JOIN Communes c ON i.code_insee = c.code_insee
            JOIN Departement d ON c.code = d.code
            JOIN Region r ON d.code_Region = r.code
            GROUP BY YEAR(i.an_installation), r.nom
            ORDER BY annee, r.nom
        ");
        $allStats['installations_annee_region'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($allStats);
        break;
}
?>