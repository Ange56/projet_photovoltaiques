<?php
$request = $_SERVER['REQUEST_URI'];

if (preg_match('/\/installation\/get$/', $request)) {
    require __DIR__ . '/endpoints/installation/get.php';
} else {
    http_response_code(404);
    echo json_encode(["message" => "Route non trouvée"]);
}
