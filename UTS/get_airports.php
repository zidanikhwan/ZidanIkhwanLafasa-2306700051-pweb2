<?php
require_once 'config.php';

header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if (strlen($term) < 2) {
    echo json_encode([]);
    exit;
}

$results = $flightService->searchAirports($term);
echo json_encode($results);
?>
