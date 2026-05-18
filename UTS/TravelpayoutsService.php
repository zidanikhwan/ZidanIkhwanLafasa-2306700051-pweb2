<?php

class TravelpayoutsService {
    private $token;
    private $baseUrl = 'https://api.travelpayouts.com/aviasales/v3/prices_for_dates';

    public function __construct($token) {
        $this->token = $token;
    }

    private function curlGetJson(string $url): array {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            return [
                'error' => 'Network error',
                'details' => $errno ? ($errno . ': ' . $error) : $error,
            ];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'error' => 'Invalid JSON response',
                'details' => json_last_error_msg(),
                'http_code' => $httpCode,
            ];
        }

        if ($httpCode >= 400) {
            return [
                'error' => 'HTTP error',
                'http_code' => $httpCode,
                'data' => $data,
            ];
        }

        return $data;
    }

    public function searchFlights($origin, $destination, $departure_at, $currency = 'idr') {
        $params = [
            'origin' => $origin,
            'destination' => $destination,
            'departure_at' => $departure_at,
            'unique' => 'false',
            'sorting' => 'price',
            'direct' => 'false',
            'currency' => $currency,
            'limit' => 20,
            'token' => $this->token
        ];

        $url = $this->baseUrl . '?' . http_build_query(array_filter($params));

        return $this->curlGetJson($url);
    }

    public function searchAirports($query) {
        $url = "https://autocomplete.travelpayouts.com/places2?term=" . urlencode($query) . "&locale=en&types[]=airport&types[]=city";

        $data = $this->curlGetJson($url);
        return isset($data['error']) ? [] : $data;
    }

    public function getAirlineName($code) {
        $url = "https://autocomplete.travelpayouts.com/airlines?code=" . $code . "&locale=en";

        $data = $this->curlGetJson($url);
        if (isset($data['error'])) {
            return $code;
        }

        if (!empty($data) && isset($data[0]['name'])) {
            return $data[0]['name'];
        }
        
        return $code;
    }
}
?>
