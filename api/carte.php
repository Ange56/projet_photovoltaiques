<?php
require_once '/config/database.php'; // Ton fichier de connexion PDO

header('Content-Type: application/json');

// Années distinctes
$years = $pdo->query("SELECT DISTINCT YEAR(an_installation) AS annee FROM Installation ORDER BY annee DESC LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

// 20 départements aléatoires
$departements = $pdo->query("SELECT code, nom FROM Departement ORDER BY RAND() LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "annees" => $years,
    "departements" => $departements
]);
?>