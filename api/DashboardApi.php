<?php
require_once __DIR__ . '/../config.php';

class DashboardApi {
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = API_BASE_URL . '/dashboard';
    }

    private function makeRequest($endpoint) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json'
        ];

        if (isset($_SESSION['api_token'])) {
            $headers[] = 'Authorization: Bearer ' . $_SESSION['api_token'];
        }

        $options = [
            'http' => [
                'method' => 'GET',
                'header' => implode("\r\n", $headers),
                'timeout' => 30
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return [
                'success' => false,
                'error' => 'Gagal terhubung ke backend',
                'data' => []
            ];
        }

        $decodedResponse = json_decode($response, true);
        
        return [
            'success' => !isset($decodedResponse['error']),
            'data' => $decodedResponse,
            'error' => $decodedResponse['error'] ?? null
        ];
    }

    public function getAdminDashboard() {
        return $this->makeRequest('/admin');
    }

    public function getPengepulDashboard() {
        return $this->makeRequest('/pengepul');
    }

    public function getRoastingDashboard() {
        return $this->makeRequest('/roasting');
    }

    public function getPenjualDashboard() {
        return $this->makeRequest('/penjual');
    }
}