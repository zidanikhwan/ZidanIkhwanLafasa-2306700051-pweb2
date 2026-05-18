<?php
require_once 'TravelpayoutsService.php';

// Prefer setting TRAVELPAYOUTS_TOKEN via environment variable to avoid committing secrets.
// Example (Windows PowerShell): $env:TRAVELPAYOUTS_TOKEN="your_token_here"
$tokenFromEnv = getenv('TRAVELPAYOUTS_TOKEN');

if (!defined('TRAVELPAYOUTS_TOKEN')) {
    define('TRAVELPAYOUTS_TOKEN', $tokenFromEnv ?: 'd0a9dc5751182944080d692d6291702d');
}

$flightService = new TravelpayoutsService(TRAVELPAYOUTS_TOKEN);
?>
