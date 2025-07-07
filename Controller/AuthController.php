<?php
class AuthController {
    private $rest;

    public function __construct($restClient) {
        $this->rest = $restClient;
    }

    public function index() {
        $this->login();
    }

    public function login() {
        if (Request::isPost()) {
            $data = [
                'identifier' => Request::post('identifier'),
                'password' => Request::post('password')
            ];

            if (empty($data['identifier']) || empty($data['password'])) {
                echo View::load('auth/login', [
                    'error' => 'Email/telepon dan password harus diisi'
                ]);
                return;
            }

            $response = $this->rest->post('auth/login', $data);

            if ($response['success'] && isset($response['data']['user'])) {
                $user = $response['data']['user'];
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'email' => $user['email'],
                    'no_telepon' => $user['no_telepon'],
                    'alamat' => $user['alamat'],
                    'role' => $user['role'],
                    'nama_toko' => $user['nama_toko'] ?? null
                ];
                Response::redirect('?controller=Dashboard&action=index');
            } else {
                echo View::load('auth/login', [
                    'error' => $response['data']['error'] ?? 'Login failed'
                ]);
            }
        } else {
            echo View::load('auth/login');
        }
    }

    public function register() {
        if (Request::isPost()) {
            $data = [
                'nama_lengkap' => Request::post('nama_lengkap'),
                'email' => Request::post('email'),
                'no_telepon' => Request::post('no_telepon'),
                'alamat' => Request::post('alamat'),
                'password' => Request::post('password'),
                'role' => Request::post('role')
            ];

            $response = $this->rest->post('auth/register', $data);

            if ($response['success']) {
                Response::redirect('?controller=Auth&action=login');
            } else {
                echo View::load('auth/register', [
                    'error' => $response['data']['error'] ?? 'Registration failed',
                    'data' => $data
                ]);
            }
        } else {
            echo View::load('auth/register');
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
        Response::redirect('?controller=Auth&action=index');
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            Response::redirect('?controller=Auth&action=index');
        }

        echo View::load('auth/profile', [
            'user' => $_SESSION['user']
        ]);
    }
}
?>