<?php
require_once 'config.php';

function loadController($controller, $method = 'index') {
    $controllerFile = CONTROLLERS_PATH . $controller . '.php';
    
    if (file_exists($controllerFile)) {
        require_once $controllerFile;
        $controllerClass = $controller;
        
        if (class_exists($controllerClass)) {
            $controllerInstance = new $controllerClass();
            
            if (method_exists($controllerInstance, $method)) {
                $controllerInstance->$method();
            } else {
                show404();
            }
        } else {
            show404();
        }
    } else {
        show404();
    }
}

function show404() {
    http_response_code(404);
    echo "<h1>404 - Page Not Found</h1>";
    echo "<p>Halaman yang Anda cari tidak ditemukan.</p>";
    echo "<a href='/web_scm/'>Kembali ke Dashboard</a>";
    exit;
}

function checkAuth() {
    require_once 'api/AuthApi.php';
    $authApi = new AuthApi();
    
    if (!$authApi->isLoggedIn()) {
        header('Location: /web_scm/login');
        exit;
    }
    
    $authApi->requireWebAccess();
}

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);

$urlParts = explode('/', $url);
$page = $urlParts[0] ?? 'dashboard';

switch ($page) {
    case '':
    case 'dashboard':
        checkAuth();
        loadController('DashboardController', 'index');
        break;
        
    case 'login':
        require_once 'api/AuthApi.php';
        $authApi = new AuthApi();
        if ($authApi->isLoggedIn()) {
            header('Location: /web_scm/dashboard');
            exit;
        }
        
        $action = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'login' : 'showLogin';
        loadController('AuthController', $action);
        break;
        
    case 'logout':
        loadController('AuthController', 'logout');
        break;
        
    case 'profile':
        checkAuth();
        $action = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'updateProfile' : 'profile';
        loadController('AuthController', $action);
        break;
        
    case 'change-password':
        checkAuth();
        loadController('AuthController', 'changePassword');
        break;
        
    case 'users':
        checkAuth();
        $action = $urlParts[1] ?? 'index';
        loadController('UserController', $action);
        break;
        
    default:
        show404();
        break;
}