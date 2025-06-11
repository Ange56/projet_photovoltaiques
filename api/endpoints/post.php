<?php
// En-têtes HTTP
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Fichiers requis
include_once '../../config/database.php';
include_once '../../models/installation.php';

// Connexion DB
$database = new Database();
$db = $database->getConnection();

// Instance de l'objet Installation
$installation = new Installation($db);

// Récupérer les données JSON
$data = json_decode(file_get_contents("php://input"));

if (
    !empty($data->nom) &&
    !empty($data->puissance) &&
    !empty($data->adresse)
) {
    // Remplir l'objet
    $installation->nom = $data->nom;
    $installation->puissance = $data->puissance;
    $installation->adresse = $data->adresse;

    // Création
    if ($installation->create()) {
        http_response_code(201);
        echo json_encode(["message" => "Installation créée avec succès."]);
    } else {
        http_response_code(503);
        echo json_encode(["message" => "Impossible de créer l'installation."]);
    }
} else {
    http_response_code(400);
    echo json_encode(["message" => "Données incomplètes."]);
}
