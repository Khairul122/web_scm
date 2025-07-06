<?php
require_once __DIR__ . '/../config.php';

class AuthApi {
    private $baseUrl;

    public function __construct() {
        $this->baseUrl = API_BASE_URL . '/auth';
    }

    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->baseUrl . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: SCM-WebPanel/1.0'
        ];

        $options = [
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $headers),
                'ignore_errors' => true,
                'timeout' => 30,
                'follow_location' => 1,
                'max_redirects' => 3
            ]
        ];

        if ($data && in_array($method, ['POST', 'PUT'])) {
            $options['http']['content'] = json_encode($data);
        }

        $context = stream_context_create($options);
        
        // Test connection dulu
        $testUrl = str_replace('/auth', '/health', $this->baseUrl);
        $testResponse = @file_get_contents($testUrl, false, $context);
        
        if ($testResponse === false) {
            // Coba alternatif port
            $alternativeUrls = [
                'http://localhost:3000/api/health',
                'http://localhost:8000/api/health',
                'http://localhost/backend_scm/api/health',
                'http://localhost/api/health'
            ];
            
            $workingUrl = null;
            foreach ($alternativeUrls as $altUrl) {
                if (@file_get_contents($altUrl, false, $context) !== false) {
                    $workingUrl = str_replace('/health', '', $altUrl);
                    break;
                }
            }
            
            if ($workingUrl) {
                $this->baseUrl = $workingUrl . '/auth';
                $url = $this->baseUrl . $endpoint;
            } else {
                return [
                    'success' => false,
                    'error' => 'Backend server tidak dapat dijangkau. Pastikan backend berjalan di salah satu port: 3000, 8000, 8080',
                    'debug_info' => [
                        'tried_url' => $url,
                        'alternatives_tried' => $alternativeUrls
                    ]
                ];
            }
        }
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            $error = error_get_last();
            return [
                'success' => false,
                'error' => 'Gagal terhubung ke backend: ' . ($error['message'] ?? 'Unknown error'),
                'debug_info' => [
                    'url' => $url,
                    'method' => $method,
                    'error_details' => $error
                ]
            ];
        }

        $decodedResponse = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'success' => false,
                'error' => 'Response tidak valid dari server: ' . json_last_error_msg(),
                'debug_info' => [
                    'raw_response' => substr($response, 0, 500)
                ]
            ];
        }

        return [
            'success' => !isset($decodedResponse['error']),
            'data' => $decodedResponse,
            'error' => $decodedResponse['error'] ?? null
        ];
    }

    public function testConnection() {
        $testUrl = str_replace('/auth', '/health', $this->baseUrl);
        $response = @file_get_contents($testUrl);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            return [
                'success' => true,
                'data' => $data,
                'url' => $testUrl
            ];
        }
        
        return [
            'success' => false,
            'error' => 'Cannot connect to backend',
            'url' => $testUrl
        ];
    }

    public function login($identifier, $password) {
        // Test koneksi dulu
        $connectionTest = $this->testConnection();
        if (!$connectionTest['success']) {
            return [
                'success' => false,
                'error' => 'Backend server tidak tersedia. ' . $connectionTest['error'],
                'debug_info' => $connectionTest
            ];
        }

        // Debug: coba dengan password plain text untuk testing
        $response = $this->makeRequest('/login', 'POST', [
            'identifier' => $identifier,
            'password' => $password
        ]);

        // Debug info
        if (!$response['success']) {
            $response['debug_info'] = [
                'sent_data' => [
                    'identifier' => $identifier,
                    'password' => '***hidden***'
                ],
                'backend_response' => $response['data'] ?? 'No response data'
            ];
        }

        if ($response['success'] && isset($response['data']['user'])) {
            $user = $response['data']['user'];
            
            if ($user['role'] === 'pembeli') {
                return [
                    'success' => false,
                    'error' => 'Pembeli tidak dapat mengakses web panel',
                    'data' => null
                ];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_data'] = $user;
            $_SESSION['role'] = $user['role'];
            $_SESSION['login_time'] = time();
            
            if (isset($response['data']['token'])) {
                $_SESSION['api_token'] = $response['data']['token'];
            }
        }

        return $response;
    }

    public function register($data) {
        return $this->makeRequest('/register', 'POST', $data);
    }

    public function getProfile() {
        $token = $_SESSION['api_token'] ?? null;
        return $this->makeRequest('/profile', 'GET', null, $token);
    }

    public function updateProfile($data) {
        $token = $_SESSION['api_token'] ?? null;
        return $this->makeRequest('/profile', 'PUT', $data, $token);
    }

    public function changePassword($currentPassword, $newPassword) {
        $token = $_SESSION['api_token'] ?? null;
        return $this->makeRequest('/change-password', 'POST', [
            'current_password' => $currentPassword,
            'new_password' => $newPassword
        ], $token);
    }

    public function logout() {
        session_unset();
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logged out successfully'
        ];
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']) && isset($_SESSION['role']);
    }

    public function getCurrentUser() {
        return $_SESSION['user_data'] ?? null;
    }

    public function getCurrentRole() {
        return $_SESSION['role'] ?? null;
    }

    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: /web_scm/login');
            exit;
        }
    }

    public function requireWebAccess() {
        $this->requireLogin();
        
        $role = $this->getCurrentRole();
        if ($role === 'pembeli') {
            http_response_code(403);
            echo "<h1>403 - Access Denied</h1>";
            echo "<p>Pembeli tidak dapat mengakses web panel.</p>";
            echo "<a href='/web_scm/logout'>Logout</a>";
            exit;
        }
    }

    public function hasRole($role) {
        return $this->getCurrentRole() === $role;
    }

    public function isAdmin() {
        return in_array($this->getCurrentRole(), ['admin', 'superadmin']);
    }

    public function getValidRoles() {
        return ['pembeli', 'pengepul', 'roasting', 'penjual'];
    }
}