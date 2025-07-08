<?php
class UserController {
    private $rest;

    public function __construct($restClient) {
        $this->rest = $restClient;
    }

    public function index() {
        $user = $this->requireAdmin();
        $response = $this->rest->get('users');
        
        echo View::load('admin/users/index', [
            'user' => $user,
            'response' => $response
        ]);
    }

    public function create() {
        $user = $this->requireAdmin();

        echo View::load('admin/users/form', [
            'user' => $user,
            'mode' => 'create',
            'userData' => null
        ]);
    }

    public function store() {
        $user = $this->requireAdminApi();

        if (Request::isPost()) {
            $data = [
                'nama_lengkap' => Request::post('nama_lengkap'),
                'email' => Request::post('email'),
                'no_telepon' => Request::post('no_telepon'),
                'alamat' => Request::post('alamat'),
                'password' => Request::post('password'),
                'role' => Request::post('role'),
                'nama_toko' => Request::post('nama_toko'),
                'status' => Request::post('status') ?? 'aktif'
            ];
            
            $response = $this->rest->post('users', $data);

            if ($response['success']) {
                Response::redirect('?controller=User&action=index&success=1');
            } else {
                echo View::load('admin/users/form', [
                    'user' => $user,
                    'mode' => 'create',
                    'userData' => null,
                    'error' => $response['data']['error'] ?? 'Failed to create user',
                    'data' => $data
                ]);
            }
        }
    }

    public function edit() {
        $user = $this->requireAdmin();
        $id = $this->getRequiredParam('id');
        
        $response = $this->rest->get("users/{$id}");

        if ($response['success']) {
            echo View::load('admin/users/form', [
                'user' => $user,
                'mode' => 'edit',
                'userData' => $response['data']['data']
            ]);
        } else {
            Response::redirect('?controller=User&action=index&error=1');
        }
    }

    public function update() {
        $user = $this->requireAdminApi();
        $id = $this->getRequiredParam('id');

        if (Request::isPost()) {
            $data = [
                'nama_lengkap' => Request::post('nama_lengkap'),
                'email' => Request::post('email'),
                'no_telepon' => Request::post('no_telepon'),
                'alamat' => Request::post('alamat'),
                'role' => Request::post('role'),
                'nama_toko' => Request::post('nama_toko'),
                'status' => Request::post('status')
            ];

            if (Request::post('password')) {
                $data['password'] = Request::post('password');
            }
            
            $response = $this->rest->put("users/{$id}", $data);

            if ($response['success']) {
                Response::redirect('?controller=User&action=index&success=2');
            } else {
                $userResponse = $this->rest->get("users/{$id}");
                echo View::load('admin/users/form', [
                    'user' => $user,
                    'mode' => 'edit',
                    'userData' => $userResponse['data']['data'],
                    'error' => $response['data']['error'] ?? 'Failed to update user',
                    'data' => $data
                ]);
            }
        }
    }

    public function delete() {
        $this->requireAdminApi();
        $id = $this->getRequiredParam('id');
        
        $response = $this->rest->delete("users/{$id}");
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? ($response['success'] ? 'Deleted successfully' : 'Delete failed')
        ], $response['http_code']);
    }

    public function show() {
        $user = $this->requireAdmin();
        $id = $this->getRequiredParam('id');
        
        $response = $this->rest->get("users/{$id}");

        if ($response['success']) {
            echo View::load('admin/users/detail', [
                'user' => $user,
                'userData' => $response['data']['data']
            ]);
        } else {
            Response::redirect('?controller=User&action=index&error=1');
        }
    }

    public function stats() {
        $user = $this->requireAdmin();
        $response = $this->rest->get('users/stats');
        
        echo View::load('admin/users/stats', [
            'user' => $user,
            'stats' => $response['data']['data'] ?? []
        ]);
    }

    public function updateStatus() {
        $this->requireAdminApi();
        $id = $this->getRequiredParam('id');
        $input = Request::input();
        
        $response = $this->rest->patch("users/{$id}/status", $input);
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? ($response['success'] ? 'Status updated successfully' : 'Status update failed')
        ], $response['http_code']);
    }

    public function resetPassword() {
        $this->requireAdminApi();
        $id = $this->getRequiredParam('id');
        $input = Request::input();
        
        $response = $this->rest->patch("users/{$id}/password", $input);
        
        Response::json([
            'success' => $response['success'],
            'message' => $response['data']['message'] ?? ($response['success'] ? 'Password reset successfully' : 'Password reset failed')
        ], $response['http_code']);
    }

    public function search() {
        $this->requireAdminApi();
        $query = $this->getRequiredParam('q');
        
        $response = $this->rest->get("users/search?q=" . urlencode($query));
        
        Response::json([
            'success' => $response['success'],
            'data' => $response['data']['data'] ?? []
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
        $decoded = json_decode(base64_decode($token), true);

        if (!$decoded || $decoded['exp'] < time()) {
            return null;
        }

        return $decoded;
    }

    private function requireAdmin() {
        $user = $this->getCurrentUser();
        
        if (!$user) {
            Response::redirect('?controller=Auth&action=index');
        }

        if (strtolower($user['role']) !== 'admin') {
            echo View::load('error/403', [
                'message' => 'Access denied. Admin role required.'
            ]);
            exit;
        }

        return $user;
    }

    private function requireAdminApi() {
        $user = $this->getCurrentUser();
        
        if (!$user || strtolower($user['role']) !== 'admin') {
            Response::json(['error' => 'Admin access required'], 403);
        }

        return $user;
    }

    private function getRequiredParam($param) {
        $value = Request::get($param);
        if (!$value) {
            if (headers_sent()) {
                Response::redirect('?controller=User&action=index&error=1');
            } else {
                Response::json(['error' => ucfirst($param) . ' required'], 400);
            }
        }
        return $value;
    }
}