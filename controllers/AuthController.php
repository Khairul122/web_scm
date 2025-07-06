<?php
require_once 'models/AuthModel.php';

class AuthController {
    private $authModel;

    public function __construct() {
        $this->authModel = new AuthModel();
    }

    public function showLogin() {
        if ($this->authModel->isLoggedIn()) {
            header('Location: /web_scm/dashboard');
            exit;
        }

        $error = $_GET['error'] ?? '';
        $message = $_GET['message'] ?? '';
        
        // Debug mode - test connection
        $debug = $_GET['debug'] ?? '';
        if ($debug === 'test') {
            $connectionTest = $this->authModel->testConnection();
            echo "<h3>Connection Test:</h3>";
            echo "<pre>";
            print_r($connectionTest);
            echo "</pre>";
            echo "<hr>";
        }
        
        include VIEWS_PATH . 'auth/login.php';
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/login');
            exit;
        }

        $identifier = $_POST['identifier'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($identifier) || empty($password)) {
            header('Location: /web_scm/login?error=' . urlencode('Email/Phone dan Password harus diisi'));
            exit;
        }

        $result = $this->authModel->login($identifier, $password);

        if ($result['success']) {
            header('Location: /web_scm/dashboard');
            exit;
        } else {
            // Debug info jika login gagal
            $debugInfo = '';
            if (isset($result['debug_info'])) {
                $debugInfo = '&debug=' . urlencode(json_encode($result['debug_info']));
            }
            
            header('Location: /web_scm/login?error=' . urlencode($result['error']) . $debugInfo);
            exit;
        }
    }

    public function logout() {
        $this->authModel->logout();
        header('Location: /web_scm/login?message=' . urlencode('Berhasil logout'));
        exit;
    }

    public function profile() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        $user = $this->authModel->getCurrentUser();
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        include VIEWS_PATH . 'auth/profile.php';
    }

    public function updateProfile() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/profile');
            exit;
        }

        $data = [
            'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
            'alamat' => $_POST['alamat'] ?? ''
        ];

        if (isset($_POST['nama_toko']) && !empty($_POST['nama_toko'])) {
            $data['nama_toko'] = $_POST['nama_toko'];
        }

        $result = $this->authModel->updateProfile($data);

        if ($result['success']) {
            header('Location: /web_scm/profile?success=' . urlencode('Profile berhasil diperbarui'));
        } else {
            header('Location: /web_scm/profile?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function changePassword() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/profile');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            header('Location: /web_scm/profile?error=' . urlencode('Semua field password harus diisi'));
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            header('Location: /web_scm/profile?error=' . urlencode('Password baru dan konfirmasi tidak sama'));
            exit;
        }

        if (strlen($newPassword) < 6) {
            header('Location: /web_scm/profile?error=' . urlencode('Password baru minimal 6 karakter'));
            exit;
        }

        $result = $this->authModel->changePassword($currentPassword, $newPassword);

        if ($result['success']) {
            header('Location: /web_scm/profile?success=' . urlencode('Password berhasil diubah'));
        } else {
            header('Location: /web_scm/profile?error=' . urlencode($result['error']));
        }
        exit;
    }
}