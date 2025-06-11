<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Content-Type: application/json; charset=UTF-8");

// Permettre les requêtes CORS si nécessaire
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gérer les requêtes OPTIONS (preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Vérifier que c'est bien une requête DELETE
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
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
        case 'delete_installation':
            // Validation des données
            if (!isset($input['id']) || empty($input['id'])) {
                throw new Exception('ID de l\'installation requis');
            }
            
            $id = $input['id'];
            
            // Vérifier que l'installation existe
            $stmt = $pdo->prepare("SELECT id FROM Installation WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                throw new Exception('Installation non trouvée');
            }
            
            // Supprimer l'installation
            $stmt = $pdo->prepare("DELETE FROM Installation WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Installation supprimée avec succès'
                ]);
            } else {
                throw new Exception('Erreur lors de la suppression en base de données');
            }
            break;
            
        default:
            throw new Exception('Action non reconnue');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données: ' . $e->getMessage()
    ]);
}
?>