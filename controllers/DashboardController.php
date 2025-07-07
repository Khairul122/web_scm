<?php
require_once 'models/DashboardModel.php';
require_once 'models/AuthModel.php';

class DashboardController {
    private $dashboardModel;
    private $authModel;

    public function __construct() {
        $this->dashboardModel = new DashboardModel();
        $this->authModel = new AuthModel();
    }

    public function index() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $role = $this->authModel->getCurrentRole();

        $dashboardData = $this->dashboardModel->getDashboardData($role);

        $data = [
            'user' => $user,
            'role' => $role,
            'stats' => $dashboardData['data']['stats'] ?? [],
            'recent_orders' => $dashboardData['data']['recent_orders'] ?? [],
            'error' => $dashboardData['error'] ?? null
        ];

        switch ($role) {
            case 'admin':
                include VIEWS_PATH . 'admin/dashboard/index.php';
                break;
            case 'pengepul':
                include VIEWS_PATH . 'pengepul/dashboard/index.php';
                break;
            case 'roasting':
                include VIEWS_PATH . 'roasting/dashboard/index.php';
                break;
            case 'penjual':
                include VIEWS_PATH . 'penjual/dashboard/index.php';
                break;
            default:
                header('Location: /web_scm/login?error=' . urlencode('Role tidak valid'));
                exit;
        }
    }
}