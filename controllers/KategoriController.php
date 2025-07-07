<?php
require_once 'models/KategoriModel.php';
require_once 'models/AuthModel.php';

class KategoriController {
    private $kategoriModel;
    private $authModel;

    public function __construct() {
        $this->kategoriModel = new KategoriModel();
        $this->authModel = new AuthModel();
    }

    public function index() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/dashboard?error=' . urlencode('Access denied'));
            exit;
        }

        $kategoriList = $this->kategoriModel->getAllKategori();
        $error = $_GET['error'] ?? '';
        $success = $_GET['success'] ?? '';

        $data = [
            'kategori' => $kategoriList,
            'error' => $error,
            'success' => $success
        ];

        include VIEWS_PATH . 'admin/kategori/index.php';
    }

    public function create() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/kategori?error=' . urlencode('Access denied'));
            exit;
        }

        $data = [
            'action' => 'create',
            'kategori' => null,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/kategori/form.php';
    }

    public function edit() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/kategori?error=' . urlencode('Access denied'));
            exit;
        }

        $id = $this->getIdFromUrl();
        
        if (!$id) {
            header('Location: /web_scm/kategori?error=' . urlencode('ID kategori tidak valid'));
            exit;
        }

        $kategori = $this->kategoriModel->getKategoriById($id);
        
        if (!$kategori) {
            header('Location: /web_scm/kategori?error=' . urlencode('Kategori tidak ditemukan'));
            exit;
        }

        $data = [
            'action' => 'edit',
            'kategori' => $kategori,
            'error' => $_GET['error'] ?? '',
            'success' => $_GET['success'] ?? ''
        ];

        include VIEWS_PATH . 'admin/kategori/form.php';
    }

    public function store() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/kategori?error=' . urlencode('Access denied'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/kategori/create');
            exit;
        }

        $namaKategori = $_POST['nama_kategori'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($namaKategori)) {
            header('Location: /web_scm/kategori/create?error=' . urlencode('Nama kategori harus diisi'));
            exit;
        }

        $data = [
            'nama_kategori' => $namaKategori,
            'deskripsi' => $deskripsi
        ];

        $result = $this->kategoriModel->createKategori($data);

        if ($result['success']) {
            header('Location: /web_scm/kategori?success=' . urlencode('Kategori berhasil ditambahkan'));
        } else {
            header('Location: /web_scm/kategori/create?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function update() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/kategori?error=' . urlencode('Access denied'));
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /web_scm/kategori');
            exit;
        }

        $id = $_POST['id'] ?? '';
        $namaKategori = $_POST['nama_kategori'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';

        if (empty($id) || empty($namaKategori)) {
            header('Location: /web_scm/kategori/edit/' . $id . '?error=' . urlencode('Data tidak lengkap'));
            exit;
        }

        $data = [
            'nama_kategori' => $namaKategori,
            'deskripsi' => $deskripsi
        ];

        $result = $this->kategoriModel->updateKategori($id, $data);

        if ($result['success']) {
            header('Location: /web_scm/kategori?success=' . urlencode('Kategori berhasil diperbarui'));
        } else {
            header('Location: /web_scm/kategori/edit/' . $id . '?error=' . urlencode($result['error']));
        }
        exit;
    }

    public function delete() {
        $this->authModel->requireLogin();
        $this->authModel->requireWebAccess();

        if (!$this->authModel->hasRole('admin')) {
            header('Location: /web_scm/kategori?error=' . urlencode('Access denied'));
            exit;
        }

        $id = $this->getIdFromUrl();
        
        if (!$id) {
            header('Location: /web_scm/kategori?error=' . urlencode('ID kategori tidak valid'));
            exit;
        }

        $result = $this->kategoriModel->deleteKategori($id);

        if ($result['success']) {
            header('Location: /web_scm/kategori?success=' . urlencode('Kategori berhasil dihapus'));
        } else {
            header('Location: /web_scm/kategori?error=' . urlencode($result['error']));
        }
        exit;
    }

    private function getIdFromUrl() {
        $url = $_GET['url'] ?? '';
        $urlParts = explode('/', $url);
        
        $kategoriIndex = array_search('kategori', $urlParts);
        if ($kategoriIndex !== false && isset($urlParts[$kategoriIndex + 2])) {
            return $urlParts[$kategoriIndex + 2];
        }
        
        return null;
    }
}