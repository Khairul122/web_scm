<?php
require_once 'config.php';

class DynamicRouter {
    private $publicRoutes = ['login', 'register'];
    private $specialRoutes = ['logout', 'profile', 'change-password'];
    
    public function route() {
        $url = $_GET['url'] ?? '';
        $url = rtrim($url, '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        
        $urlParts = explode('/', $url);
        $page = $urlParts[0] ?? 'dashboard';
        $action = $urlParts[1] ?? 'index';
        $params = array_slice($urlParts, 2);
        
        if ($this->handleSpecialRoutes($page, $action, $params)) {
            return;
        }
        
        if (!in_array($page, $this->publicRoutes)) {
            $this->checkAuth();
        }
        
        $controllerName = $this->getControllerName($page);
        $methodName = $this->getMethodName($action, $page);
        
        $this->loadController($controllerName, $methodName, $params);
    }
    
    private function handleSpecialRoutes($page, $action, $params) {
        switch ($page) {
            case '':
            case 'dashboard':
                $this->checkAuth();
                $this->loadController('DashboardController', 'index');
                return true;
                
            case 'login':
                require_once 'api/AuthApi.php';
                $authApi = new AuthApi();
                if ($authApi->isLoggedIn()) {
                    header('Location: /web_scm/dashboard');
                    exit;
                }
                
                $method = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'login' : 'showLogin';
                $this->loadController('AuthController', $method);
                return true;
                
            case 'logout':
                $this->loadController('AuthController', 'logout');
                return true;
                
            case 'profile':
                $this->checkAuth();
                $method = $_SERVER['REQUEST_METHOD'] === 'POST' ? 'updateProfile' : 'profile';
                $this->loadController('AuthController', $method);
                return true;
                
            case 'change-password':
                $this->checkAuth();
                $this->loadController('AuthController', 'changePassword');
                return true;
                
            default:
                return false;
        }
    }
    
    private function getControllerName($page) {
        $variations = [
            ucfirst($page) . 'Controller',
            ucfirst(rtrim($page, 's')) . 'Controller',
            ucfirst($page . 's') . 'Controller',
            ucfirst(str_replace('-', '', $page)) . 'Controller',
            ucfirst(str_replace('_', '', $page)) . 'Controller'
        ];
        
        foreach ($variations as $controllerName) {
            if ($this->controllerExists($controllerName)) {
                return $controllerName;
            }
        }
        
        return 'DashboardController';
    }
    
    private function getMethodName($action, $page) {
        if ($this->isResourceAction($action)) {
            return $action;
        }
        
        if (empty($action) || $action === 'index') {
            return $this->getMethodByHttpVerb();
        }
        
        if (is_numeric($action)) {
            return $this->getMethodByHttpVerb();
        }
        
        return $action;
    }
    
    private function isResourceAction($action) {
        $resourceActions = [
            'index', 'create', 'store', 'show', 'edit', 'update', 'delete', 'destroy',
            'view', 'form', 'list', 'detail', 'add', 'save', 'remove'
        ];
        
        return in_array($action, $resourceActions);
    }
    
    private function getMethodByHttpVerb() {
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                return 'store';
            case 'PUT':
                return 'update';
            case 'DELETE':
                return 'delete';
            default:
                return 'index';
        }
    }
    
    private function controllerExists($controllerName) {
        $controllerFile = CONTROLLERS_PATH . $controllerName . '.php';
        return file_exists($controllerFile);
    }
    
    private function loadController($controller, $method = 'index', $params = []) {
        $controllerFile = CONTROLLERS_PATH . $controller . '.php';
        
        if (!file_exists($controllerFile)) {
            $this->loadStaticFile($params);
            return;
        }
        
        require_once $controllerFile;
        $controllerClass = $controller;
        
        if (!class_exists($controllerClass)) {
            $this->show404();
            return;
        }
        
        $controllerInstance = new $controllerClass();
        
        if (!method_exists($controllerInstance, $method)) {
            if (method_exists($controllerInstance, 'index')) {
                $method = 'index';
            } else {
                $this->show404();
                return;
            }
        }
        
        call_user_func_array([$controllerInstance, $method], $params);
    }
    
    private function loadStaticFile($pathParts) {
        $url = $_GET['url'] ?? '';
        $segments = explode('/', trim($url, '/'));
        
        $possiblePaths = [
            VIEWS_PATH . implode('/', $segments) . '.php',
            VIEWS_PATH . implode('/', $segments) . '/index.php',
            PUBLIC_PATH . implode('/', $segments) . '.php',
            PUBLIC_PATH . implode('/', $segments) . '/index.php'
        ];
        
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                    $this->checkAuth();
                    include $path;
                    return;
                } else {
                    $this->serveFile($path);
                    return;
                }
            }
        }
        
        $this->show404();
    }
    
    private function serveFile($filePath) {
        $mimeType = $this->getMimeType($filePath);
        
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($filePath));
        
        readfile($filePath);
        exit;
    }
    
    private function getMimeType($filePath) {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        $mimeTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'json' => 'application/json',
            'xml' => 'application/xml'
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
    
    private function checkAuth() {
        require_once 'api/AuthApi.php';
        $authApi = new AuthApi();
        
        if (!$authApi->isLoggedIn()) {
            header('Location: /web_scm/login');
            exit;
        }
        
        $authApi->requireWebAccess();
    }
    
    private function show404() {
        http_response_code(404);
        if (file_exists(VIEWS_PATH . 'errors/404.php')) {
            include VIEWS_PATH . 'errors/404.php';
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>Halaman yang Anda cari tidak ditemukan.</p>";
            echo "<a href='/web_scm/'>Kembali ke Dashboard</a>";
        }
        exit;
    }
}

$router = new DynamicRouter();
$router->route();