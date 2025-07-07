<?php
require_once 'api/KategoriApi.php';

class KategoriModel {
    private $kategoriApi;

    public function __construct() {
        $this->kategoriApi = new KategoriApi();
    }

    public function getAllKategori() {
        $result = $this->kategoriApi->getAllKategori();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKategoriById($id) {
        $result = $this->kategoriApi->getKategoriById($id);
        
        if ($result['success']) {
            return $result['data']['data'] ?? null;
        }
        
        return null;
    }

    public function createKategori($data) {
        $result = $this->kategoriApi->createKategori($data);
        
        return [
            'success' => $result['success'],
            'data' => $result['data']['data'] ?? null,
            'error' => $result['error']
        ];
    }

    public function updateKategori($id, $data) {
        $result = $this->kategoriApi->updateKategori($id, $data);
        
        return [
            'success' => $result['success'],
            'data' => $result['data']['data'] ?? null,
            'error' => $result['error']
        ];
    }

    public function deleteKategori($id) {
        $result = $this->kategoriApi->deleteKategori($id);
        
        return [
            'success' => $result['success'],
            'error' => $result['error']
        ];
    }
}