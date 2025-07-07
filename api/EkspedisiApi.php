<?php
require_once __DIR__ . '/../config.php';

class EkspedisiApi {
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = API_BASE_URL . '/kurir';
    }

    private function makeRequest($endpoint, $method = 'GET', $data = null) {
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
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'timeout' => 30
            ]
        ];

        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options['http']['content'] = json_encode($data);
        }

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

    public function getAllKurir($status = null) {
        $endpoint = '';
        if ($status) {
            $endpoint = '?status=' . $status;
        }
        return $this->makeRequest($endpoint);
    }

    public function getKurirById($id) {
        return $this->makeRequest('?id=' . $id);
    }

    public function createKurir($data) {
        return $this->makeRequest('', 'POST', $data);
    }

    public function updateKurir($id, $data) {
        $data['id'] = $id;
        return $this->makeRequest('', 'PUT', $data);
    }

    public function deleteKurir($id) {
        return $this->makeRequest('', 'DELETE', ['id' => $id]);
    }

    public function updateKurirStatus($id, $status) {
        return $this->makeRequest('', 'PATCH', ['id' => $id, 'status' => $status]);
    }

    public function searchKurir($query) {
        return $this->makeRequest('/search?q=' . urlencode($query));
    }

    public function getKurirStats() {
        return $this->makeRequest('/stats');
    }

    public function getKurirPerformance() {
        return $this->makeRequest('/performance');
    }

    public function getKurirDeliveryTime() {
        return $this->makeRequest('/delivery-time');
    }

    public function getKurirCostAnalysis() {
        return $this->makeRequest('/cost-analysis');
    }

    public function getKurirAnalytics() {
        return $this->makeRequest('/analytics');
    }

    public function getKurirUsageStats() {
        return $this->makeRequest('/usage-stats');
    }

    public function getPoorPerformingKurir($threshold = 70) {
        return $this->makeRequest('/poor-performers?threshold=' . $threshold);
    }

    public function getKurirTrends($days = 30) {
        return $this->makeRequest('/trends?days=' . $days);
    }

    public function getAvailableKurirCodes() {
        return $this->makeRequest('/available-codes');
    }

    public function importKurirFromApi() {
        return $this->makeRequest('/import', 'POST');
    }

    public function bulkUpdateStatus($data) {
        return $this->makeRequest('/bulk-update', 'POST', $data);
    }

    public function cleanupPoorPerformers($threshold) {
        return $this->makeRequest('/cleanup', 'POST', ['threshold' => $threshold]);
    }
}