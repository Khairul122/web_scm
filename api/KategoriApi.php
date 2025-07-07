<?php
require_once __DIR__ . '/../config.php';

class KategoriApi {
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = API_BASE_URL . '/kategori';
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

        if ($data && in_array($method, ['POST', 'PUT'])) {
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

    public function getAllKategori() {
        return $this->makeRequest('');
    }

    public function getKategoriById($id) {
        return $this->makeRequest('/' . $id);
    }

    public function createKategori($data) {
        return $this->makeRequest('', 'POST', $data);
    }

    public function updateKategori($id, $data) {
        return $this->makeRequest('/' . $id, 'PUT', $data);
    }

    public function deleteKategori($id) {
        return $this->makeRequest('/' . $id, 'DELETE');
    }

    public function getKategoriProducts($id) {
        return $this->makeRequest('/' . $id . '/products');
    }
}