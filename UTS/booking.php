<?php
function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$airline = (string) ($_GET['airline'] ?? 'Unknown');
$flight = (string) ($_GET['flight'] ?? '-');
$price = (float) ($_GET['price'] ?? 0);
$currency = strtoupper(trim((string) ($_GET['currency'] ?? 'IDR')));
$origin = strtoupper(trim((string) ($_GET['origin'] ?? '-')));
$destination = strtoupper(trim((string) ($_GET['destination'] ?? '-')));
$seat = strtoupper(trim((string) ($_GET['seat'] ?? '-')));

$isIata = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
$isCurrency = static fn ($v) => (bool) preg_match('/^[A-Z]{3}$/', $v);
$isSeat = static fn ($v) => $v === '-' || (bool) preg_match('/^\d{1,2}[A-Z]$/', $v);
if (!$isIata($origin) || !$isIata($destination) || !$isCurrency($currency) || !$isSeat($seat) || $price < 0) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Penumpang - SkyHigh Luxury</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        :root {
            --background: #f8f9ff;
            --surface: #ffffff;
            --surface-container: #e5eeff;
            --surface-container-low: #eff4ff;
            --primary: #00030a;
            --primary-container: #0a1d37;
            --secondary: #00658d;
            --secondary-container: #2dbcfe;
            --on-surface: #0b1c30;
            --on-surface-variant: #44474d;
            --outline-variant: #c5c6ce;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--background);
            color: var(--on-surface);
            font-family: 'Inter', sans-serif;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 20;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 48px;
            background: var(--surface);
            border-bottom: 1px solid var(--outline-variant);
        }

        .brand {
            color: var(--primary);
            font-size: 1.55rem;
            font-weight: 800;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 32px;
            align-items: center;
            font-size: 0.9rem;
        }

        .nav-links a {
            color: var(--on-surface-variant);
            text-decoration: none;
        }

        .nav-links .active {
            color: var(--secondary);
            border-bottom: 2px solid var(--secondary);
            font-weight: 700;
            padding-bottom: 6px;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            color: var(--primary);
        }

        .icon-button {
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: inherit;
            cursor: pointer;
        }

        .icon-button:hover,
        .icon-button:focus-visible {
            background: var(--surface-container-low);
            outline: none;
        }

        main {
            padding: 128px 24px 56px;
        }

        .page {
            width: min(1000px, 100%);
            margin: 0 auto;
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 40px;
            color: var(--outline-variant);
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .step-number {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 2px solid currentColor;
            font-size: 0.75rem;
        }

        .step.active,
        .step.done {
            color: var(--secondary);
        }

        .step.done .step-number,
        .step.active .step-number {
            background: var(--secondary);
            border-color: var(--secondary);
            color: white;
        }

        .step-line {
            width: 48px;
            height: 1px;
            background: var(--outline-variant);
        }

        .summary-panel {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 32px;
            align-items: center;
            margin-bottom: 32px;
            padding: 28px 32px;
            background: var(--surface);
            border: 1px solid var(--outline-variant);
            border-radius: 28px;
            box-shadow: 0 4px 20px rgba(0, 3, 10, 0.04);
        }

        .flight-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 12px;
            margin-bottom: 14px;
            border-radius: 999px;
            background: var(--surface-container);
            color: var(--secondary);
            font-size: 0.78rem;
            font-weight: 800;
        }

        .route {
            margin: 0 0 10px;
            color: var(--primary);
            font-size: clamp(1.7rem, 4vw, 2.5rem);
            line-height: 1.1;
            font-weight: 800;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: var(--on-surface-variant);
            font-size: 0.92rem;
        }

        .price-block {
            min-width: 220px;
            text-align: right;
        }

        .price-label {
            margin: 0 0 8px;
            color: var(--on-surface-variant);
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
        }

        .price {
            margin: 0;
            color: var(--secondary);
            font-size: clamp(1.8rem, 4vw, 2.3rem);
            font-weight: 800;
            letter-spacing: 0;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 28px;
            align-items: start;
        }

        .form-panel,
        .side-panel {
            background: var(--surface);
            border: 1px solid var(--outline-variant);
            border-radius: 28px;
            box-shadow: 0 4px 20px rgba(0, 3, 10, 0.04);
        }

        .form-panel {
            padding: 40px;
        }

        .form-header {
            margin-bottom: 28px;
        }

        h1 {
            margin: 0 0 8px;
            color: var(--primary);
            font-size: clamp(1.7rem, 3vw, 2.15rem);
            line-height: 1.2;
        }

        .form-subtitle {
            margin: 0;
            max-width: 620px;
            color: var(--on-surface-variant);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 700;
        }

        input {
            width: 100%;
            min-height: 54px;
            padding: 0 16px;
            border: 1px solid var(--outline-variant);
            border-radius: 14px;
            background: white;
            color: var(--on-surface);
            font: inherit;
            font-size: 1rem;
            transition: border-color 180ms ease, box-shadow 180ms ease, background-color 180ms ease;
        }

        input::placeholder {
            color: #6b7280;
        }

        input:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 4px rgba(0, 101, 141, 0.12);
            outline: none;
        }

        .btn-confirm,
        .btn-secondary {
            width: 100%;
            min-height: 56px;
            border-radius: 14px;
            font: inherit;
            font-weight: 800;
            cursor: pointer;
            transition: background-color 180ms ease, border-color 180ms ease, color 180ms ease, transform 180ms ease;
        }

        .btn-confirm {
            grid-column: 1 / -1;
            margin-top: 10px;
            border: 0;
            background: var(--secondary);
            color: white;
        }

        .btn-confirm:hover,
        .btn-confirm:focus-visible {
            background: var(--primary);
            outline: none;
        }

        .btn-confirm:active,
        .btn-secondary:active {
            transform: translateY(1px);
        }

        .side-panel {
            padding: 28px;
        }

        .side-title {
            margin: 0 0 18px;
            color: var(--primary);
            font-size: 1rem;
            font-weight: 800;
        }

        .detail-list {
            display: grid;
            gap: 16px;
            margin-bottom: 24px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--surface-container);
            color: var(--on-surface-variant);
            font-size: 0.88rem;
        }

        .detail-item strong {
            color: var(--primary);
            text-align: right;
        }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--outline-variant);
            background: white;
            color: var(--primary);
            text-decoration: none;
        }

        .btn-secondary:hover,
        .btn-secondary:focus-visible {
            border-color: var(--secondary);
            color: var(--secondary);
            outline: none;
        }

        @media (max-width: 860px) {
            .navbar {
                padding: 0 20px;
            }

            .nav-links {
                display: none;
            }

            .summary-panel,
            .content-grid {
                grid-template-columns: 1fr;
            }

            .price-block {
                text-align: left;
            }

            .stepper {
                justify-content: flex-start;
                overflow-x: auto;
                padding-bottom: 8px;
            }

            .step-line {
                width: 28px;
                flex: 0 0 auto;
            }
        }

        @media (max-width: 620px) {
            main {
                padding: 112px 16px 40px;
            }

            .summary-panel,
            .form-panel,
            .side-panel {
                border-radius: 20px;
                padding: 24px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="brand" href="index.php">SkyHigh Luxury</a>
        <div class="nav-links" aria-label="Main navigation">
            <a class="active" href="index.php">Flights</a>
            <a href="#">Hotels</a>
            <a href="#">Experiences</a>
        </div>
        <div class="nav-actions">
            <button class="icon-button" type="button" aria-label="Change language">
                <span class="material-symbols-outlined">language</span>
            </button>
            <button class="icon-button" type="button" aria-label="Account">
                <span class="material-symbols-outlined">account_circle</span>
            </button>
        </div>
    </nav>

    <main>
        <div class="page">
            <div class="stepper" aria-label="Booking progress">
                <div class="step done"><span class="step-number">1</span><span>Penerbangan</span></div>
                <div class="step-line"></div>
                <div class="step done"><span class="step-number">2</span><span>Kursi</span></div>
                <div class="step-line"></div>
                <div class="step active"><span class="step-number">3</span><span>Penumpang</span></div>
                <div class="step-line"></div>
                <div class="step"><span class="step-number">4</span><span>Selesai</span></div>
            </div>

            <section class="summary-panel" aria-label="Ringkasan penerbangan">
                <div>
                    <div class="flight-chip">
                        <span class="material-symbols-outlined" aria-hidden="true">flight_takeoff</span>
                        <?= h($airline) ?> - <?= h($flight) ?>
                    </div>
                    <h2 class="route"><?= h($origin) ?> &rarr; <?= h($destination) ?></h2>
                    <div class="meta">
                        <span>Kursi <strong><?= h($seat) ?></strong></span>
                        <span>1 Penumpang</span>
                    </div>
                </div>
                <div class="price-block">
                    <p class="price-label">Total Pembayaran</p>
                    <p class="price"><?= h($currency) ?> <?= h(number_format($price, 0, ',', '.')) ?></p>
                </div>
            </section>

            <div class="content-grid">
                <section class="form-panel">
                    <div class="form-header">
                        <h1>Detail Penumpang</h1>
                        <p class="form-subtitle">Pastikan data sesuai KTP atau paspor agar proses check-in dan e-ticket tidak bermasalah.</p>
                    </div>

                    <form action="confirmation.php" method="POST" class="form-grid">
                        <input type="hidden" name="flight_info" value="<?= h($airline) ?> (<?= h($flight) ?>) <?= h($origin) ?>-<?= h($destination) ?> Seat <?= h($seat) ?>">
                        <input type="hidden" name="total" value="<?= h($currency) ?> <?= h(number_format($price, 0, ',', '.')) ?>">

                        <div class="form-group full">
                            <label for="fullname">Nama Lengkap</label>
                            <input type="text" id="fullname" name="fullname" placeholder="Contoh: John Doe" autocomplete="name" required>
                        </div>

                        <div class="form-group full">
                            <label for="email">Alamat Email</label>
                            <input type="email" id="email" name="email" placeholder="john@example.com" autocomplete="email" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Nomor Telepon</label>
                            <input type="tel" id="phone" name="phone" placeholder="+62..." autocomplete="tel" required>
                        </div>

                        <div class="form-group">
                            <label for="passport">NIK atau Paspor</label>
                            <input type="text" id="passport" name="passport" placeholder="Nomor ID Anda" autocomplete="off" required>
                        </div>

                        <button type="submit" class="btn-confirm">Konfirmasi Pemesanan</button>
                    </form>
                </section>

                <aside class="side-panel" aria-label="Detail perjalanan">
                    <h2 class="side-title">Detail Perjalanan</h2>
                    <div class="detail-list">
                        <div class="detail-item"><span>Maskapai</span><strong><?= h($airline) ?></strong></div>
                        <div class="detail-item"><span>Nomor Flight</span><strong><?= h($flight) ?></strong></div>
                        <div class="detail-item"><span>Kursi</span><strong><?= h($seat) ?></strong></div>
                        <div class="detail-item"><span>Total</span><strong><?= h($currency) ?> <?= h(number_format($price, 0, ',', '.')) ?></strong></div>
                    </div>
                    <a class="btn-secondary" href="index.php">Ubah Pencarian</a>
                </aside>
            </div>
        </div>
    </main>
</body>
</html>
