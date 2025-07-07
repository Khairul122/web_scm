<?php
require_once 'api/EkspedisiApi.php';

class EkspedisiModel {
    private $ekspedisiApi;

    public function __construct() {
        $this->ekspedisiApi = new EkspedisiApi();
    }

    public function getAllKurir() {
        $result = $this->ekspedisiApi->getAllKurir();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getActiveKurir() {
        $result = $this->ekspedisiApi->getAllKurir('active');
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirById($id) {
        $result = $this->ekspedisiApi->getKurirById($id);
        
        if ($result['success']) {
            return $result['data']['data'] ?? null;
        }
        
        return null;
    }

    public function createKurir($data) {
        $result = $this->ekspedisiApi->createKurir($data);
        
        return [
            'success' => $result['success'],
            'data' => $result['data']['data'] ?? null,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal menambahkan ekspedisi')
        ];
    }

    public function updateKurir($id, $data) {
        $result = $this->ekspedisiApi->updateKurir($id, $data);
        
        return [
            'success' => $result['success'],
            'data' => $result['data']['data'] ?? null,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal memperbarui ekspedisi')
        ];
    }

    public function deleteKurir($id) {
        $result = $this->ekspedisiApi->deleteKurir($id);
        
        return [
            'success' => $result['success'],
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal menghapus ekspedisi')
        ];
    }

    public function updateKurirStatus($id, $status) {
        $result = $this->ekspedisiApi->updateKurirStatus($id, $status);
        
        return [
            'success' => $result['success'],
            'data' => $result['data']['data'] ?? null,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal memperbarui status ekspedisi')
        ];
    }

    public function searchKurir($query) {
        $result = $this->ekspedisiApi->searchKurir($query);
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirStats() {
        $result = $this->ekspedisiApi->getKurirStats();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [
            'total_kurir' => 0,
            'active_kurir' => 0,
            'inactive_kurir' => 0
        ];
    }

    public function getKurirPerformance() {
        $result = $this->ekspedisiApi->getKurirPerformance();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirDeliveryTime() {
        $result = $this->ekspedisiApi->getKurirDeliveryTime();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirCostAnalysis() {
        $result = $this->ekspedisiApi->getKurirCostAnalysis();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirAnalytics() {
        $result = $this->ekspedisiApi->getKurirAnalytics();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [
            'overview' => [],
            'performance' => [],
            'delivery_time' => [],
            'cost_analysis' => [],
            'usage_statistics' => []
        ];
    }

    public function getKurirUsageStats() {
        $result = $this->ekspedisiApi->getKurirUsageStats();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getPoorPerformingKurir($threshold = 70) {
        $result = $this->ekspedisiApi->getPoorPerformingKurir($threshold);
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getKurirTrends($days = 30) {
        $result = $this->ekspedisiApi->getKurirTrends($days);
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [];
    }

    public function getAvailableKurirCodes() {
        $result = $this->ekspedisiApi->getAvailableKurirCodes();
        
        if ($result['success']) {
            return $result['data']['data'] ?? [];
        }
        
        return [
            ['kode' => 'jne', 'nama' => 'JNE'],
            ['kode' => 'pos', 'nama' => 'POS Indonesia'],
            ['kode' => 'tiki', 'nama' => 'TIKI']
        ];
    }

    public function importKurirFromApi() {
        $result = $this->ekspedisiApi->importKurirFromApi();
        
        return [
            'success' => $result['success'],
            'imported_count' => $result['data']['imported_count'] ?? 0,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal mengimpor ekspedisi')
        ];
    }

    public function bulkUpdateStatus($data) {
        $result = $this->ekspedisiApi->bulkUpdateStatus($data);
        
        return [
            'success' => $result['success'],
            'updated_count' => $result['data']['updated_count'] ?? 0,
            'total_requested' => $result['data']['total_requested'] ?? 0,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal memperbarui status ekspedisi')
        ];
    }

    public function cleanupPoorPerformers($threshold) {
        $result = $this->ekspedisiApi->cleanupPoorPerformers($threshold);
        
        return [
            'success' => $result['success'],
            'deactivated_count' => $result['data']['deactivated_count'] ?? 0,
            'threshold_used' => $result['data']['threshold_used'] ?? $threshold,
            'error' => $result['error'] ?? ($result['data']['error'] ?? 'Gagal membersihkan ekspedisi dengan performa buruk')
        ];
    }
}