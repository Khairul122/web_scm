<?php
require_once 'api/DashboardApi.php';

class DashboardModel {
    private $dashboardApi;

    public function __construct() {
        $this->dashboardApi = new DashboardApi();
    }

    public function getDashboardData($role) {
        switch ($role) {
            case 'admin':
                return $this->dashboardApi->getAdminDashboard();
            case 'pengepul':
                return $this->dashboardApi->getPengepulDashboard();
            case 'roasting':
                return $this->dashboardApi->getRoastingDashboard();
            case 'penjual':
                return $this->dashboardApi->getPenjualDashboard();
            default:
                return [
                    'success' => false,
                    'error' => 'Role tidak valid',
                    'data' => []
                ];
        }
    }
}