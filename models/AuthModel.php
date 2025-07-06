<?php
require_once 'api/AuthApi.php';

class AuthModel {
    private $authApi;

    public function __construct() {
        $this->authApi = new AuthApi();
    }

    public function login($identifier, $password) {
        $result = $this->authApi->login($identifier, $password);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Login berhasil',
                'user' => $result['data']['user'] ?? null
            ];
        } else {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Login gagal'
            ];
        }
    }

    public function logout() {
        return $this->authApi->logout();
    }

    public function getProfile() {
        $result = $this->authApi->getProfile();
        
        if ($result['success']) {
            return [
                'success' => true,
                'user' => $result['data']['user'] ?? null
            ];
        } else {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Gagal mengambil profile'
            ];
        }
    }

    public function updateProfile($data) {
        $result = $this->authApi->updateProfile($data);
        
        if ($result['success']) {
            if (isset($result['data']['user'])) {
                $_SESSION['user_data'] = $result['data']['user'];
            }
            
            return [
                'success' => true,
                'message' => 'Profile berhasil diperbarui'
            ];
        } else {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Gagal memperbarui profile'
            ];
        }
    }

    public function changePassword($currentPassword, $newPassword) {
        $result = $this->authApi->changePassword($currentPassword, $newPassword);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Password berhasil diubah'
            ];
        } else {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Gagal mengubah password'
            ];
        }
    }

    public function register($data) {
        $result = $this->authApi->register($data);
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Registrasi berhasil',
                'user' => $result['data']['user'] ?? null
            ];
        } else {
            return [
                'success' => false,
                'error' => $result['error'] ?? 'Registrasi gagal'
            ];
        }
    }

    public function testConnection() {
        return $this->authApi->testConnection();
    }

    public function isLoggedIn() {
        return $this->authApi->isLoggedIn();
    }

    public function getCurrentUser() {
        return $this->authApi->getCurrentUser();
    }

    public function getCurrentRole() {
        return $this->authApi->getCurrentRole();
    }

    public function requireLogin() {
        return $this->authApi->requireLogin();
    }

    public function requireWebAccess() {
        return $this->authApi->requireWebAccess();
    }

    public function hasRole($role) {
        return $this->authApi->hasRole($role);
    }

    public function isAdmin() {
        return $this->authApi->isAdmin();
    }

    public function getValidRoles() {
        return $this->authApi->getValidRoles();
    }
}