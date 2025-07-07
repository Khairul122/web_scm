<?php
require_once 'models/EkspedisiModel.php';
require_once 'models/AuthModel.php';

class EkspedisiController {
    private $ekspedisiModel;
    private $authModel;

    public function __construct() {
        $this->ekspedisiModel = new EkspedisiModel();
        $this->authModel = new AuthModel();
    }

    public function index() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/dashboard?error=' . urlencode('Access denied'));
            exit;
        }

        $status = $_GET['status'] ?? null;
        $kurirList = $this->ekspedisiModel->getAllKurir($status);
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        $data = [
            'kurir' => $kurirList,
            'error' => $error,
            'success' => $success,
            'current_status' => $status
        ];

        include VIEWS_PATH . 'admin/ekspedisi/index.php';
    }

    public function create() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $availableCodes = $this->ekspedisiModel->getAvailableKurirCodes();

        $data = [
            'action' => 'create',
            'kurir' => null,
            'available_codes' => $availableCodes,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/ekspedisi/form.php';
    }

    public function store() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/ekspedisi/create');
            exit;
        }

        $kode = $_POST['kode'] ?? '';
        $nama = $_POST['nama'] ?? '';

        if (empty($kode) || empty($nama)) {
            header('Location: /web_scm/ekspedisi/create?error=' . urlencode('Kode dan nama kurir harus diisi'));
            exit;
        }

        $data = [
            'kode' => $kode,
            'nama' => $nama
        ];

        $result = $this->ekspedisiModel->createKurir($data);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Kurir berhasil ditambahkan'));
        } else {
            header('Location: /web_scm/ekspedisi/create?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function edit() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $id = $this->getIdFromUrl();
        
        if (!$id) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('ID kurir tidak valid'));
            exit;
        }

        $kurir = $this->ekspedisiModel->getKurirById($id);
        
        if (!$kurir) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Kurir tidak ditemukan'));
            exit;
        }

        $availableCodes = $this->ekspedisiModel->getAvailableKurirCodes();

        $data = [
            'action' => 'edit',
            'kurir' => $kurir,
            'available_codes' => $availableCodes,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/ekspedisi/form.php';
    }

    public function update() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/ekspedisi');
            exit;
        }

        $id = $_POST['id'] ?? '';
        $kode = $_POST['kode'] ?? '';
        $nama = $_POST['nama'] ?? '';

        if (empty($id) || empty($kode) || empty($nama)) {
            header('Location: /web_scm/ekspedisi/edit/' . $id . '?error=' . urlencode('Data tidak lengkap'));
            exit;
        }

        $data = [
            'kode' => $kode,
            'nama' => $nama
        ];

        $result = $this->ekspedisiModel->updateKurir($id, $data);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Kurir berhasil diperbarui'));
        } else {
            header('Location: /web_scm/ekspedisi/edit/' . $id . '?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function delete() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $id = $this->getIdFromUrl();
        
        if (!$id) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('ID kurir tidak valid'));
            exit;
        }

        $result = $this->ekspedisiModel->deleteKurir($id);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Kurir berhasil dihapus'));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function toggleStatus() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $id = $this->getIdFromUrl();
        $status = $_GET['status'] ?? '';
        
        if (!$id || !in_array($status, ['aktif', 'nonaktif'])) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Parameter tidak valid'));
            exit;
        }

        $result = $this->ekspedisiModel->updateKurirStatus($id, $status);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Status kurir berhasil diubah'));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function import() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $result = $this->ekspedisiModel->importKurirFromApi();

        if ($result['success']) {
            $message = 'Import berhasil. ' . $result['imported_count'] . ' kurir diimpor dari API';
            header('Location: /web_scm/ekspedisi?success=' . urlencode($message));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    private function getIdFromUrl() {
        $url = $_GET['url'] ?? '';
        $urlParts = explode('/', $url);
        
        $ekspedisiIndex = array_search('ekspedisi', $urlParts);
        if ($ekspedisiIndex !== false) {
            if (isset($urlParts[$ekspedisiIndex + 2]) && is_numeric($urlParts[$ekspedisiIndex + 2])) {
                return $urlParts[$ekspedisiIndex + 2];
            }
            if (isset($urlParts[$ekspedisiIndex + 1]) && is_numeric($urlParts[$ekspedisiIndex + 1])) {
                return $urlParts[$ekspedisiIndex + 1];
            }
        }
        
        for ($i = count($urlParts) - 1; $i >= 0; $i--) {
            if (is_numeric($urlParts[$i])) {
                return $urlParts[$i];
            }
        }
        
        return null;
    }
}