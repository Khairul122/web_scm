<?php
class KategoriController {
    private $rest;

    public function __construct($restClient) {
        $this->rest = $restClient;
    }

    public function index() {
        error_log('=== KATEGORI CONTROLLER DEBUG ===');
        
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

        error_log('Admin access granted - loading kategori index');
        
        $response = $this->rest->get('kategori');
        
        error_log('REST API Response: ' . print_r($response, true));
        
        echo View::load('admin/kategori/index', [
            'user' => $user,
            'response' => $response
        ]);
    }

    public function create() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::redirect('?controller=Auth&action=index');
        }

        echo View::load('admin/kategori/form', [
            'user' => $user,
            'mode' => 'create',
            'kategori' => null
        ]);
    }

    public function store() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        if (Request::isPost()) {
            $data = [
                'nama_kategori' => Request::post('nama_kategori'),
                'deskripsi' => Request::post('deskripsi')
            ];

            error_log('Store data: ' . print_r($data, true));
            
            $response = $this->rest->post('kategori', $data);
            
            error_log('Store response: ' . print_r($response, true));

            if ($response['success']) {
                Response::redirect('?controller=Kategori&action=index&success=1');
            } else {
                echo View::load('admin/kategori/form', [
                    'user' => $user,
                    'mode' => 'create',
                    'kategori' => null,
                    'error' => $response['data']['error'] ?? 'Failed to create kategori',
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
            Response::redirect('?controller=Kategori&action=index&error=1');
        }

        error_log('Edit kategori ID: ' . $id);
        
        $response = $this->rest->get("kategori/{$id}");
        
        error_log('Edit response: ' . print_r($response, true));

        if ($response['success']) {
            echo View::load('admin/kategori/form', [
                'user' => $user,
                'mode' => 'edit',
                'kategori' => $response['data']['data'],
                'response' => $response
            ]);
        } else {
            Response::redirect('?controller=Kategori&action=index&error=1');
        }
    }

    public function update() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        $id = Request::get('id');
        if (!$id) {
            Response::redirect('?controller=Kategori&action=index&error=1');
        }

        if (Request::isPost()) {
            $data = [
                'nama_kategori' => Request::post('nama_kategori'),
                'deskripsi' => Request::post('deskripsi')
            ];

            error_log('Update data for ID ' . $id . ': ' . print_r($data, true));
            
            $response = $this->rest->put("kategori/{$id}", $data);
            
            error_log('Update response: ' . print_r($response, true));

            if ($response['success']) {
                Response::redirect('?controller=Kategori&action=index&success=2');
            } else {
                $kategoriResponse = $this->rest->get("kategori/{$id}");
                echo View::load('admin/kategori/form', [
                    'user' => $user,
                    'mode' => 'edit',
                    'kategori' => $kategoriResponse['data']['data'],
                    'error' => $response['data']['error'] ?? 'Failed to update kategori',
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

        error_log('Delete kategori ID: ' . $id);
        
        $response = $this->rest->delete("kategori/{$id}");
        
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
            Response::redirect('?controller=Kategori&action=index&error=1');
        }

        error_log('Show kategori ID: ' . $id);
        
        $response = $this->rest->get("kategori/{$id}");
        
        error_log('Show response: ' . print_r($response, true));

        if ($response['success']) {
            echo View::load('admin/kategori/detail', [
                'user' => $user,
                'kategori' => $response['data']['data'],
                'products' => $response['data']['products'] ?? [],
                'response' => $response
            ]);
        } else {
            Response::redirect('?controller=Kategori&action=index&error=1');
        }
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