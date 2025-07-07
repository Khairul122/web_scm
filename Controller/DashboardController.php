<?php
class DashboardController {
    private $rest;

    public function __construct($restClient) {
        $this->rest = $restClient;
    }

    public function index() {
        $user = $this->getCurrentUser();

        if (!$user) {
            error_log('No valid token - redirecting to auth');
            Response::redirect('?controller=Auth&action=index');
        }

        $role = $user['role'];
        error_log('Dashboard index for role: ' . $role . ' (user: ' . $user['email'] . ')');

        switch ($role) {
            case 'admin':
                echo View::load('admin/dashboard/index', ['user' => $user]);
                break;
            case 'pengepul':
                echo View::load('pengepul/dashboard/index', ['user' => $user]);
                break;
            case 'roasting':
                echo View::load('roasting/dashboard/index', ['user' => $user]);
                break;
            case 'penjual':
                echo View::load('penjual/dashboard/index', ['user' => $user]);
                break;
            case 'pembeli':
                echo View::load('pembeli/dashboard/index', ['user' => $user]);
                break;
            default:
                error_log('Unknown role: ' . $role);
                echo "Role tidak dikenal: " . $role;
                break;
        }
    }

    public function getStats() {
        $user = $this->getCurrentUser();

        if (!$user) {
            error_log('getStats: No valid token');
            Response::json([
                'error' => 'Unauthorized',
                'message' => 'Valid Bearer token required',
                'debug' => [
                    'timestamp' => date('Y-m-d H:i:s'),
                    'endpoint' => 'getStats'
                ]
            ], 401);
        }

        error_log('Getting stats for user: ' . $user['email'] . ' (role: ' . $user['role'] . ')');
        
        $response = $this->rest->get('dashboard');
        
        error_log('Stats response: ' . print_r($response, true));
        
        Response::json([
            'success' => $response['success'],
            'data' => $response['data'],
            'debug' => [
                'user' => $user['email'],
                'role' => $user['role'],
                'api_response' => $response,
                'timestamp' => date('Y-m-d H:i:s')
            ]
        ], $response['http_code']);
    }

    public function admin() {
        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'admin') {
            error_log('Admin access denied for user: ' . ($user['email'] ?? 'NO TOKEN'));
            Response::redirect('?controller=Auth&action=index');
        }

        error_log('Admin dashboard access granted for: ' . $user['email']);
        echo View::load('admin/dashboard/index', ['user' => $user]);
    }

    public function pengepul() {
        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'pengepul') {
            error_log('Pengepul access denied for user: ' . ($user['email'] ?? 'NO TOKEN'));
            Response::redirect('?controller=Auth&action=index');
        }

        error_log('Pengepul dashboard access granted for: ' . $user['email']);
        echo View::load('pengepul/dashboard/index', ['user' => $user]);
    }

    public function roasting() {
        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'roasting') {
            error_log('Roasting access denied for user: ' . ($user['email'] ?? 'NO TOKEN'));
            Response::redirect('?controller=Auth&action=index');
        }

        error_log('Roasting dashboard access granted for: ' . $user['email']);
        echo View::load('roasting/dashboard/index', ['user' => $user]);
    }

    public function penjual() {
        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'penjual') {
            error_log('Penjual access denied for user: ' . ($user['email'] ?? 'NO TOKEN'));
            Response::redirect('?controller=Auth&action=index');
        }

        error_log('Penjual dashboard access granted for: ' . $user['email']);
        echo View::load('penjual/dashboard/index', ['user' => $user]);
    }

    public function pembeli() {
        $user = $this->getCurrentUser();

        if (!$user || $user['role'] !== 'pembeli') {
            error_log('Pembeli access denied for user: ' . ($user['email'] ?? 'NO TOKEN'));
            Response::redirect('?controller=Auth&action=index');
        }

        error_log('Pembeli dashboard access granted for: ' . $user['email']);
        echo View::load('pembeli/dashboard/index', ['user' => $user]);
    }

    private function getCurrentUser() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader && isset($_COOKIE['auth_token'])) {
            $authHeader = 'Bearer ' . $_COOKIE['auth_token'];
        }

        error_log('Dashboard auth header present: ' . ($authHeader ? 'YES' : 'NO'));

        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            error_log('No valid Bearer token in dashboard');
            return null;
        }

        $token = $matches[1];
        error_log('Dashboard token received: ' . substr($token, 0, 50) . '...');

        $decoded = json_decode(base64_decode($token), true);

        if (!$decoded) {
            error_log('Dashboard token decode failed');
            return null;
        }

        if ($decoded['exp'] < time()) {
            error_log('Dashboard token expired at: ' . date('Y-m-d H:i:s', $decoded['exp']));
            return null;
        }

        error_log('Dashboard token valid for user: ' . $decoded['email'] . ' (role: ' . $decoded['role'] . ')');
        return $decoded;
    }
}
?>