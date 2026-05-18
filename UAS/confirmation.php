<?php
function h($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$fullname = $_POST['fullname'] ?? 'Tamu';
$flight_info = $_POST['flight_info'] ?? '-';
$total = $_POST['total'] ?? '-';
$email = $_POST['email'] ?? '';
$booking_id = strtoupper(bin2hex(random_bytes(4)));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Berhasil - SkyHigh Luxury</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        :root {
            --background: #f8f9ff;
            --surface: #ffffff;
            --surface-container: #e5eeff;
            --surface-container-low: #eff4ff;
            --primary: #00030a;
            --secondary: #00658d;
            --on-surface: #0b1c30;
            --on-surface-variant: #44474d;
            --outline-variant: #c5c6ce;
            --success: #00885a;
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

        .nav-actions {
            display: flex;
            gap: 12px;
        }

        .icon-button {
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: var(--primary);
            cursor: pointer;
        }

        .icon-button:hover,
        .icon-button:focus-visible {
            background: var(--surface-container-low);
            outline: none;
        }

        main {
            min-height: calc(100vh - 80px);
            display: grid;
            place-items: center;
            padding: 48px 24px;
        }

        .success-panel {
            width: min(760px, 100%);
            padding: 44px;
            background: var(--surface);
            border: 1px solid var(--outline-variant);
            border-radius: 28px;
            box-shadow: 0 4px 20px rgba(0, 3, 10, 0.04);
        }

        .success-icon {
            width: 72px;
            height: 72px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            border-radius: 50%;
            background: #e7f8ef;
            color: var(--success);
        }

        .success-icon .material-symbols-outlined {
            font-size: 40px;
        }

        h1 {
            margin: 0 0 10px;
            color: var(--primary);
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.1;
        }

        .lead {
            margin: 0 0 32px;
            color: var(--on-surface-variant);
            font-size: 1rem;
            line-height: 1.7;
        }

        .ticket-info {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .ticket-item {
            padding: 18px;
            background: var(--surface-container-low);
            border: 1px solid var(--surface-container);
            border-radius: 16px;
        }

        .ticket-item.full {
            grid-column: 1 / -1;
        }

        .label {
            margin-bottom: 6px;
            color: var(--on-surface-variant);
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .value {
            color: var(--primary);
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .value.price {
            color: var(--secondary);
            font-size: 1.25rem;
        }

        .actions {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .btn-home,
        .btn-secondary {
            min-height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 22px;
            border-radius: 14px;
            font-weight: 800;
            text-decoration: none;
            transition: background-color 180ms ease, border-color 180ms ease, color 180ms ease;
        }

        .btn-home {
            background: var(--secondary);
            color: white;
        }

        .btn-home:hover,
        .btn-home:focus-visible {
            background: var(--primary);
            outline: none;
        }

        .btn-secondary {
            border: 1px solid var(--outline-variant);
            color: var(--primary);
        }

        .btn-secondary:hover,
        .btn-secondary:focus-visible {
            border-color: var(--secondary);
            color: var(--secondary);
            outline: none;
        }

        @media (max-width: 620px) {
            .navbar {
                padding: 0 20px;
            }

            .success-panel {
                padding: 28px;
                border-radius: 22px;
            }

            .ticket-info {
                grid-template-columns: 1fr;
            }

            .actions a {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a class="brand" href="index.php">SkyHigh Luxury</a>
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
        <section class="success-panel">
            <div class="success-icon" aria-hidden="true">
                <span class="material-symbols-outlined">check_circle</span>
            </div>
            <h1>Pemesanan Berhasil</h1>
            <p class="lead">E-ticket Anda telah dikirim ke email <strong><?= h($email) ?></strong>. Simpan ID pemesanan ini untuk check-in dan bantuan pelanggan.</p>

            <div class="ticket-info">
                <div class="ticket-item">
                    <div class="label">ID Pemesanan</div>
                    <div class="value">#<?= h($booking_id) ?></div>
                </div>
                <div class="ticket-item">
                    <div class="label">Penumpang</div>
                    <div class="value"><?= h($fullname) ?></div>
                </div>
                <div class="ticket-item full">
                    <div class="label">Penerbangan</div>
                    <div class="value"><?= h($flight_info) ?></div>
                </div>
                <div class="ticket-item full">
                    <div class="label">Total Pembayaran</div>
                    <div class="value price"><?= h($total) ?></div>
                </div>
            </div>

            <div class="actions">
                <a href="index.php" class="btn-home">Kembali ke Beranda</a>
                <a href="index.php" class="btn-secondary">Cari Penerbangan Baru</a>
            </div>
        </section>
    </main>
</body>
</html>
