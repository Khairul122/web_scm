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

        $status = $_GET['status'] ?? '';
        
        if ($status === 'active') {
            $ekspedisiList = $this->ekspedisiModel->getActiveKurir();
        } else {
            $ekspedisiList = $this->ekspedisiModel->getAllKurir();
        }

        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        $data = [
            'ekspedisi' => $ekspedisiList,
            'error' => $error,
            'success' => $success,
            'status_filter' => $status
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

        // Get available courier codes
        $availableCodes = $this->ekspedisiModel->getAvailableKurirCodes();

        $data = [
            'action' => 'create',
            'ekspedisi' => null,
            'available_codes' => $availableCodes,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/ekspedisi/form.php';
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
            header('Location: /web_scm/ekspedisi?error=' . urlencode('ID ekspedisi tidak valid'));
            exit;
        }

        $ekspedisi = $this->ekspedisiModel->getKurirById($id);
        
        if (!$ekspedisi) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Ekspedisi tidak ditemukan'));
            exit;
        }

        // Get available courier codes
        $availableCodes = $this->ekspedisiModel->getAvailableKurirCodes();

        $data = [
            'action' => 'edit',
            'ekspedisi' => $ekspedisi,
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
        $status = $_POST['status'] ?? 'aktif';

        if (empty($kode) || empty($nama)) {
            header('Location: /web_scm/ekspedisi/create?error=' . urlencode('Kode dan nama ekspedisi harus diisi'));
            exit;
        }

        $data = [
            'kode' => $kode,
            'nama' => $nama,
            'status' => $status
        ];

        $result = $this->ekspedisiModel->createKurir($data);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Ekspedisi berhasil ditambahkan'));
        } else {
            header('Location: /web_scm/ekspedisi/create?error=' . urlencode($result['error']));
        }
        exit;
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
        $status = $_POST['status'] ?? 'aktif';

        if (empty($id) || empty($kode) || empty($nama)) {
            header('Location: /web_scm/ekspedisi/edit/' . $id . '?error=' . urlencode('Data tidak lengkap'));
            exit;
        }

        $data = [
            'kode' => $kode,
            'nama' => $nama,
            'status' => $status
        ];

        $result = $this->ekspedisiModel->updateKurir($id, $data);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Ekspedisi berhasil diperbarui'));
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
            header('Location: /web_scm/ekspedisi?error=' . urlencode('ID ekspedisi tidak valid'));
            exit;
        }

        $result = $this->ekspedisiModel->deleteKurir($id);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Ekspedisi berhasil dihapus'));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function updateStatus() {
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
        $status = $_POST['status'] ?? '';

        if (empty($id) || empty($status)) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Data tidak lengkap'));
            exit;
        }

        if (!in_array($status, ['aktif', 'nonaktif'])) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Status tidak valid'));
            exit;
        }

        $result = $this->ekspedisiModel->updateKurirStatus($id, $status);

        if ($result['success']) {
            header('Location: /web_scm/ekspedisi?success=' . urlencode('Status ekspedisi berhasil diperbarui'));
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
            $message = 'Berhasil mengimpor ' . $result['imported_count'] . ' ekspedisi dari Raja Ongkir API';
            header('Location: /web_scm/ekspedisi?success=' . urlencode($message));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function analytics() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $analytics = $this->ekspedisiModel->getKurirAnalytics();
        $stats = $this->ekspedisiModel->getKurirStats();
        $performance = $this->ekspedisiModel->getKurirPerformance();

        $data = [
            'analytics' => $analytics,
            'stats' => $stats,
            'performance' => $performance,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/ekspedisi/analytics.php';
    }

    public function search() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Query pencarian tidak boleh kosong'));
            exit;
        }

        $ekspedisiList = $this->ekspedisiModel->searchKurir($query);

        $data = [
            'ekspedisi' => $ekspedisiList,
            'search_query' => $query,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/ekspedisi/index.php';
    }

    public function bulkUpdateStatus() {
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

        $ids = $_POST['ids'] ?? [];
        $status = $_POST['status'] ?? '';

        if (empty($ids) || empty($status)) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Data tidak lengkap'));
            exit;
        }

        if (!in_array($status, ['aktif', 'nonaktif'])) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Status tidak valid'));
            exit;
        }

        $data = [
            'ids' => $ids,
            'status' => $status
        ];

        $result = $this->ekspedisiModel->bulkUpdateStatus($data);

        if ($result['success']) {
            $message = 'Berhasil memperbarui status ' . $result['updated_count'] . ' dari ' . $result['total_requested'] . ' ekspedisi';
            header('Location: /web_scm/ekspedisi?success=' . urlencode($message));
        } else {
            header('Location: /web_scm/ekspedisi?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function cleanupPoorPerformers() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/ekspedisi?error=' . urlencode('Access denied'));
            exit;
        }

        $threshold = $_POST['threshold'] ?? 50;

        $result = $this->ekspedisiModel->cleanupPoorPerformers($threshold);

        if ($result['success']) {
            $message = 'Berhasil menonaktifkan ' . $result['deactivated_count'] . ' ekspedisi dengan performa buruk (< ' . $threshold . '%)';
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
        if ($ekspedisiIndex !== false && isset($urlParts[$ekspedisiIndex + 2])) {
            return $urlParts[$ekspedisiIndex + 2];
        }
        
        return null;
    }
}