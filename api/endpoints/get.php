<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../models/installation.php";


// Connexion à la base de données
$database = new Database();
$db = $database->getConnection();

// Création de l'objet Installation
$installation = new Installation($db);

// Récupération des données
$stmt = $installation->readAll();
$num = $stmt->rowCount();

if ($num > 0) {
    $installations = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $installations[] = $row;
    }

    echo json_encode($installations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(["message" => "Aucune installation trouvée."]);
}
?>
