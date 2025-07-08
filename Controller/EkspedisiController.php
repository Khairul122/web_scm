<?php
class EkspedisiController {
    private $rest;

    public function __construct($restClient) {
        $this->rest = $restClient;
    }

    public function index() {
        error_log('=== EKSPEDISI CONTROLLER DEBUG ===');
        
        $user = $this->getCurrentUser();
        
        error_log('Token exists: ' . ($user ? 'YES' : 'NO'));
        error_log('User data: ' . print_r($user ?? 'NOT SET', true));
        error_log('User role: ' . ($user['role'] ?? 'NO ROLE'));

        if (!$user) {
            error_log('No user token - redirecting to auth');
            Response::redirect('?controller=Auth&action=index');
        }

        if (strtolower($user['role']) !== 'admin') {
            error_log('User is not admin: ' . $user['role']);
            echo View::load('error/403', [
                'message' => 'Access denied. Admin role required.',
                'current_role' => $user['role']
            ]);
            return;
        }

        error_log('Admin access granted - loading ekspedisi index');
        
        $response = $this->rest->get('kurir');
        
        error_log('REST API Response: ' . print_r($response, true));
        
        echo View::load('admin/ekspedisi/index', [
            'user' => $user,
            'response' => $response
        ]);
    }

    public function create() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::redirect('?controller=Auth&action=index');
        }

        $codesResponse = $this->rest->get('kurir/available-codes');
        
        echo View::load('admin/ekspedisi/form', [
            'user' => $user,
            'mode' => 'create',
            'ekspedisi' => null,
            'availableCodes' => $codesResponse['data']['data'] ?? []
        ]);
    }

    public function store() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        if (Request::isPost()) {
            $data = [
                'kode' => Request::post('kode'),
                'nama' => Request::post('nama'),
                'status' => Request::post('status') ?? 'aktif'
            ];

            error_log('Store data: ' . print_r($data, true));
            
            $response = $this->rest->post('kurir', $data);
            
            error_log('Store response: ' . print_r($response, true));

            if ($response['success']) {
                Response::redirect('?controller=Ekspedisi&action=index&success=1');
            } else {
                $codesResponse = $this->rest->get('kurir/available-codes');
                echo View::load('admin/ekspedisi/form', [
                    'user' => $user,
                    'mode' => 'create',
                    'ekspedisi' => null,
                    'availableCodes' => $codesResponse['data']['data'] ?? [],
                    'error' => $response['data']['error'] ?? 'Failed to create ekspedisi',
                    'data' => $data,
                    'response' => $response
                ]);
            }
        }
    }

    public function edit() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::redirect('?controller=Auth&action=index');
        }

        $id = Request::get('id');
        if (!$id) {
            Response::redirect('?controller=Ekspedisi&action=index&error=1');
        }

        error_log('Edit ekspedisi ID: ' . $id);
        
        $response = $this->rest->get("kurir?id={$id}");
        $codesResponse = $this->rest->get('kurir/available-codes');
        
        error_log('Edit response: ' . print_r($response, true));

        if ($response['success']) {
            echo View::load('admin/ekspedisi/form', [
                'user' => $user,
                'mode' => 'edit',
                'ekspedisi' => $response['data']['data'],
                'availableCodes' => $codesResponse['data']['data'] ?? [],
                'response' => $response
            ]);
        } else {
            Response::redirect('?controller=Ekspedisi&action=index&error=1');
        }
    }

    public function update() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        $id = Request::get('id');
        if (!$id) {
            Response::redirect('?controller=Ekspedisi&action=index&error=1');
        }

        if (Request::isPost()) {
            $data = [
                'id' => $id,
                'kode' => Request::post('kode'),
                'nama' => Request::post('nama'),
                'status' => Request::post('status')
            ];

            error_log('Update data for ID ' . $id . ': ' . print_r($data, true));
            
            $response = $this->rest->put("kurir", $data);
            
            error_log('Update response: ' . print_r($response, true));

            if ($response['success']) {
                Response::redirect('?controller=Ekspedisi&action=index&success=2');
            } else {
                $ekspedisiResponse = $this->rest->get("kurir?id={$id}");
                $codesResponse = $this->rest->get('kurir/available-codes');
                echo View::load('admin/ekspedisi/form', [
                    'user' => $user,
                    'mode' => 'edit',
                    'ekspedisi' => $ekspedisiResponse['data']['data'],
                    'availableCodes' => $codesResponse['data']['data'] ?? [],
                    'error' => $response['data']['error'] ?? 'Failed to update ekspedisi',
                    'data' => $data,
                    'response' => $response
                ]);
            }
        }
    }

    public function delete() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        $id = Request::get('id');
        if (!$id) {
            Response::json(['error' => 'ID required'], 400);
        }

        error_log('Delete ekspedisi ID: ' . $id);
        
        $response = $this->rest->delete("kurir", ['id' => $id]);
        
        error_log('Delete response: ' . print_r($response, true));
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? ($response['success'] ? 'Deleted successfully' : 'Delete failed'),
            'debug' => $response
        ], $response['http_code']);
    }

    public function show() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::redirect('?controller=Auth&action=index');
        }

        $id = Request::get('id');
        if (!$id) {
            Response::redirect('?controller=Ekspedisi&action=index&error=1');
        }

        error_log('Show ekspedisi ID: ' . $id);
        
        $response = $this->rest->get("kurir?id={$id}");
        $statsResponse = $this->rest->get('kurir/stats');
        
        error_log('Show response: ' . print_r($response, true));

        if ($response['success']) {
            echo View::load('admin/ekspedisi/detail', [
                'user' => $user,
                'ekspedisi' => $response['data']['data'],
                'stats' => $statsResponse['data']['data'] ?? [],
                'response' => $response
            ]);
        } else {
            Response::redirect('?controller=Ekspedisi&action=index&error=1');
        }
    }

    public function stats() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::redirect('?controller=Auth&action=index');
        }

        $response = $this->rest->get('kurir/stats');
        
        echo View::load('admin/ekspedisi/stats', [
            'user' => $user,
            'stats' => $response['data']['data'] ?? [],
            'response' => $response
        ]);
    }

    public function import() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        $response = $this->rest->post('kurir/import', []);
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? 'Import completed',
            'data' => $response['data']
        ], $response['http_code']);
    }

    public function bulkUpdate() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        $input = Request::input();
        
        $response = $this->rest->post('kurir/bulk-update', $input);
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? 'Bulk update completed',
            'data' => $response['data']
        ], $response['http_code']);
    }

    private function getCurrentUser() {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

        if (!$authHeader && isset($_COOKIE['auth_token'])) {
            $authHeader = 'Bearer ' . $_COOKIE['auth_token'];
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return null;
        }

        $token = $matches[1];
        
        error_log('Token received: ' . substr($token, 0, 50) . '...');
        
        $decoded = json_decode(base64_decode($token), true);

        if (!$decoded) {
            error_log('Token decode failed');
            return null;
        }

        if ($decoded['exp'] < time()) {
            error_log('Token expired');
            return null;
        }

        error_log('Token valid for user: ' . $decoded['email']);
        
        return $decoded;
    }
}
?>