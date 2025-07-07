<?php
require_once 'api/KategoriApi.php';

class KategoriModel {
    private $kategoriApi;

    public function __construct() {
        $this->kategoriApi = new KategoriApi();
    }

    public function getAllKategori() {
        return $this->kategoriApi->getAllKategori();
    }

    public function getKategoriById($id) {
        return $this->kategoriApi->getKategoriById($id);
    }

    public function createKategori($data) {
        return $this->kategoriApi->createKategori($data);
    }

    public function updateKategori($id, $data) {
        return $this->kategoriApi->updateKategori($id, $data);
    }

    public function deleteKategori($id) {
        return $this->kategoriApi->deleteKategori($id);
    }

    public function getKategoriProducts($id) {
        return $this->kategoriApi->getKategoriProducts($id);
    }
}