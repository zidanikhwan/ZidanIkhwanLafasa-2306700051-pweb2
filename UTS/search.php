<?php
require_once 'config.php';

function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$origin = strtoupper(trim($_GET['origin'] ?? ''));
$destination = strtoupper(trim($_GET['destination'] ?? ''));
$departure_at = trim($_GET['departure_at'] ?? '');
$currency = strtoupper(trim($_GET['currency'] ?? 'IDR'));

$isIata = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
$isCurrency = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
$dt = DateTime::createFromFormat('Y-m-d', $departure_at);
$isDate = $dt && $dt->format('Y-m-d') === $departure_at;

if (!$isIata($origin) || !$isIata($destination) || !$isDate || !$isCurrency($currency)) {
    header('Location: index.php');
    exit;
}

$response = $flightService->searchFlights($origin, $destination, $departure_at, $currency);
$results = $response['data'] ?? [];
$error = isset($response['error']) ? $response['error'] : (empty($results) ? "No flights found for this route on the selected date." : null);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voyage Results - SkyExplorer</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #10b981;
            --secondary: #6366f1;
            --bg: #020617;
            --surface: rgba(15, 23, 42, 0.4);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-dim: #94a3b8;
            --font-heading: 'Outfit', sans-serif;
            --font-body: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg);
            color: var(--text-main);
            font-family: var(--font-body);
            min-height: 100vh;
            padding: 40px 20px;
            background-image: 
                radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.1) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(16, 185, 129, 0.05) 0px, transparent 50%);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            animation: fadeIn 0.8s ease-out;
        }

        .logo {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(to right, #fff, var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-decoration: none;
        }

        .search-info {
            background: var(--surface);
            padding: 8px 24px;
            border-radius: 100px;
            border: 1px solid var(--glass-border);
            font-size: 0.9rem;
            color: var(--text-dim);
            backdrop-filter: blur(10px);
        }

        .search-info b { color: var(--primary); }

        /* Luminous Flight Card */
        .flight-card {
            background: var(--surface);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            padding: 35px;
            margin-bottom: 25px;
            border: 1px solid var(--glass-border);
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            align-items: center;
            gap: 30px;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            animation: slideUp 0.6s ease-out backwards;
        }

        .flight-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .flight-card:hover {
            transform: scale(1.02) translateX(10px);
            border-color: rgba(16, 185, 129, 0.4);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .flight-card:hover::before { opacity: 1; }

        .airline-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .airline-name {
            font-family: var(--font-heading);
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
        }

        .flight-no {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-dim);
            background: rgba(255,255,255,0.05);
            padding: 4px 10px;
            border-radius: 6px;
            width: fit-content;
        }

        .flight-path {
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .node {
            text-align: center;
            z-index: 2;
        }

        .node .iata {
            font-family: var(--font-heading);
            font-size: 2.2rem;
            font-weight: 800;
            display: block;
            line-height: 1;
        }

        .node .label {
            font-size: 0.75rem;
            color: var(--text-dim);
            margin-top: 5px;
            display: block;
        }

        .path-visual {
            flex-grow: 1;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--glass-border), transparent);
            position: relative;
            margin: 0 20px;
        }

        .path-visual::after {
            content: '✈';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(90deg);
            color: var(--primary);
            font-size: 1.2rem;
            text-shadow: 0 0 10px var(--primary-glow);
        }

        .price-tag {
            text-align: right;
        }

        .amount {
            font-family: var(--font-heading);
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--primary);
            display: block;
            margin-bottom: 12px;
        }

        .btn-reserve {
            padding: 14px 28px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: white;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-reserve:hover {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 20px var(--primary-glow);
            transform: translateY(-2px);
        }

        .error-state {
            text-align: center;
            padding: 60px;
            background: var(--surface);
            border-radius: 32px;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-state h2 { color: #f87171; margin-bottom: 10px; }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (max-width: 768px) {
            .flight-card { grid-template-columns: 1fr; text-align: center; gap: 40px; }
            .airline-meta { align-items: center; }
            .price-tag { text-align: center; }
            .node .iata { font-size: 1.8rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-bar">
            <a href="index.php" class="logo">SkyExplorer</a>
            <div class="search-info">
                <b><?= h($origin) ?></b> to <b><?= h($destination) ?></b> • <?= h(date('d M Y', strtotime($departure_at))) ?>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="error-state">
                <h2>No Voyages Available</h2>
                <p><?= h($error) ?></p>
                <br>
                <a href="index.php" class="btn-reserve">New Search</a>
            </div>
        <?php else: ?>
            <?php 
                $airlineCache = [];
                $i = 0;
                foreach ($results as $flight): 
                    $carrier = $flight['airline'] ?? 'Carrier';
                    if (!isset($airlineCache[$carrier])) {
                        $airlineCache[$carrier] = $flightService->getAirlineName($carrier);
                    }
                    $airlineName = $airlineCache[$carrier];
                    $i++;
            ?>
                <div class="flight-card" style="animation-delay: <?= $i * 0.1 ?>s">
                    <div class="airline-meta">
                        <span class="airline-name"><?= h($airlineName) ?></span>
                        <span class="flight-no">Flight <?= h($flight['flight_number'] ?? '-') ?></span>
                    </div>

                    <div class="flight-path">
                        <div class="node">
                            <span class="iata"><?= h($origin) ?></span>
                            <span class="label">Origin</span>
                        </div>
                        <div class="path-visual"></div>
                        <div class="node">
                            <span class="iata"><?= h($destination) ?></span>
                            <span class="label">Destination</span>
                        </div>
                    </div>

                    <div class="price-tag">
                        <span class="amount"><?= h(number_format((float) ($flight['price'] ?? 0), 0, ',', '.')) ?> <?= h($currency) ?></span>
                        <a href="booking.php?airline=<?= urlencode($airlineName) ?>&flight=<?= urlencode($flight['flight_number'] ?? '') ?>&price=<?= urlencode((string) ($flight['price'] ?? 0)) ?>&currency=<?= urlencode($currency) ?>&origin=<?= urlencode($origin) ?>&destination=<?= urlencode($destination) ?>" class="btn-reserve">Reserve</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
