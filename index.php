<?php
date_default_timezone_set('Asia/Jakarta');

define('BASE_PATH', __DIR__);
define('CONTROLLER_PATH', BASE_PATH . '/Controller/');
define('API_PATH', BASE_PATH . '/Api/');
define('TEMPLATE_PATH', BASE_PATH . '/template/');
define('ASSET_PATH', BASE_PATH . '/assets/');
define('VIEW_PATH', BASE_PATH . '/views/');
define('API_BASE_URL', 'http://localhost/backend_scm/api/');
define('JWT_SECRET', 'your-secret-key-change-in-production');

class TokenManager {
    public static function getCurrentUser() {
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

    public static function createToken($userData) {
        $payload = [
            'id' => $userData['id'],
            'nama_lengkap' => $userData['nama_lengkap'],
            'email' => $userData['email'],
            'no_telepon' => $userData['no_telepon'] ?? '',
            'alamat' => $userData['alamat'] ?? '',
            'role' => $userData['role'],
            'nama_toko' => $userData['nama_toko'] ?? '',
            'iat' => time(),
            'exp' => time() + (24 * 60 * 60)
        ];

        return base64_encode(json_encode($payload));
    }

    public static function setTokenCookie($token) {
        setcookie('auth_token', $token, time() + (24 * 60 * 60), '/', '', false, true);
    }

    public static function clearTokenCookie() {
        setcookie('auth_token', '', time() - 3600, '/', '', false, true);
    }

    public static function isAuthenticated() {
        return self::getCurrentUser() !== null;
    }

    public static function hasRole($role) {
        $user = self::getCurrentUser();
        return $user && $user['role'] === $role;
    }
}

class RestClient {
    private $baseUrl;
    private $defaultHeaders;
    
    public function __construct($baseUrl = '', $headers = []) {
        $this->baseUrl = rtrim($baseUrl, '/');
        
        $authHeaders = [];
        $user = TokenManager::getCurrentUser();
        if ($user) {
            $token = isset($_COOKIE['auth_token']) ? $_COOKIE['auth_token'] : '';
            if ($token) {
                $authHeaders[] = 'Authorization: Bearer ' . $token;
            }
        }
        
        $this->defaultHeaders = array_merge([
            'Content-Type: application/json',
            'Accept: application/json'
        ], $authHeaders, $headers);
    }
    
    public function get($endpoint, $headers = []) {
        return $this->request('GET', $endpoint, null, $headers);
    }
    
    public function post($endpoint, $data = null, $headers = []) {
        return $this->request('POST', $endpoint, $data, $headers);
    }
    
    public function put($endpoint, $data = null, $headers = []) {
        return $this->request('PUT', $endpoint, $data, $headers);
    }
    
    public function delete($endpoint, $headers = []) {
        return $this->request('DELETE', $endpoint, null, $headers);
    }
    
    public function patch($endpoint, $data = null, $headers = []) {
        return $this->request('PATCH', $endpoint, $data, $headers);
    }
    
    private function request($method, $endpoint, $data = null, $headers = []) {
        $url = $this->baseUrl ? $this->baseUrl . '/' . ltrim($endpoint, '/') : $endpoint;
        
        error_log("RestClient {$method} request to: {$url}");
        if ($data) {
            error_log("Request data: " . json_encode($data));
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => array_merge($this->defaultHeaders, $headers),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        
        if ($data && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
        }
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        
        error_log("RestClient response code: {$httpCode}");
        error_log("RestClient response: " . substr($response, 0, 500));
        
        if ($error) {
            error_log("RestClient error: {$error}");
            return ['error' => $error, 'http_code' => 0, 'success' => false];
        }
        
        $decodedResponse = json_decode($response, true);
        
        return [
            'data' => $decodedResponse ?: $response,
            'http_code' => $httpCode,
            'success' => $httpCode >= 200 && $httpCode < 300,
            'raw_response' => $response
        ];
    }
}

class Router {
    private $controller;
    private $action;
    private $restClient;
    
    public function __construct() {
        $this->restClient = new RestClient(API_BASE_URL);
        $this->parseRequest();
        $this->logRequest();
    }
    
    private function logRequest() {
        $user = TokenManager::getCurrentUser();
        error_log('=== REQUEST DEBUG ===');
        error_log('Controller: ' . $this->controller);
        error_log('Action: ' . $this->action);
        error_log('User: ' . ($user ? $user['email'] . ' (' . $user['role'] . ')' : 'NOT AUTHENTICATED'));
        error_log('Token cookie exists: ' . (isset($_COOKIE['auth_token']) ? 'YES' : 'NO'));
    }
    
    private function parseRequest() {
        $this->controller = $this->sanitize($_GET['controller'] ?? 'Dashboard');
        $this->action = $this->sanitize($_GET['action'] ?? 'index');
    }
    
    private function sanitize($input) {
        return preg_replace('/[^a-zA-Z0-9_]/', '', $input);
    }
    
    public function dispatch() {
        if ($this->isApiRequest()) {
            return $this->handleApiRequest();
        }
        
        if ($this->isAssetRequest()) {
            return $this->handleAssetRequest();
        }
        
        return $this->handleControllerRequest();
    }
    
    private function isApiRequest() {
        return $this->controller === 'Api' || isset($_GET['api']);
    }
    
    private function isAssetRequest() {
        return $this->controller === 'Assets' || strpos($_SERVER['REQUEST_URI'], '/assets/') !== false;
    }
    
    private function handleApiRequest() {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        
        $endpoint = $_GET['api'] ?? $this->action;
        $apiFile = API_PATH . ucfirst($endpoint) . 'Api.php';
        
        error_log('API request for endpoint: ' . $endpoint);
        error_log('API file: ' . $apiFile);
        
        if (file_exists($apiFile)) {
            $GLOBALS['restClient'] = $this->restClient;
            include $apiFile;
        } else {
            $this->jsonResponse(['error' => 'API endpoint tidak ditemukan'], 404);
        }
    }
    
    private function handleAssetRequest() {
        $assetPath = str_replace('/assets/', '', $_SERVER['REQUEST_URI']);
        $fullAssetPath = ASSET_PATH . $assetPath;
        
        if (file_exists($fullAssetPath)) {
            $mimeType = mime_content_type($fullAssetPath);
            header('Content-Type: ' . $mimeType);
            header('Cache-Control: public, max-age=31536000');
            readfile($fullAssetPath);
        } else {
            $this->notFound('Asset tidak ditemukan');
        }
    }
    
    private function handleControllerRequest() {
        $controllerFile = CONTROLLER_PATH . $this->controller . 'Controller.php';
        
        error_log('Loading controller: ' . $controllerFile);
        
        if (!file_exists($controllerFile)) {
            return $this->notFound('Controller tidak ditemukan: ' . $this->controller);
        }
        
        require_once $controllerFile;
        
        $controllerClass = ucfirst($this->controller) . 'Controller';
        
        if (!class_exists($controllerClass)) {
            return $this->notFound('Controller class tidak ditemukan: ' . $controllerClass);
        }
        
        $controllerObj = new $controllerClass($this->restClient);
        
        if (!method_exists($controllerObj, $this->action)) {
            return $this->notFound('Action tidak ditemukan: ' . $this->action);
        }
        
        error_log('Executing: ' . $controllerClass . '::' . $this->action);
        $controllerObj->{$this->action}();
    }
    
    private function notFound($message) {
        error_log('404 Error: ' . $message);
        http_response_code(404);
        echo "404 - $message";
    }
    
    private function jsonResponse($data, $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

class View {
    public static function load($viewName, $data = []) {
        $viewFile = VIEW_PATH . $viewName . '.php';
        
        if (file_exists($viewFile)) {
            extract($data);
            ob_start();
            include $viewFile;
            return ob_get_clean();
        }
        
        return "View tidak ditemukan: $viewName";
    }
    
    public static function template($templateName, $data = []) {
        $templateFile = TEMPLATE_PATH . $templateName . '.php';
        
        if (file_exists($templateFile)) {
            extract($data);
            ob_start();
            include $templateFile;
            return ob_get_clean();
        }
        
        return "Template tidak ditemukan: $templateName";
    }
    
    public static function asset($path) {
        return '/assets/' . ltrim($path, '/');
    }
    
    public static function debugToken() {
        $user = TokenManager::getCurrentUser();
        echo "<script>";
        echo "console.log('=== TOKEN DEBUG ===');";
        echo "console.log('User Data:', " . json_encode($user) . ");";
        echo "console.log('Token Cookie:', document.cookie.includes('auth_token'));";
        if ($user) {
            echo "console.log('Token expires:', new Date(" . ($user['exp'] * 1000) . "));";
            echo "console.log('Minutes until expiry:', Math.floor((" . $user['exp'] . " * 1000 - Date.now()) / 1000 / 60));";
        } else {
            echo "console.log('No valid token found');";
        }
        echo "</script>";
    }
}

class Response {
    public static function json($data, $httpCode = 200) {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    public static function redirect($url) {
        header("Location: $url");
        exit;
    }
}

class Request {
    public static function get($key = null, $default = null) {
        return $key ? ($_GET[$key] ?? $default) : $_GET;
    }
    
    public static function post($key = null, $default = null) {
        return $key ? ($_POST[$key] ?? $default) : $_POST;
    }
    
    public static function input() {
        return json_decode(file_get_contents('php://input'), true) ?: [];
    }
    
    public static function method() {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public static function isPost() {
        return self::method() === 'POST';
    }
    
    public static function isGet() {
        return self::method() === 'GET';
    }
}

$router = new Router();
$router->dispatch();
?>