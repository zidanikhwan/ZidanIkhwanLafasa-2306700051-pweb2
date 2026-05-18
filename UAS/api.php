<?php

require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$action = $_GET['action'] ?? '';

if ($action === 'search_airports') {
    $term = $_GET['term'] ?? '';
    if (strlen($term) < 2) {
        echo json_encode([]);
        exit;
    }
    $results = $flightService->searchAirports($term);
    echo json_encode($results);
    exit;
}

if ($action === 'search_flights') {
    $origin = strtoupper($_GET['origin'] ?? '');
    $destination = strtoupper($_GET['destination'] ?? '');
    $departure_at = $_GET['departure_at'] ?? '';
    $currency = $_GET['currency'] ?? 'IDR';

    $isIata = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
    $isCurrency = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
    $dt = DateTime::createFromFormat('Y-m-d', $departure_at);
    $isDate = $dt && $dt->format('Y-m-d') === $departure_at;

    if (!$isIata($origin) || !$isIata($destination) || !$isDate || !$isCurrency(strtoupper($currency))) {
        http_response_code(400);
        echo json_encode(['error' => 'Parameter tidak valid']);
        exit;
    }

    $response = $flightService->searchFlights($origin, $destination, $departure_at, strtoupper($currency));
    

    if (isset($response['data'])) {
        $airlineCache = [];
        foreach ($response['data'] as &$flight) {
            $carrier = $flight['airline'];
            if (!isset($airlineCache[$carrier])) {
                $airlineCache[$carrier] = $flightService->getAirlineName($carrier);
            }
            $flight['airline_full_name'] = $airlineCache[$carrier];
        }
    }

    echo json_encode($response);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Action tidak valid']);
?>
