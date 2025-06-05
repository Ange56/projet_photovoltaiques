<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Headers CORS et Content-Type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Vérifier que la méthode HTTP est POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        "error" => true,
        "message" => "Seule la méthode POST est autorisée."
    ]);
    exit();
}

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../models/installation.php";

try {
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();

    if (!$db) {
        throw new Exception("Erreur de connexion à la base de données");
    }

    // Création de l'objet Installation
    $installation = new Installation($db);

    // Récupération des données JSON
    $data = json_decode(file_get_contents("php://input"));

    // Validation des données
    if (empty($data)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "error" => true,
            "message" => "Données JSON invalides ou vides."
        ]);
        exit();
    }

    // Validation des champs requis (ajustez selon votre modèle)
    $required_fields = ['nom', 'adresse', 'type']; // Exemple de champs requis
    $missing_fields = [];

    foreach ($required_fields as $field) {
        if (empty($data->$field)) {
            $missing_fields[] = $field;
        }
    }

    if (!empty($missing_fields)) {
        http_response_code(400); // Bad Request
        echo json_encode([
            "error" => true,
            "message" => "Champs requis manquants : " . implode(', ', $missing_fields)
        ]);
        exit();
    }

    // Attribution des propriétés à l'objet Installation
    $installation->nom = htmlspecialchars(strip_tags($data->nom));
    $installation->adresse = htmlspecialchars(strip_tags($data->adresse));
    $installation->type = htmlspecialchars(strip_tags($data->type));
    
    // Champs optionnels (ajustez selon votre modèle)
    if (isset($data->description)) {
        $installation->description = htmlspecialchars(strip_tags($data->description));
    }
    if (isset($data->date_installation)) {
        $installation->date_installation = $data->date_installation;
    }

    // Tentative de création
    if ($installation->create()) {
        http_response_code(201); // Created
        echo json_encode([
            "error" => false,
            "message" => "Installation créée avec succès.",
            "data" => [
                "id" => $installation->id ?? null,
                "nom" => $installation->nom,
                "adresse" => $installation->adresse,
                "type" => $installation->type,
                "created_at" => date('Y-m-d H:i:s')
            ]
        ]);
    } else {
        http_response_code(503); // Service Unavailable
        echo json_encode([
            "error" => true,
            "message" => "Impossible de créer l'installation. Erreur de service."
        ]);
    }

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode([
        "error" => true,
        "message" => "Erreur interne du serveur : " . $e->getMessage()
    ]);
}
?>